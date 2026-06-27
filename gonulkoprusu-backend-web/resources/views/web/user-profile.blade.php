@extends('layouts.app')

@section('title', $user->username . ' — Gönül Köprüsü')

@section('content')
<div class="profile-card user-profile-card">
    <div class="user-profile-header">
        <div class="profile-photo">
            @if($user->profile_photo_url)
                <img src="{{ $user->profile_photo_url }}" alt="{{ $user->username }}" style="width:100%;height:100%;border-radius:50%;object-fit:cover">
            @else
                {{ strtoupper(substr($user->username, 0, 1)) }}
            @endif
        </div>
        <div class="user-profile-meta">
            <h1 class="user-profile-name">{{ $user->username }}</h1>
            @include('partials.online-status', ['user' => $user, 'class' => 'online-status--profile'])
            <p class="user-profile-location">
                @include('partials.location-link', [
                    'country' => $user->country ?? 'Türkiye',
                    'city' => $user->city,
                    'district' => $user->district,
                ])
            </p>
            <p class="user-profile-count">{{ $posts->count() }} gönderi</p>

            <div class="user-profile-actions">
                <a href="{{ route('messages.show', $user->username) }}" class="profile-action-btn profile-action-btn--message">
                    <span class="profile-action-icon profile-action-icon--messages" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M5 6.5A3.5 3.5 0 018.5 3h7A3.5 3.5 0 0119 6.5v7A3.5 3.5 0 0115.5 17H10l-4.5 3.5V17H8.5A3.5 3.5 0 015 13.5v-7z" stroke="currentColor" stroke-width="1.75" stroke-linejoin="round"/>
                        </svg>
                    </span>
                    <span class="profile-action-label">Mesaj Gönder</span>
                </a>

                <button type="button" class="profile-action-btn profile-action-btn--report" id="openReportDialog" aria-haspopup="dialog">
                    <span class="profile-action-icon profile-action-icon--report" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 9v4" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"/>
                            <path d="M12 17h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            <path d="M10.3 3.6L2.6 17a2 2 0 001.7 3h15.4a2 2 0 001.7-3L13.7 3.6a2 2 0 00-3.4 0z" stroke="currentColor" stroke-width="1.75" stroke-linejoin="round"/>
                        </svg>
                    </span>
                    <span class="profile-action-label">Şikayet Et</span>
                </button>
            </div>
        </div>
    </div>

    @if($posts->isNotEmpty())
    <div class="user-profile-posts">
        <h2 class="user-profile-posts-title">Gönderiler</h2>
        <div class="user-profile-grid">
            @foreach($posts as $post)
            <div class="user-profile-grid-item">
                @if($post->image_url)
                    <img src="{{ $post->image_url }}" alt="Gönderi">
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @else
    <p class="feed-empty">Henüz gönderi yok.</p>
    @endif
</div>

<dialog id="reportDialog" class="profile-report-dialog">
    <form method="POST" action="{{ route('users.report', $user->username) }}" class="profile-report-form">
        @csrf
        <header class="profile-report-header">
            <h2>{{ $user->username }} — Şikayet Et</h2>
            <button type="button" class="profile-report-close" data-close-report aria-label="Kapat">×</button>
        </header>
        <p class="profile-report-hint">Uygunsuz davranış veya profil içeriği hakkında moderasyon ekibimize bildirin.</p>
        <label for="report_reason" class="sr-only">Şikayet sebebi</label>
        <textarea
            id="report_reason"
            name="reason"
            class="profile-report-input {{ $errors->has('reason') ? 'profile-report-input--error' : '' }}"
            rows="4"
            maxlength="1000"
            placeholder="Şikayet sebebinizi yazın…"
            required
        >{{ old('reason') }}</textarea>
        @error('reason') <small class="form-error">{{ $message }}</small> @enderror
        <footer class="profile-report-footer">
            <button type="button" class="btn btn-outline btn-sm" data-close-report>İptal</button>
            <button type="submit" class="btn btn-primary btn-sm profile-report-submit">Gönder</button>
        </footer>
    </form>
</dialog>

<script>
(function () {
    const dialog = document.getElementById('reportDialog');
    const openBtn = document.getElementById('openReportDialog');
    if (!dialog || !openBtn) return;

    openBtn.addEventListener('click', function () { dialog.showModal(); });
    document.querySelectorAll('[data-close-report]').forEach(function (el) {
        el.addEventListener('click', function () { dialog.close(); });
    });

    @if($errors->has('reason'))
    dialog.showModal();
    @endif
})();
</script>
@endsection
