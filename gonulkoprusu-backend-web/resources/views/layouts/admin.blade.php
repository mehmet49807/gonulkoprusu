<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Yönetim Paneli') — Gönül Köprüsü</title>
    @include('partials.admin-head')
</head>
<body class="admin-body">
    <button class="admin-menu-toggle" type="button" onclick="document.querySelector('.admin-sidebar').classList.toggle('open')">☰ Menü</button>

    <div class="admin-shell">
        <aside class="admin-sidebar">
            <div class="admin-sidebar-glow" aria-hidden="true"></div>

            <div class="admin-sidebar-header">
                <a href="{{ route('admin.dashboard') }}" class="logo admin-logo">
                    @include('partials.logo')
                </a>
                <p class="admin-panel-label">Yönetim Paneli</p>
            </div>

            <p class="admin-nav-heading">Ana Menü</p>
            <ul class="admin-nav">
                <li>
                    <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <span class="admin-nav-icon-wrap"><svg class="admin-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg></span>
                        Dashboard
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.users') }}" class="{{ request()->routeIs('admin.users') ? 'active' : '' }}">
                        <span class="admin-nav-icon-wrap"><svg class="admin-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="7" r="3"/><circle cx="17" cy="9" r="2.5"/><path d="M2 20c0-3.3 3.1-5 7-5s7 1.7 7 5"/><path d="M14 20c0-2.2 2-3.5 4.5-3.5"/></svg></span>
                        Kullanıcılar
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.messages') }}" class="{{ request()->routeIs('admin.messages') ? 'active' : '' }}">
                        <span class="admin-nav-icon-wrap"><svg class="admin-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 11.5a8.4 8.4 0 0 1-.9 3.8 8 8 0 0 1-7.6 5.3 8.4 8.4 0 0 1-3.8-.9L3 21l1.3-5.7A8.4 8.4 0 0 1 3 11.5 8 8 0 0 1 11.5 3a8.4 8.4 0 0 1 3.8.9L21 3l-1.3 5.7a8.4 8.4 0 0 1 .3 2.8z"/></svg></span>
                        Mesajlar
                    </a>
                </li>
            </ul>

            <p class="admin-nav-heading">Moderasyon</p>
            <ul class="admin-nav">
                <li>
                    <a href="{{ route('admin.reports') }}" class="{{ request()->routeIs('admin.reports') ? 'active' : '' }}">
                        <span class="admin-nav-icon-wrap"><svg class="admin-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 9v4"/><path d="M12 17h.01"/><path d="M10.3 3.6L2.6 17a2 2 0 0 0 1.7 3h15.4a2 2 0 0 0 1.7-3L13.7 3.6a2 2 0 0 0-3.4 0z"/></svg></span>
                        Şikayetler
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.premium') }}" class="{{ request()->routeIs('admin.premium') ? 'active' : '' }}">
                        <span class="admin-nav-icon-wrap"><svg class="admin-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.1 8.6 22 9.3 17 14.1 18.2 21 12 17.8 5.8 21 7 14.1 2 9.3 8.9 8.6 12 2"/></svg></span>
                        Premium
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.broadcasts') }}" class="{{ request()->routeIs('admin.broadcasts') ? 'active' : '' }}">
                        <span class="admin-nav-icon-wrap"><svg class="admin-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8a3 3 0 1 0 0 6"/><path d="M6 8v8c0 1.1.9 2 2 2h7l5 3V5l-5 3H8c-1.1 0-2 .9-2 2z"/></svg></span>
                        Duyurular
                    </a>
                </li>
            </ul>

            <div class="admin-sidebar-footer">
                <a href="{{ config('app.url') }}" class="admin-sidebar-link">← Siteye Dön</a>
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit" class="admin-sidebar-logout">Çıkış Yap</button>
                </form>
            </div>
        </aside>

        <main class="admin-main">
            <header class="admin-topbar">
                <div class="admin-topbar-left">
                    <span class="admin-topbar-kicker">Gönül Köprüsü Admin</span>
                    <span class="admin-topbar-title">@yield('title', 'Yönetim Paneli')</span>
                </div>
                <div class="admin-topbar-right">
                    @auth
                    <span class="admin-topbar-user">{{ auth()->user()->username }}</span>
                    @endauth
                    <span class="admin-topbar-badge">Yönetici</span>
                </div>
            </header>

            <div class="admin-main-inner">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @yield('content')
            </div>
        </main>
    </div>
</body>
</html>
