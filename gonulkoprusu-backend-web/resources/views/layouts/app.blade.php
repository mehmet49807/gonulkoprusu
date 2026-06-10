<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Gönül Köprüsü')</title>
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700&family=Playfair+Display:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v=gul-bahcesi-v2">
</head>
@php $appShell = trim($__env->yieldContent('body-class')) === 'app-shell'; @endphp
<body class="site-body @yield('body-class')">
    <header class="site-header">
        <div class="container header-inner">
            <a href="{{ route('home') }}" class="logo logo-header">
                @include('partials.logo')
            </a>

            <nav class="nav-center" aria-label="Ana menü">
                <a href="{{ route('home') }}">Ana Sayfa</a>
                <a href="{{ route('about') }}">Hakkımızda</a>
                <a href="{{ route('safe-meeting') }}">Güvenlik</a>
            </nav>

            @unless($appShell)
            <div class="header-actions">
                @auth
                    @php
                        $headerNotify = app(\App\Services\NotificationService::class);
                        $unreadMessages = $headerNotify->unreadMessageCount(auth()->user());
                        $unreadNotifications = $headerNotify->unreadBroadcastCount(auth()->user());
                    @endphp
                    <a href="{{ route('feed') }}" class="nav-auth-link">Akış</a>
                    <a href="{{ route('notifications.index') }}" class="nav-auth-link">
                        Bildirimler
                        @if($unreadNotifications > 0)
                            <span class="nav-badge">{{ $unreadNotifications > 9 ? '9+' : $unreadNotifications }}</span>
                        @endif
                    </a>
                    <a href="{{ route('messages.index') }}" class="nav-auth-link">
                        Mesajlar
                        @if($unreadMessages > 0)
                            <span class="nav-badge">{{ $unreadMessages > 9 ? '9+' : $unreadMessages }}</span>
                        @endif
                    </a>
                    <a href="{{ route('profile') }}" class="nav-auth-link">Profil</a>
                    @if(auth()->user()->gender === 'male')
                        <a href="{{ route('premium') }}" class="nav-auth-link nav-auth-link--premium">Premium</a>
                    @endif
                    @if(auth()->user()->isAdmin())
                        <a href="{{ rtrim(config('app.admin_url'), '/') }}/dashboard" class="nav-auth-link">Yönetim</a>
                    @endif
                    <form action="{{ route('logout') }}" method="POST" class="nav-logout">
                        @csrf
                        <button type="submit" class="btn btn-outline btn-sm">Çıkış</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="btn btn-outline btn-sm">Giriş Yap</a>
                    <a href="{{ route('register') }}" class="btn btn-primary btn-sm">Üye Ol</a>
                @endauth
            </div>
            @endunless

            <button class="nav-toggle" type="button" aria-label="Menü" onclick="document.querySelector('.nav-mobile').classList.toggle('open')">
                <span></span><span></span><span></span>
            </button>
        </div>

        <nav class="nav-mobile" aria-label="Mobil menü">
            <a href="{{ route('home') }}">Ana Sayfa</a>
            <a href="{{ route('about') }}">Hakkımızda</a>
            <a href="{{ route('safe-meeting') }}">Güvenlik</a>
            <a href="{{ route('home') }}#iletisim">İletişim</a>
            @unless($appShell)
                @auth
                    <a href="{{ route('feed') }}">Akış</a>
                    <a href="{{ route('notifications.index') }}">Bildirimler</a>
                    <a href="{{ route('messages.index') }}">Mesajlar</a>
                    <a href="{{ route('profile') }}">Profil</a>
                    @if(auth()->user()->gender === 'male')
                        <a href="{{ route('premium') }}">Premium</a>
                    @endif
                @endauth
                @guest
                    <a href="{{ route('login') }}">Giriş Yap</a>
                    <a href="{{ route('register') }}" class="btn btn-primary btn-sm">Üye Ol</a>
                @endguest
            @endunless
        </nav>
    </header>

    <main>
        @if(session('success'))
            <div class="container" style="margin-top:16px">
                <div class="alert alert-success">{{ session('success') }}</div>
            </div>
        @endif
        @yield('content')
    </main>

    @include('partials.footer')
</body>
</html>
