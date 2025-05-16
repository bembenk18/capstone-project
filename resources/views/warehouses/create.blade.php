@extends('adminlte::page')

@section('title', 'Tambah Gudang')

@section('content_header')
    <h1>Tambah Gudang</h1>
@stop

@section('content')
    <form action="{{ route('warehouses.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="name">Nama Gudang</label>
            <input type="text" name="name" class="form-control" required placeholder="Gudang A">
        </div>

        <div class="form-group mt-3">
            <label for="location">Lokasi</label>
            <input type="text" name="location" class="form-control" placeholder="Jakarta, Surabaya...">
        </div>

        <button type="submit" class="btn btn-primary mt-3">Simpan</button>
        <a href="{{ route('warehouses.index') }}" class="btn btn-secondary mt-3">Kembali</a>
    </form>
@stop
