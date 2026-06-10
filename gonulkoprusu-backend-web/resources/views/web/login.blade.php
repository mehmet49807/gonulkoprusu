@extends('layouts.app')

@section('title', 'Giriş — Gönül Köprüsü')

@section('content')
<section class="auth-page">
    <div class="auth-visual">
        <img src="{{ asset('images/auth-login.jpg') }}" alt="Mutlu çift" class="auth-visual-img" width="800" height="1000" loading="eager">
        <div class="auth-visual-overlay"></div>
        <div class="auth-visual-text">
            <h2>Tekrar hoş geldin</h2>
            <p>Kalpten kalbe, güvenle.</p>
        </div>
    </div>

    <div class="auth-form-wrap">
        <div class="form-card">
            <h2>Giriş Yap</h2>
            <p class="auth-form-sub">Hesabına giriş yap ve bağlantılarına kaldığın yerden devam et.</p>

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="form-group">
                    <label>Kullanıcı Adı veya E-posta</label>
                    <input type="text" name="login" value="{{ old('login') }}" required>
                    @error('login') <small class="form-error">{{ $message }}</small> @enderror
                </div>
                <div class="form-group">
                    <label>Şifre</label>
                    <input type="password" name="password" required>
                </div>
                <button type="submit" class="btn btn-primary btn-full">Giriş Yap</button>
            </form>

            <p class="auth-switch">Hesabın yok mu? <a href="{{ route('register') }}">Kayıt Ol</a></p>
        </div>
    </div>
</section>
@endsection
