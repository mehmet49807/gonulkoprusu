@extends('layouts.app-with-sidebar')

@php $activeNav = 'premium'; @endphp

@section('title', 'Premium Paketler — Gönül Köprüsü')

@section('app-content')
<div class="premium-page">
    <header class="premium-hero">
        <h1>Premium Paketler</h1>
        @if($user->gender === 'female')
            <p class="premium-hero-sub">Kadın kullanıcılar tüm özelliklere ücretsiz erişebilir.</p>
        @else
            <p class="premium-hero-sub">Hikaye paylaşmak ve daha fazla görünürlük için premium üye olun.</p>
        @endif
    </header>

    @if($user->gender === 'male')
        @if($user->isOnTrial())
        <div class="premium-status premium-status--trial">
            <div class="premium-status-icon">⏳</div>
            <div>
                <strong>3 Günlük Deneme Aktif</strong>
                <p>{{ $user->trial_ends_at->format('d.m.Y H:i') }} tarihine kadar premium özellikler kullanılabilir
                    ({{ $user->trialDaysRemaining() }} gün kaldı)</p>
            </div>
        </div>
        @elseif($activeSubscription)
        <div class="premium-status premium-status--active">
            <div class="premium-status-icon">★</div>
            <div>
                <strong>Aktif Premium: {{ $packages[$activeSubscription->package_type]['name'] ?? ucfirst($activeSubscription->package_type) }}</strong>
                <p>{{ $activeSubscription->expires_at->format('d.m.Y H:i') }} tarihine kadar geçerli</p>
            </div>
        </div>
        @else
        <div class="premium-status premium-status--inactive">
            <p>Deneme süreniz sona erdi. Premium satın almak için Android veya iOS uygulamasını kullanın.</p>
        </div>
        @endif

        <div class="premium-app-notice">
            <h2>Mobil Uygulama ile Satın Alın</h2>
            <p>Premium ödemeler yalnızca <strong>Android</strong> ve <strong>iOS</strong> uygulamaları üzerinden yapılabilir. Web sitesinde ödeme entegrasyonu bulunmamaktadır.</p>
            @include('partials.store-badges', ['class' => 'store-badges--premium'])
        </div>

        <section class="premium-features" aria-label="Premium özellikler">
            <h2>Premium ile neler kazanırsınız?</h2>
            <ul class="premium-features-list">
                @foreach($features as $feature)
                <li>{{ $feature }}</li>
                @endforeach
            </ul>
        </section>

        <section class="premium-packages" aria-label="Paketler">
            <div class="premium-grid">
                @foreach($packages as $type => $pkg)
                <div class="premium-card premium-card--{{ $type }}{{ $type === 'platinum' ? ' premium-card--featured' : '' }}">
                    @if($type === 'platinum')
                        <span class="premium-badge">En Popüler</span>
                    @endif
                    <div class="premium-card-icon premium-card-icon--{{ $type }}" aria-hidden="true">
                        @if($type === 'pro')
                            <svg class="premium-icon-svg" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <circle cx="32" cy="32" r="28" stroke="currentColor" stroke-width="2.5" opacity="0.35"/>
                                <path d="M36 14L22 36h11l-3 14 18-24H35l1-12z" fill="currentColor" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>
                            </svg>
                        @elseif($type === 'gold')
                            <svg class="premium-icon-svg" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M32 10l6.5 13.2L54 26l-10 9.7L47 52 32 43.8 17 52l3-16.3L10 26l15.5-2.8L32 10z" fill="currentColor" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>
                                <circle cx="32" cy="32" r="26" stroke="currentColor" stroke-width="2" opacity="0.3"/>
                            </svg>
                        @else
                            <svg class="premium-icon-svg" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M32 8l6 18h19l-15 11 6 18-16-12-16 12 6-18-15-11h19l6-18z" fill="currentColor" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>
                                <circle cx="32" cy="32" r="27" stroke="currentColor" stroke-width="2" opacity="0.25"/>
                                <circle class="premium-icon-sparkle" cx="48" cy="16" r="2.5" fill="currentColor"/>
                                <circle class="premium-icon-sparkle premium-icon-sparkle--delay" cx="14" cy="20" r="2" fill="currentColor"/>
                            </svg>
                        @endif
                    </div>
                    <h3>{{ $pkg['name'] }}</h3>
                    <div class="price">{{ number_format($pkg['price_tl'], 0, ',', '.') }} <small>TL</small></div>
                    <div class="duration">{{ $pkg['duration_days'] }} gün</div>
                    <ul class="premium-card-features">
                        <li>Hikaye paylaşımı</li>
                        <li>{{ $pkg['duration_days'] }} gün erişim</li>
                        @if($type === 'gold')
                            <li>14 gün görünürlük</li>
                        @elseif($type === 'platinum')
                            <li>30 gün tam erişim</li>
                            <li>En iyi değer</li>
                        @else
                            <li>7 gün deneme</li>
                        @endif
                    </ul>
                    <p class="premium-card-app-only">Uygulamadan satın alın</p>
                </div>
                @endforeach
            </div>
        </section>
    @endif
</div>
@endsection
