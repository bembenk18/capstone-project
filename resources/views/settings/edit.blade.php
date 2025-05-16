@extends('adminlte::page')

@section('title', 'Pengaturan')

@section('content_header')
    <h1>Pengaturan Perusahaan</h1>
@stop

@section('content')
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="form-group">
            <label>Nama Perusahaan</label>
            <input type="text" name="company_name" class="form-control" value="{{ $setting->company_name }}" required>
        </div>

        <div class="form-group mt-3">
            <label>Logo</label>
            <input type="file" name="logo" class="form-control">
            @if($setting->logo)
                <img src="{{ asset('storage/'.$setting->logo) }}" class="mt-2" height="80">
            @endif
        </div>

        <button type="submit" class="btn btn-primary mt-3">Simpan</button>
    </form>
@stop
