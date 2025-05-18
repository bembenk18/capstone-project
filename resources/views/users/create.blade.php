@extends('adminlte::page')

@section('title', 'Tambah User')

@section('content_header')
<h1>Tambah User</h1>
@stop

@section('content')
<form action="{{ route('users.store') }}" method="POST">
    @csrf
    <div class="form-group">
        <label>Nama</label>
        <input type="text" name="name" class="form-control" required value="{{ old('name') }}">
    </div>

    <div class="form-group mt-3">
        <label>Email</label>
        <input type="email" name="email" class="form-control" required value="{{ old('email') }}">
    </div>

    <div class="form-group mt-3">
        <label>Password</label>
        <input type="password" name="password" class="form-control" required>
    </div>

    <div class="form-group mt-3">
        <label>Konfirmasi Password</label>
        <input type="password" name="password_confirmation" class="form-control" required>
    </div>

    <div class="form-group mt-3">
        <label>Role</label>
        <select name="role" class="form-control" required>
            <option value="user" {{ old('role') == 'user' ? 'selected' : '' }}>User</option>
            <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
        </select>
    </div>

    <button type="submit" class="btn btn-primary mt-3">Simpan</button>
    <a href="{{ route('users.index') }}" class="btn btn-secondary mt-3">Kembali</a>
</form>
@stop
