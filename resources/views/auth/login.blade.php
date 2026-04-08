@extends('layouts.auth')
@section('title', 'Masuk — DramaCina')
@section('content')
<div class="auth-card">
    <div class="auth-logo">DramaCina</div>
    <h2 style="text-align:center;margin-bottom:var(--gap-xl);font-size:1.2rem;color:var(--text-secondary);">Selamat datang kembali</h2>

    @if ($errors->any())
        <div class="alert alert-error">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf
        <div class="form-group">
            <label for="email" class="form-label">Email</label>
            <input id="email" type="email" name="email" class="form-input" value="{{ old('email') }}" required autofocus placeholder="email@contoh.com">
        </div>
        <div class="form-group">
            <label for="password" class="form-label">Password</label>
            <input id="password" type="password" name="password" class="form-input" required placeholder="••••••••">
        </div>
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:var(--gap-lg);">
            <label style="display:flex;align-items:center;gap:var(--gap-sm);font-size:0.85rem;color:var(--text-secondary);cursor:pointer;">
                <input type="checkbox" name="remember" style="accent-color:var(--gold);"> Ingat saya
            </label>
            @if(Route::has('password.request'))
                <a href="{{ route('password.request') }}" style="font-size:0.8rem;color:var(--warm);">Lupa password?</a>
            @endif
        </div>
        <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;">Masuk</button>
    </form>

    <p style="text-align:center;margin-top:var(--gap-lg);font-size:0.875rem;color:var(--text-muted);">
        Belum punya akun?
        <a href="{{ route('register') }}" style="color:var(--gold);">Daftar sekarang</a>
    </p>
    <p style="text-align:center;margin-top:var(--gap-sm);font-size:0.8rem;">
        <a href="{{ route('home') }}" style="color:var(--text-dim);">← Kembali ke beranda</a>
    </p>
</div>
@endsection
