<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('warehouses')->get();
        return view('products.index', compact('products'));
    }

    public function create()
    {
        $warehouses = Warehouse::all();
        return view('products.create', compact('warehouses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'         => 'required|string|max:255',
            'sku'          => 'required|string|max:255|unique:products',
            'stock'        => 'required|integer|min:0',
            'warehouse_id' => 'required|exists:warehouses,id',
            'image'        => 'nullable|image|max:2048',
        ]);

        $data = $request->only(['name', 'sku', 'stock']);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        // Simpan produk
        $product = Product::create($data);

        // Tambahkan ke warehouse via pivot table
        $product->warehouses()->attach($request->warehouse_id, [
            'stock' => $request->stock
        ]);

        return redirect()->route('products.index')->with('success', 'Barang berhasil ditambahkan');
    }

    public function edit(Product $product)
    {
        $warehouses = Warehouse::all();
        $product->load('warehouses');
        return view('products.edit', compact('product', 'warehouses'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name'         => 'required|string|max:255',
            'sku'          => 'required|string|max:255|unique:products,sku,' . $product->id,
            'stock'        => 'required|integer|min:0',
            'warehouse_id' => 'required|exists:warehouses,id',
            'image'        => 'nullable|image|max:2048',
        ]);

        $data = $request->only(['name', 'sku']);

        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($data);

        // Sink atau attach ulang stok ke gudang
        $product->warehouses()->syncWithoutDetaching([
            $request->warehouse_id => ['stock' => $request->stock]
        ]);

        return redirect()->route('products.index')->with('success', 'Barang berhasil diperbarui');
    }

    public function destroy(Product $product)
    {
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        // Detach relasi dengan gudang dulu
        $product->warehouses()->detach();

        $product->delete();

        return redirect()->route('products.index')->with('success', 'Barang berhasil dihapus');
    }
}
