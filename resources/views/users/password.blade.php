@extends('adminlte::page')

@section('title', 'Ganti Password')

@section('content_header')
    <h1>Ganti Password - {{ $user->name }}</h1>
@stop

@section('content')
    <form action="{{ route('users.password.update', $user) }}" method="POST" id="passwordForm">
        @csrf
        @method('PATCH')

        <div class="form-group">
            <label>Password Baru</label>
            <div class="input-group">
                <input type="password" name="password" id="password" class="form-control" minlength="8" required>
                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password')">
                    <i class="fas fa-eye"></i>
                </button>
            </div>
            @error('password')
                <span class="text-danger">{{ $message }}</span>
            @enderror
            <small id="passwordHelp" class="text-muted">Minimal 8 karakter</small>
        </div>

        <div class="form-group mt-3">
            <label>Konfirmasi Password</label>
            <div class="input-group">
                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password_confirmation')">
                    <i class="fas fa-eye"></i>
                </button>
            </div>
            <small id="matchMessage" class="text-danger d-none">Password tidak cocok</small>
        </div>

        <button type="submit" class="btn btn-primary mt-3">Simpan</button>
        <a href="{{ route('users.index') }}" class="btn btn-secondary mt-3">Kembali</a>
    </form>
@stop

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

document.getElementById('passwordForm').addEventListener('submit', function (e) {
    const pass = document.getElementById('password').value;
    const confirm = document.getElementById('password_confirmation').value;
    if (pass.length < 8 || pass !== confirm) {
        e.preventDefault();
        alert('Pastikan password minimal 8 karakter dan cocok dengan konfirmasi.');
    }
});
</script>
@stop
