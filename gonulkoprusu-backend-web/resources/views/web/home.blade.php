@extends('layouts.app')

@section('title', 'Gönül Köprüsü — Kalpten Kalbe, Güvenle')

@section('content')
<section class="hero-photo">
    <div class="hero-photo-bg" style="background-image: url('{{ asset('images/hero-couple-hero.jpg') }}')"></div>
    <div class="hero-photo-overlay"></div>

    <div class="container hero-photo-inner">
        <div class="hero-photo-content">
            <p class="hero-kicker">Türkiye'nin Güvenli Tanışma Platformu</p>
            <h1>Gönül Köprüsü</h1>
            <p class="hero-heart-divider">
                <span>Kalpten kalbe,</span>
                <svg viewBox="0 0 24 24" width="16" height="16" aria-hidden="true"><path fill="currentColor" d="M12 21s-6.7-4.6-6.7-9.4C5.3 8.6 7.6 6.3 10.4 6.3c1.5 0 2.9.8 3.6 2 0.7-1.2 2.1-2 3.6-2 2.8 0 5.1 2.3 5.1 5.3C18.7 16.4 12 21 12 21z"/></svg>
                <span>güvenle.</span>
            </p>
            <p class="hero-description">Ciddi ilişki arayanları, saygılı ve modern bir ortamda buluşturuyoruz. Her profil gerçek, her sohbet anlamlı.</p>
            @guest
                <div class="hero-actions">
                    <a href="{{ route('register') }}" class="btn btn-primary btn-hero-mobile">Hemen Keşfet</a>
                    <a href="{{ route('login') }}" class="btn btn-outline btn-hero-mobile">Giriş Yap</a>
                </div>
            @else
                <a href="{{ route('feed') }}" class="btn btn-primary btn-hero-mobile">Akışa Git</a>
            @endguest
        </div>
    </div>
</section>

<section class="about-section" id="hakkimizda">
    <div class="container about-grid">
        <div>
            <h2>Gönül Köprüsü Nedir?</h2>
            <p>Türkiye'nin güvenli ve samimi tanışma platformu. Ciddi ilişki arayanları, saygılı ve modern bir ortamda buluşturuyoruz. Her profil gerçek, her sohbet anlamlı.</p>
        </div>
        <div class="about-cards">
            <div class="about-card">
                <strong>10K+</strong>
                <span>Aktif üye</span>
            </div>
            <div class="about-card">
                <strong>500+</strong>
                <span>Mutlu çift</span>
            </div>
            <div class="about-card">
                <strong>7/24</strong>
                <span>Destek</span>
            </div>
        </div>
    </div>
</section>

<section class="how-it-works" id="nasil-calisir">
    <div class="container">
        <h2 class="how-title">Nasıl Çalışır?</h2>
        <p class="how-subtitle">Üç basit adımda tanışma yolculuğunuza başlayın</p>
        <div class="how-steps">
            <div class="how-step">
                <div class="how-step-number">1</div>
                <h3>Profil Oluştur</h3>
                <p>Ücretsiz hesabını oluştur, profil fotoğrafını ekle ve kendini tanıt.</p>
            </div>
            <div class="how-step">
                <div class="how-step-number">2</div>
                <h3>Keşfet & Eşleş</h3>
                <p>Şehir ve ilçe bazlı üyeleri keşfet, profilleri incele ve beğen.</p>
            </div>
            <div class="how-step">
                <div class="how-step-number">3</div>
                <h3>Sohbet Başlat</h3>
                <p>Güvenli mesajlaşma ile tanış, hikayeler paylaş ve bağ kur.</p>
            </div>
        </div>
    </div>
</section>

<section class="security-section" id="guvenlik">
    <div class="container security-grid">
        <h2>Güvenliğiniz Önceliğimiz</h2>
        <div class="security-cards">
            <div class="security-card">
                <div class="security-card-icon">🔒</div>
                <h3>Gizlilik Politikası</h3>
                <p>Kişisel verileriniz şifreli ve güvende tutulur. Bilgileriniz üçüncü taraflarla paylaşılmaz.</p>
            </div>
            <div class="security-card">
                <div class="security-card-icon">🛡️</div>
                <h3>Şikayet & Engelleme</h3>
                <p>Rahatsız edici profilleri anında engelleyin. Moderasyon ekibimiz 7/24 aktif.</p>
            </div>
            <div class="security-card">
                <div class="security-card-icon">💝</div>
                <h3>Güvenli Tanışma</h3>
                <p>İlk buluşma için güvenlik ipuçları ve rehberler sunuyoruz.</p>
            </div>
        </div>
    </div>
</section>
@endsection
