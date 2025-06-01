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
    <div class="form-group mt-3">
    <label>Password Baru <small class="text-muted">(kosongkan jika tidak ingin mengubah)</small></label>
    <div class="input-group">
        <input type="password" name="password" id="password" class="form-control" minlength="8">
        <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password')">
            <i class="fas fa-eye"></i>
        </button>
    </div>
    @error('password')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>

<div class="form-group mt-3">
    <label>Konfirmasi Password</label>
    <div class="input-group">
        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control">
        <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password_confirmation')">
            <i class="fas fa-eye"></i>
        </button>
    </div>
    <small id="matchMessage" class="text-danger d-none">Password tidak cocok</small>
</div>

@section('js')
<script>
function togglePassword(id) {
    const input = document.getElementById(id);
    input.type = input.type === 'password' ? 'text' : 'password';
}

document.getElementById('password_confirmation').addEventListener('input', function () {
    const pass = document.getElementById('password').value;
    const confirm = this.value;
    const message = document.getElementById('matchMessage');
    if (confirm !== pass) {
        message.classList.remove('d-none');
    } else {
        message.classList.add('d-none');
    }
});
</script>
@stop


    <button type="submit" class="btn btn-primary mt-3">Update</button>
    <a href="{{ route('users.index') }}" class="btn btn-secondary mt-3">Kembali</a>
</form>
@stop
