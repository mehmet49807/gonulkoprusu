@extends('layouts.app')

@section('title', 'Gönül Köprüsü — Kalpten Kalbe, Güvenle')

@section('content')
<section class="hero-v2">
    <div class="hero-v2-shapes" aria-hidden="true">
        <div class="hero-v2-shape hero-v2-shape--1"></div>
        <div class="hero-v2-shape hero-v2-shape--2"></div>
        <div class="hero-v2-shape hero-v2-shape--3"></div>
    </div>

    <div class="container hero-v2-inner">
        <div class="hero-v2-content">
            <div class="hero-v2-badge">
                <svg viewBox="0 0 24 24" width="16" height="16" aria-hidden="true"><path fill="currentColor" d="M12 21s-6.7-4.6-6.7-9.4C5.3 8.6 7.6 6.3 10.4 6.3c1.5 0 2.9.8 3.6 2 0.7-1.2 2.1-2 3.6-2 2.8 0 5.1 2.3 5.1 5.3C18.7 16.4 12 21 12 21z"/></svg>
                Türkiye'nin güvenli tanışma platformu
            </div>

            <h1>Kalpten kalbe,<br><em>güvenle bağlan.</em></h1>

            <p class="hero-v2-lead">
                Ciddi ilişki arayanları samimi ve saygılı bir ortamda buluşturuyoruz.
                Gerçek profiller, anlamlı sohbetler ve güvenli tanışma deneyimi.
            </p>

            <div class="hero-v2-actions">
                @guest
                    <a href="{{ route('register') }}" class="btn btn-primary">Ücretsiz Üye Ol</a>
                    <a href="{{ route('login') }}" class="btn btn-hero-outline">Giriş Yap</a>
                @else
                    <a href="{{ route('feed') }}" class="btn btn-primary">Akışa Git</a>
                    <a href="{{ route('messages.index') }}" class="btn btn-hero-outline">Mesajlarım</a>
                @endguest
            </div>

            <div class="hero-v2-trust">
                <div class="hero-v2-trust-item">
                    <svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true"><path fill="currentColor" d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm-1 15l-4-4 1.41-1.41L11 13.17l5.59-5.59L18 9l-7 7z"/></svg>
                    Doğrulanmış profiller
                </div>
                <div class="hero-v2-trust-item">
                    <svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true"><path fill="currentColor" d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z"/></svg>
                    Şifreli veriler
                </div>
                <div class="hero-v2-trust-item">
                    <svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true"><path fill="currentColor" d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                    7/24 destek
                </div>
            </div>
        </div>

        <div class="hero-v2-visual">
            <div class="hero-v2-card">
                <img src="{{ asset('images/hero-couple-hero.jpg') }}" alt="Mutlu çift" width="400" height="500" loading="eager">
                <div class="hero-v2-card-overlay"></div>
                <div class="hero-v2-card-caption">
                    <strong>500+ mutlu çift</strong>
                    <span>Gönül Köprüsü'nde tanıştılar</span>
                </div>
            </div>

            <div class="hero-v2-float hero-v2-float--1">
                <div class="hero-v2-float-icon">💬</div>
                <div class="hero-v2-float-text">
                    <strong>Anlamlı sohbetler</strong>
                    <span>Gerçek bağlantılar kur</span>
                </div>
            </div>

            <div class="hero-v2-float hero-v2-float--2">
                <div class="hero-v2-float-icon">✨</div>
                <div class="hero-v2-float-text">
                    <strong>10K+ aktif üye</strong>
                    <span>Her gün yeni tanışmalar</span>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="steps-section" id="nasil-calisir">
    <div class="container">
        <div class="section-header">
            <span class="section-eyebrow">Nasıl Çalışır</span>
            <h2 class="section-title">Üç adımda tanışmaya başla</h2>
            <p class="section-lead">Basit, hızlı ve güvenli. Dakikalar içinde profilini oluştur, gerçek insanlarla tanış.</p>
        </div>

        <div class="steps-grid">
            <div class="step-card">
                <span class="step-number">1</span>
                <div class="step-icon">📝</div>
                <h3>Profilini Oluştur</h3>
                <p>Ücretsiz kayıt ol, fotoğrafını ekle ve kendini tanıt. Gerçek bilgilerle güven oluştur.</p>
            </div>
            <div class="step-card">
                <span class="step-number">2</span>
                <div class="step-icon">🔍</div>
                <h3>Keşfet & Bağlan</h3>
                <p>Akışta paylaşımları incele, ilgini çeken profilleri ziyaret et ve mesaj gönder.</p>
            </div>
            <div class="step-card">
                <span class="step-number">3</span>
                <div class="step-icon">💕</div>
                <h3>Güvenle Tanış</h3>
                <p>Anlamlı sohbetler kur, güvenlik rehberlerimizle güvenli buluşmalar planla.</p>
            </div>
        </div>
    </div>
</section>

<section class="features-section" id="hakkimizda">
    <div class="container">
        <div class="section-header">
            <span class="section-eyebrow">Neden Gönül Köprüsü?</span>
            <h2 class="section-title">Ciddi ilişki için tasarlandı</h2>
            <p class="section-lead">Flört uygulamalarının hızlı kaydırma kültüründen uzak, saygılı ve samimi bir platform.</p>
        </div>

        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-card-icon">
                    <svg viewBox="0 0 24 24" width="22" height="22" fill="currentColor" aria-hidden="true"><path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4z"/></svg>
                </div>
                <h3>Güvenli Ortam</h3>
                <p>Şikayet ve engelleme sistemiyle rahatsız edici davranışlara anında müdahale edilir.</p>
            </div>
            <div class="feature-card">
                <div class="feature-card-icon">
                    <svg viewBox="0 0 24 24" width="22" height="22" fill="currentColor" aria-hidden="true"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                </div>
                <h3>Gerçek Profiller</h3>
                <p>Her profil gerçek kişilere ait. Sahte hesaplara karşı aktif moderasyon uygulanır.</p>
            </div>
            <div class="feature-card">
                <div class="feature-card-icon">
                    <svg viewBox="0 0 24 24" width="22" height="22" fill="currentColor" aria-hidden="true"><path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2z"/></svg>
                </div>
                <h3>Anlamlı Sohbetler</h3>
                <p>Mesajlaşma sistemi ciddi iletişim için tasarlandı. Yüzeysel etkileşimlerin önüne geçilir.</p>
            </div>
            <div class="feature-card">
                <div class="feature-card-icon">
                    <svg viewBox="0 0 24 24" width="22" height="22" fill="currentColor" aria-hidden="true"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5A2.5 2.5 0 1 1 12 6a2.5 2.5 0 0 1 0 5.5z"/></svg>
                </div>
                <h3>Yerel Bağlantılar</h3>
                <p>Şehir ve bölge bazlı keşif ile yakınınızdaki insanlarla tanışma imkânı sunulur.</p>
            </div>
            <div class="feature-card">
                <div class="feature-card-icon">
                    <svg viewBox="0 0 24 24" width="22" height="22" fill="currentColor" aria-hidden="true"><path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2z"/></svg>
                </div>
                <h3>Gizlilik Koruması</h3>
                <p>Kişisel verileriniz şifreli saklanır. Gizlilik politikamız KVKK uyumludur.</p>
            </div>
            <div class="feature-card">
                <div class="feature-card-icon">
                    <svg viewBox="0 0 24 24" width="22" height="22" fill="currentColor" aria-hidden="true"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                </div>
                <h3>Premium Deneyim</h3>
                <p>Özel özellikler ve öncelikli destek ile tanışma yolculuğunu hızlandır.</p>
            </div>
        </div>
    </div>
</section>

<section class="testimonials-section">
    <div class="container">
        <div class="section-header">
            <span class="section-eyebrow">Mutlu Hikayeler</span>
            <h2 class="section-title">Üyelerimiz ne diyor?</h2>
            <p class="section-lead">Gönül Köprüsü'nde tanışan çiftlerden geri bildirimler.</p>
        </div>

        <div class="testimonials-grid">
            <div class="testimonial-card">
                <div class="testimonial-stars">★★★★★</div>
                <blockquote>Güvenli bir ortamda tanışmak istiyordum. Gönül Köprüsü tam aradığım platformdu. Şimdi nişanlıyız!</blockquote>
                <div class="testimonial-author">
                    <div class="testimonial-avatar">E</div>
                    <div>
                        <strong>Elif &amp; Murat</strong>
                        <span>İstanbul · 2025</span>
                    </div>
                </div>
            </div>
            <div class="testimonial-card">
                <div class="testimonial-stars">★★★★★</div>
                <blockquote>Sahte profillerden bıkmıştım. Burada gerçek insanlarla samimi sohbetler kurabildim. Çok memnunum.</blockquote>
                <div class="testimonial-author">
                    <div class="testimonial-avatar">A</div>
                    <div>
                        <strong>Ayşe</strong>
                        <span>Ankara · 2025</span>
                    </div>
                </div>
            </div>
            <div class="testimonial-card">
                <div class="testimonial-stars">★★★★★</div>
                <blockquote>Arayüz sade ve kullanımı kolay. Destek ekibi de çok ilgili. Kesinlikle tavsiye ederim.</blockquote>
                <div class="testimonial-author">
                    <div class="testimonial-avatar">K</div>
                    <div>
                        <strong>Kemal</strong>
                        <span>İzmir · 2026</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="security-section" id="guvenlik">
    <div class="container security-grid">
        <h2>Güvenliğiniz Önceliğimiz</h2>
        <div class="security-cards">
            <div class="security-card">
                <h3>Gizlilik Politikası</h3>
                <p>Kişisel verileriniz şifreli ve güvende tutulur. KVKK uyumlu veri işleme.</p>
            </div>
            <div class="security-card">
                <h3>Şikayet & Engelleme</h3>
                <p>Rahatsız edici profilleri anında engelleyin, moderasyon ekibimize bildirin.</p>
            </div>
            <div class="security-card">
                <h3>Güvenli Tanışma</h3>
                <p>İlk buluşma için güvenlik ipuçları ve rehberler her zaman elinizin altında.</p>
            </div>
        </div>
    </div>
</section>

@guest
<section class="cta-banner">
    <div class="container cta-banner-inner">
        <div>
            <h2>Hikayen bugün başlasın</h2>
            <p>Ücretsiz kayıt ol, gerçek bağlar kur. İlk adımı atmak için tek tık yeterli.</p>
            @include('partials.store-badges', ['class' => 'store-badges-inline'])
        </div>
        <a href="{{ route('register') }}" class="btn btn-primary" style="padding: 16px 40px; font-size: 1.05rem;">Hemen Üye Ol</a>
    </div>
</section>
@endguest
@endsection
