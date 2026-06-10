@php
    use App\Services\NotificationService;
    $sidebarUser = auth()->user();
    $notificationService = app(NotificationService::class);
    $unreadNotifications = $notificationService->unreadBroadcastCount($sidebarUser);
    $unreadMessages = $notificationService->unreadMessageCount($sidebarUser);
    $active = $active ?? '';
@endphp

<aside class="app-sidebar" aria-label="Uygulama menüsü">
    <a href="{{ route('feed') }}" class="app-sidebar-logo" aria-label="Gönül Köprüsü ana sayfa">
        @include('partials.logo')
    </a>

    <nav class="app-sidebar-nav">
        <a href="{{ route('feed') }}" class="app-nav-item {{ $active === 'feed' ? 'app-nav-item--active' : '' }}">
            <span class="app-nav-icon app-nav-icon--feed" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M4 10.5L12 4l8 6.5V19a2 2 0 01-2 2H6a2 2 0 01-2-2v-8.5z" stroke="currentColor" stroke-width="1.75" stroke-linejoin="round"/>
                    <path d="M9 21v-6h6v6" stroke="currentColor" stroke-width="1.75" stroke-linejoin="round"/>
                </svg>
            </span>
            <span class="app-nav-label">Akış</span>
        </a>

        <a href="{{ route('profile') }}" class="app-nav-item {{ $active === 'profile' ? 'app-nav-item--active' : '' }}">
            <span class="app-nav-icon app-nav-icon--profile" aria-hidden="true">
                @if($sidebarUser->profile_photo_url)
                    <img src="{{ $sidebarUser->profile_photo_url }}" alt="" class="app-nav-avatar">
                @else
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="12" cy="8" r="4" stroke="currentColor" stroke-width="1.75"/>
                        <path d="M5 20c0-3.866 3.134-7 7-7s7 3.134 7 7" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"/>
                    </svg>
                @endif
            </span>
            <span class="app-nav-label">Profil</span>
        </a>

        <a href="{{ route('messages.index') }}" class="app-nav-item {{ $active === 'messages' ? 'app-nav-item--active' : '' }}">
            <span class="app-nav-icon app-nav-icon--messages" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M5 6.5A3.5 3.5 0 018.5 3h7A3.5 3.5 0 0119 6.5v7A3.5 3.5 0 0115.5 17H10l-4.5 3.5V17H8.5A3.5 3.5 0 015 13.5v-7z" stroke="currentColor" stroke-width="1.75" stroke-linejoin="round"/>
                </svg>
            </span>
            <span class="app-nav-label">Mesajlar</span>
            @if($unreadMessages > 0)
                <span class="app-nav-badge">{{ $unreadMessages > 9 ? '9+' : $unreadMessages }}</span>
            @endif
        </a>

        <a href="{{ route('notifications.index') }}" class="app-nav-item {{ $active === 'notifications' ? 'app-nav-item--active' : '' }}">
            <span class="app-nav-icon app-nav-icon--notifications" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 3a5 5 0 00-5 5v3.5L5 14.5h14l-2-3V8a5 5 0 00-5-5z" stroke="currentColor" stroke-width="1.75" stroke-linejoin="round"/>
                    <path d="M10 18a2 2 0 004 0" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"/>
                </svg>
            </span>
            <span class="app-nav-label">Bildirimler</span>
            @if($unreadNotifications > 0)
                <span class="app-nav-badge app-nav-badge--notify">{{ $unreadNotifications > 9 ? '9+' : $unreadNotifications }}</span>
            @endif
        </a>

        <a href="{{ route('premium') }}" class="app-nav-item {{ $active === 'premium' ? 'app-nav-item--active' : '' }}">
            <span class="app-nav-icon app-nav-icon--premium" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 3l2.2 5.2L20 9.3l-4.2 3.6L17 19l-5-3.1L7 19l1.2-6.1L4 9.3l5.8-1.1L12 3z" stroke="currentColor" stroke-width="1.75" stroke-linejoin="round"/>
                </svg>
            </span>
            <span class="app-nav-label">Premium</span>
        </a>
    </nav>

    <div class="app-sidebar-footer">
        @if($sidebarUser->isAdmin())
            <a href="{{ rtrim(config('app.admin_url'), '/') }}/dashboard" class="app-nav-item app-nav-item--sub">
                <span class="app-nav-icon app-nav-icon--admin" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 3l8 4v6c0 4.418-3.582 8-8 8s-8-3.582-8-8V7l8-4z" stroke="currentColor" stroke-width="1.75" stroke-linejoin="round"/>
                    </svg>
                </span>
                <span class="app-nav-label">Yönetim</span>
            </a>
        @endif
        <form action="{{ route('logout') }}" method="POST" class="app-sidebar-logout">
            @csrf
            <button type="submit" class="app-nav-item app-nav-item--sub app-nav-item--button">
                <span class="app-nav-icon app-nav-icon--logout" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M9 6v12M5 12h10m4 0l-3-3m3 3l-3 3" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </span>
                <span class="app-nav-label">Çıkış</span>
            </button>
        </form>
    </div>
</aside>
