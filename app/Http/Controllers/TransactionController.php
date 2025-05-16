<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Warehouse;
use App\Models\Transaction;
use Illuminate\Http\Request;

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
            'product_id'   => 'required|exists:products,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'type'         => 'required|in:in,out',
            'quantity'     => 'required|integer|min:1',
            'note'         => 'nullable|string',
        ]);

        // Simpan transaksi
        $transaction = Transaction::create([
            'product_id'   => $request->product_id,
            'warehouse_id' => $request->warehouse_id,
            'type'         => $request->type,
            'quantity'     => $request->quantity,
            'note'         => $request->note,
        ]);

        // Update stok produk
        $product = Product::find($request->product_id);
        if ($request->type === 'in') {
            $product->increment('stock', $request->quantity);
        } else {
            $product->decrement('stock', $request->quantity);
        }

        return redirect()->route('transactions.index')
                         ->with('success', 'Transaksi berhasil ditambahkan');
    }
}
