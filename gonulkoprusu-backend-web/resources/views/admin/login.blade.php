<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yönetici Girişi — Gönül Köprüsü</title>
    @include('partials.admin-head')
</head>
<body class="admin-login-body">
<div class="admin-login-page">
    <div class="admin-login-visual">
        <div class="admin-login-pattern" aria-hidden="true"></div>
        <div class="admin-login-visual-content">
            <div class="admin-login-shield">
                <svg viewBox="0 0 24 24" stroke-width="2">
                    <path d="M12 2l8 4v6c0 5.5-3.8 10.7-8 12-4.2-1.3-8-6.5-8-12V6l8-4z"/>
                    <path d="M9 12l2 2 4-4"/>
                </svg>
            </div>
            <h1>Yönetim Paneli</h1>
            <p>Gönül Köprüsü platformunu güvenle yönetin.</p>
            <ul class="admin-login-features">
                <li>Kullanıcı & premium yönetimi</li>
                <li>Mesaj ve şikayet denetimi</li>
                <li>Toplu duyuru sistemi</li>
            </ul>
        </div>
    </div>

    <div class="admin-login-form-wrap">
        <div class="admin-login-card">
            <div class="admin-login-brand">
                @include('partials.logo')
            </div>
            <h2>Yönetici Girişi</h2>
            <p class="admin-login-sub">Yalnızca yetkili yönetici hesapları giriş yapabilir.</p>

            <form method="POST" action="{{ route('admin.login') }}">
                @csrf
                <div class="form-group">
                    <label>Kullanıcı Adı veya E-posta</label>
                    <input type="text" name="login" value="{{ old('login') }}" required autofocus placeholder="admin@gonulkoprusu.com">
                    @error('login') <small class="form-error">{{ $message }}</small> @enderror
                </div>
                <div class="form-group">
                    <label>Şifre</label>
                    <input type="password" name="password" required placeholder="••••••••">
                </div>
                <button type="submit" class="btn btn-primary btn-full">Panele Giriş</button>
            </form>

            <p class="admin-login-back"><a href="{{ config('app.url') }}">← Ana siteye dön</a></p>
        </div>
    </div>
</div>
</body>
</html>
