@extends('adminlte::page')

@section('title', 'Edit Gudang | ' . \App\Helpers\SettingHelper::companyName())


@section('content_header')
    <h1>Edit Gudang</h1>
@stop

@section('content')
    <form action="{{ route('warehouses.update', $warehouse->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="name">Nama Gudang</label>
            <input type="text" name="name" class="form-control" value="{{ $warehouse->name }}" required>
        </div>

        <div class="form-group mt-3">
            <label for="location">Lokasi</label>
            <input type="text" name="location" class="form-control" value="{{ $warehouse->location }}">
        </div>

        <button type="submit" class="btn btn-primary mt-3">Update</button>
        <a href="{{ route('warehouses.index') }}" class="btn btn-secondary mt-3">Kembali</a>
    </form>
@stop
