@extends('adminlte::page')


@section('title', 'Gudang | ' . \App\Helpers\SettingHelper::companyName())


@section('content_header')
    <h1>Daftar Gudang</h1>
@stop

@section('content')
    <a href="{{ route('warehouses.create') }}" class="btn btn-primary mb-3">+ Tambah Gudang</a>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Nama</th>
                <th>Lokasi</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($warehouses as $w)
                <tr>
                    <td>{{ $w->name }}</td>
                    <td>{{ $w->location }}</td>
                    <td>
                        <a href="{{ route('warehouses.edit', $w->id) }}" class="btn btn-warning btn-sm">Edit</a>
                        <form action="{{ route('warehouses.destroy', $w->id) }}" method="POST" style="display:inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Yakin?')">Hapus</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@stop
