@extends('layouts.app')

@section('title', 'Gönül Köprüsü — Kalpten Kalbe, Güvenle')

@section('content')
{{-- ── HERO ── --}}
<section class="hero-photo">
    <div class="hero-photo-bg" style="background-image: url('{{ asset('images/hero-couple-hero.jpg') }}')"></div>
    <div class="hero-photo-overlay"></div>

    <div class="container hero-photo-inner">
        <div class="hero-photo-content">
            <div class="hero-kicker">
                <span class="hero-kicker-dot"></span>
                Türkiye'nin Güvenli Tanışma Platformu
            </div>
            <h1>Gönül Köprüsü<br><em>Kalpten Kalbe</em></h1>
            <p class="hero-heart-divider">
                <svg viewBox="0 0 24 24" width="16" height="16" aria-hidden="true"><path fill="currentColor" d="M12 21s-6.7-4.6-6.7-9.4C5.3 8.6 7.6 6.3 10.4 6.3c1.5 0 2.9.8 3.6 2 0.7-1.2 2.1-2 3.6-2 2.8 0 5.1 2.3 5.1 5.3C18.7 16.4 12 21 12 21z"/></svg>
                Samimi bağlar, güvenli ortam, anlamlı ilişkiler.
            </p>
            <div class="hero-cta-group">
                @guest
                    <a href="{{ route('register') }}" class="btn btn-hero-primary">Hemen Ücretsiz Üye Ol</a>
                    <a href="{{ route('login') }}" class="btn btn-hero-outline">Giriş Yap</a>
                @else
                    <a href="{{ route('feed') }}" class="btn btn-hero-primary">Akışa Git</a>
                @endguest
            </div>
            <div class="hero-stats">
                <div class="hero-stat">
                    <strong>10K+</strong>
                    <span>Aktif Üye</span>
                </div>
                <div class="hero-stat">
                    <strong>500+</strong>
                    <span>Mutlu Çift</span>
                </div>
                <div class="hero-stat">
                    <strong>7/24</strong>
                    <span>Destek</span>
                </div>
                <div class="hero-stat">
                    <strong>%100</strong>
                    <span>Güvenli</span>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ── ABOUT ── --}}
<section class="about-section" id="hakkimizda">
    <div class="container about-grid">
        <div>
            <h2>Gönül Köprüsü Nedir?</h2>
            <p>Türkiye'nin güvenli ve samimi tanışma platformu. Ciddi ilişki arayanları, saygılı ve modern bir ortamda buluşturuyoruz. Her profil gerçek, her sohbet anlamlı.</p>
            <p>Kimliği doğrulanmış gerçek profiller, şifreli mesajlaşma ve 7/24 destek ekibimizle yanınızdayız.</p>
            <a href="{{ route('about') }}" class="about-cta-link">
                Hakkımızda daha fazla öğren →
            </a>
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

{{-- ── HOW IT WORKS ── --}}
<section class="how-section">
    <div class="container">
        <h2>Nasıl Çalışır?</h2>
        <p class="section-subtitle">3 adımda tanışmaya başla</p>
        <div class="how-steps">
            <div class="how-step">
                <div class="how-step-num">1</div>
                <h3>Profil Oluştur</h3>
                <p>Gerçek bilgilerinle ücretsiz hesap aç, fotoğraf yükle ve kendini tanıt.</p>
            </div>
            <div class="how-step">
                <div class="how-step-num">2</div>
                <h3>Keşfet &amp; Beğen</h3>
                <p>Akışta paylaşımları gözden geçir, ilgi çekici profilleri keşfet ve beğen.</p>
            </div>
            <div class="how-step">
                <div class="how-step-num">3</div>
                <h3>Mesajlaş</h3>
                <p>Karşılıklı ilgi varsa güvenli özel mesaj kanalıyla sohbete başla.</p>
            </div>
        </div>
    </div>
</section>

{{-- ── SECURITY ── --}}
<section class="security-section" id="guvenlik">
    <div class="container">
        <h2>Güvenliğiniz Önceliğimiz</h2>
        <p class="section-subtitle">Her adımda yanınızdayız</p>
        <div class="security-cards">
            <div class="security-card">
                <div class="security-card-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                </div>
                <h3>Gizlilik Politikası</h3>
                <p>Kişisel verileriniz uçtan uca şifrelenerek güvende tutulur. Asla üçüncü taraflarla paylaşılmaz.</p>
            </div>
            <div class="security-card">
                <div class="security-card-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/></svg>
                </div>
                <h3>Şikayet &amp; Engelleme</h3>
                <p>Rahatsız edici profilleri ve mesajları anında engelleyin; 7/24 destek ekibimize bildirin.</p>
            </div>
            <div class="security-card">
                <div class="security-card-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                </div>
                <h3>Güvenli Tanışma</h3>
                <p>İlk buluşmanız için güvenlik rehberimizi okuyun. Kamuya açık mekânları tercih edin.</p>
            </div>
        </div>
    </div>
</section>

{{-- ── TRUST BANNER ── --}}
<section class="trust-section">
    <div class="container trust-grid">
        <div class="trust-item">
            <strong>10K+</strong>
            <span>Aktif Üye</span>
        </div>
        <div class="trust-item">
            <strong>500+</strong>
            <span>Mutlu Çift</span>
        </div>
        <div class="trust-item">
            <strong>%100</strong>
            <span>Güvenli Platform</span>
        </div>
        <div class="trust-item">
            <strong>7/24</strong>
            <span>Canlı Destek</span>
        </div>
    </div>
</section>
@endsection
