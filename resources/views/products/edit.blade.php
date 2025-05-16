@extends('adminlte::page')

@section('title', 'Edit Barang')

@section('content_header')
    <h1>Edit Barang</h1>
@stop

@section('content')
    <form action="{{ route('products.update', $product->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label>Nama Barang</label>
            <input type="text" name="name" class="form-control" value="{{ $product->name }}" required>
        </div>

        <div class="form-group mt-3">
            <label>Kode SKU</label>
            <input type="text" name="sku" class="form-control" value="{{ $product->sku }}" required>
        </div>

        <div class="form-group mt-3">
            <label>Stok</label>
            <input type="number" name="stock" class="form-control" value="{{ $product->stock }}" required min="0">
        </div>

        <button type="submit" class="btn btn-primary mt-3">Update</button>
        <a href="{{ route('products.index') }}" class="btn btn-secondary mt-3">Kembali</a>
    </form>
@stop
