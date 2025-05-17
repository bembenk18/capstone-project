@extends('adminlte::page')

@section('title', 'Tambah Transaksi | ' . \App\Helpers\SettingHelper::companyName())

@section('content_header')
<h1>Transaksi Barang</h1>
@stop

@section('content')
@if ($errors->any())
<div class="alert alert-danger">
    <ul class="mb-0">
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<form action="{{ route('transactions.store') }}" method="POST">
    @csrf

    <div class="form-group">
        <label>Produk</label>
        <select name="product_id" class="form-control" required>
            <option value="">-- Pilih Produk --</option>
            @foreach($products as $product)
            <option value="{{ $product->id }}">{{ $product->name }} (Stok: {{ $product->stock }})</option>
            @endforeach
        </select>
    </div>

    <div class="form-group mt-3">
        <label>Jenis Transaksi</label>
        <select name="type" class="form-control" required>
            <option value="in">Barang Masuk</option>
            <option value="out">Barang Keluar</option>
        </select>
    </div>

    <div class="form-group">
    <label>Jumlah</label>
    <input type="number" name="quantity" class="form-control" min="1" value="{{ old('quantity') }}">
    @error('quantity')
        <small class="text-danger">{{ $message }}</small>
    @enderror
</div>

    <div class="form-group mt-3">
        <label>Gudang</label>
        <select name="warehouse_id" class="form-control" required>
            <option value="">-- Pilih Gudang --</option>
            @foreach($warehouses as $g)
            <option value="{{ $g->id }}">{{ $g->name }}</option>
            @endforeach
        </select>

        <div class="form-group mt-3">
            <label>Catatan (opsional)</label>
            <textarea name="note" class="form-control"></textarea>
        </div>

        <button type="submit" class="btn btn-primary mt-3">Simpan</button>
        <a href="{{ route('transactions.index') }}" class="btn btn-secondary mt-3">Kembali</a>
</form>

</div>

@stop