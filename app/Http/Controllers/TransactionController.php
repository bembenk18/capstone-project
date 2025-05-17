<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Warehouse;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Helpers\Telegram;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::all();
        $warehouses = Warehouse::all();

        $query = Transaction::with(['product', 'warehouse'])->latest();

        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        if ($request->filled('warehouse_id')) {
            $query->where('warehouse_id', $request->warehouse_id);
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
        }

        if ($request->has('type') && in_array($request->type, ['in', 'out'])) {
            $query->where('type', $request->type);
        }

        $transactions = $query->get();

        return view('transactions.index', compact('transactions', 'products', 'warehouses'));
    }

    public function create()
    {
        $products = Product::all();
        $warehouses = Warehouse::all();

        return view('transactions.create', compact('products', 'warehouses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id'    => 'required|exists:products,id',
            'type'          => 'required|in:in,out',
            'quantity'      => 'required|integer|min:1',
            'note'          => 'nullable|string',
            'warehouse_id'  => 'required|exists:warehouses,id',
        ]);

        $productId = $request->product_id;
        $warehouseId = $request->warehouse_id;
        $quantity = $request->quantity;
        $type = $request->type;

        $pivot = DB::table('product_warehouse')
            ->where('product_id', $productId)
            ->where('warehouse_id', $warehouseId)
            ->first();

        if (!$pivot) {
            DB::table('product_warehouse')->insert([
                'product_id' => $productId,
                'warehouse_id' => $warehouseId,
                'stock' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $pivotStock = 0;
        } else {
            $pivotStock = $pivot->stock;
        }

        if ($type === 'out' && $quantity > $pivotStock) {
            return back()->withInput()->withErrors(['quantity' => 'Stok di gudang ini tidak mencukupi.']);
        }

        // Simpan transaksi
        Transaction::create($request->all());

        $newStock = $type === 'in' ? $pivotStock + $quantity : $pivotStock - $quantity;

        DB::table('product_warehouse')
            ->where('product_id', $productId)
            ->where('warehouse_id', $warehouseId)
            ->update([
                'stock' => $newStock,
                'updated_at' => now()
            ]);

        // Kirim notifikasi Telegram
        $product = Product::find($productId);
        $warehouse = Warehouse::find($warehouseId);

        $message = "<b>📦 Transaksi Baru</b>\n"
                 . "Produk: <b>{$product->name}</b>\n"
                 . "Jenis: <b>" . ($type === 'in' ? 'Masuk' : 'Keluar') . "</b>\n"
                 . "Jumlah: <b>{$quantity}</b>\n"
                 . "Gudang: <b>{$warehouse->name}</b>\n"
                 . "Waktu: " . now()->format('d/m/Y H:i');

        Telegram::send($message);

        // Cek stok setelah transaksi
        $minimum = $product->minimum_stock ?? 0;
        if ($newStock < $minimum) {
            $lowStockMessage = "⚠️ Stok menipis untuk <b>{$product->name}</b> di gudang <b>{$warehouse->name}</b>.\n"
                             . "Sisa: <b>{$newStock}</b>, Minimum: <b>{$minimum}</b>";
            Telegram::send($lowStockMessage);
        }

        return redirect()->route('transactions.index')->with('success', 'Transaksi berhasil disimpan.');
    }
}
