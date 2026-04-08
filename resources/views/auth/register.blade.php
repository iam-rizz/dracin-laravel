@extends('layouts.auth')
@section('title', 'Daftar — DramaCina')
@section('content')
<div class="auth-card">
    <div class="auth-logo">DramaCina</div>
    <h2 style="text-align:center;margin-bottom:var(--gap-xl);font-size:1.2rem;color:var(--text-secondary);">Buat akun baru</h2>

    @if ($errors->any())
        <div class="alert alert-error">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('register') }}">
        @csrf
        <div class="form-group">
            <label for="name" class="form-label">Nama</label>
            <input id="name" type="text" name="name" class="form-input" value="{{ old('name') }}" required autofocus placeholder="Nama lengkap">
        </div>
        <div class="form-group">
            <label for="email" class="form-label">Email</label>
            <input id="email" type="email" name="email" class="form-input" value="{{ old('email') }}" required placeholder="email@contoh.com">
        </div>
        <div class="form-group">
            <label for="password" class="form-label">Password</label>
            <input id="password" type="password" name="password" class="form-input" required placeholder="Min. 8 karakter">
        </div>
        <div class="form-group">
            <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
            <input id="password_confirmation" type="password" name="password_confirmation" class="form-input" required placeholder="Ulangi password">
        </div>
        <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;">Daftar</button>
    </form>

    <p style="text-align:center;margin-top:var(--gap-lg);font-size:0.875rem;color:var(--text-muted);">
        Sudah punya akun?
        <a href="{{ route('login') }}" style="color:var(--gold);">Masuk</a>
    </p>
    <p style="text-align:center;margin-top:var(--gap-sm);font-size:0.8rem;">
        <a href="{{ route('home') }}" style="color:var(--text-dim);">← Kembali ke beranda</a>
    </p>
</div>
@endsection
