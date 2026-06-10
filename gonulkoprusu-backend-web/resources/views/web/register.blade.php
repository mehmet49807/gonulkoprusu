@extends('layouts.app')

@section('title', 'Kayıt Ol — Gönül Köprüsü')

@section('content')
<section class="auth-page">
    <div class="auth-visual">
        <img src="{{ asset('images/auth-register.jpg') }}" alt="Mutlu çift" class="auth-visual-img" width="800" height="1000" loading="eager">
        <div class="auth-visual-overlay"></div>
        <div class="auth-visual-text">
            <h2>Hikayen burada başlıyor</h2>
            <p>Güvenli ve samimi tanışma platformuna katıl.</p>
        </div>
    </div>

    <div class="auth-form-wrap">
        <div class="form-card form-card-wide">
            <h2>Kayıt Ol</h2>
            <p class="auth-form-sub">Ücretsiz hesap oluştur, gerçek bağlar kur.</p>

            <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data" id="registerForm">
                @csrf

                <div class="register-photo-block">
                    <div class="register-photo-preview" id="registerPhotoPreview">
                        <span class="register-photo-placeholder">📷</span>
                    </div>
                    <div class="register-photo-meta">
                        <label class="btn btn-outline btn-sm register-photo-btn">
                            Profil Fotoğrafı Ekle
                            <input type="file" name="photo" accept="image/jpeg,image/png,image/gif,image/webp" class="register-photo-input">
                        </label>
                        <p class="form-hint">İsteğe bağlı. JPEG, PNG, GIF veya WebP (en fazla 5 MB).</p>
                        @error('photo') <small class="form-error">{{ $message }}</small> @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label>Kullanıcı Adı</label>
                    <input type="text" name="username" value="{{ old('username') }}" required>
                    @error('username') <small class="form-error">{{ $message }}</small> @enderror
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Ad</label>
                        <input type="text" name="first_name" value="{{ old('first_name') }}" required>
                    </div>
                    <div class="form-group">
                        <label>Soyad</label>
                        <input type="text" name="last_name" value="{{ old('last_name') }}" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>E-posta</label>
                    <input type="email" name="email" value="{{ old('email') }}" required>
                </div>

                <div class="form-group">
                    <label>Şifre</label>
                    <input type="password" name="password" required>
                </div>

                <div class="form-group">
                    <label>Şifre Tekrar</label>
                    <input type="password" name="password_confirmation" required>
                </div>

                <div class="form-group">
                    <label>Telefon</label>
                    <input type="tel" name="phone" value="{{ old('phone') }}" required>
                </div>

                <div class="form-group">
                    <label>Cinsiyet</label>
                    <select name="gender" required>
                        <option value="">Seçiniz</option>
                        <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>Kadın</option>
                        <option value="male" {{ old('gender') === 'male' ? 'selected' : '' }}>Erkek</option>
                    </select>
                    <small class="form-hint">Erkek üyeler kayıt sonrası 3 gün ücretsiz deneme ile premium özelliklere erişir.</small>
                </div>

                <div class="form-group">
                    <label>Ülke, Şehir & İlçe</label>
                    @include('partials.location-fields')
                </div>

                <button type="submit" class="btn btn-primary btn-full">Kayıt Ol</button>
            </form>

            <p class="auth-switch">Zaten hesabın var mı? <a href="{{ route('login') }}">Giriş Yap</a></p>
        </div>
    </div>
</section>
<script src="{{ asset('js/locations.js') }}?v=world-locations-1"></script>
<script src="{{ asset('js/profile-photo.js') }}?v=profile-photo-1"></script>
@endsection
