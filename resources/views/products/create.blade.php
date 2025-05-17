@extends('adminlte::page')

@section('title', 'Tambah Barang | ' . \App\Helpers\SettingHelper::companyName())

@section('content_header')
    <h1>Tambah Barang</h1>
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

    <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="form-group">
            <label>Nama Barang</label>
            <input type="text" name="name" class="form-control" required>
        </div>

        <div class="form-group mt-3">
            <label>Kode SKU</label>
            <input type="text" name="sku" class="form-control" required>
        </div>

        <div class="form-group mt-3">
            <label>Stok Awal</label>
            <input type="number" name="stock" class="form-control" required value="0" min="0">
        </div>

        <div class="form-group mt-3">
            <label>Gudang</label>
            <select name="warehouse_id" class="form-control" required>
                <option value="">-- Pilih Gudang --</option>
                @foreach($warehouses as $g)
                    <option value="{{ $g->id }}">{{ $g->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group mt-3">
            <label>Foto Produk</label>
            <input type="file" name="image" class="form-control">
        </div>

        <button type="submit" class="btn btn-primary mt-3">Simpan</button>
        <a href="{{ route('products.index') }}" class="btn btn-secondary mt-3">Kembali</a>
    </form>
@stop
