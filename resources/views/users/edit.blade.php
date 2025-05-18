@extends('adminlte::page')

@section('title', 'Edit User')

@section('content_header')
<h1>Edit User</h1>
@stop

@section('content')
<form action="{{ route('users.update', $user->id) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="form-group">
        <label>Nama</label>
        <input type="text" name="name" class="form-control" required value="{{ old('name', $user->name) }}">
    </div>

    <div class="form-group mt-3">
        <label>Email</label>
        <input type="email" name="email" class="form-control" required value="{{ old('email', $user->email) }}">
    </div>

    <div class="form-group mt-3">
        <label>Role</label>
        <select name="role" class="form-control" required>
            <option value="user" {{ $user->role == 'user' ? 'selected' : '' }}>User</option>
            <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
        </select>
    </div>

    <button type="submit" class="btn btn-primary mt-3">Update</button>
    <a href="{{ route('users.index') }}" class="btn btn-secondary mt-3">Kembali</a>
</form>
@stop
