@extends('layouts.app-with-sidebar')

@php $activeNav = 'messages'; @endphp

@section('title', 'Mesajlar — Gönül Köprüsü')

@section('app-content')
<div class="messages-page">
    <header class="messages-page-header">
        <h1>Mesajlar</h1>
        <p class="messages-page-subtitle">Karşı cinsle özel mesajlaşma</p>
    </header>

    @if($conversations->isNotEmpty())
    <ul class="conversation-list">
        @foreach($conversations as $conversation)
        @php $user = $conversation['user']; @endphp
        <li>
            <a href="{{ route('messages.show', $user->username) }}" class="conversation-item {{ $conversation['unread_count'] > 0 ? 'conversation-item--unread' : '' }}">
                <div class="conversation-avatar">
                    @if($user->profile_photo_url)
                        <img src="{{ $user->profile_photo_url }}" alt="{{ $user->username }}">
                    @else
                        {{ strtoupper(substr($user->username, 0, 1)) }}
                    @endif
                </div>
                <div class="conversation-body">
                    <div class="conversation-top">
                        <span class="conversation-name">{{ $user->username }}</span>
                        @if($conversation['last_message_at'])
                            <time class="conversation-time" datetime="{{ $conversation['last_message_at']->toIso8601String() }}">
                                {{ $conversation['last_message_at']->format('d.m.Y H:i') }}
                            </time>
                        @endif
                    </div>
                    <p class="conversation-preview">{{ Str::limit($conversation['last_message'], 80) }}</p>
                    @if(($conversation['message_count'] ?? 0) > 1)
                        <p class="conversation-meta">{{ $conversation['message_count'] }} mesaj</p>
                    @endif
                </div>
                @if($conversation['unread_count'] > 0)
                    <span class="conversation-badge" aria-label="{{ $conversation['unread_count'] }} okunmamış mesaj">{{ $conversation['unread_count'] }}</span>
                @endif
            </a>
        </li>
        @endforeach
    </ul>
    @else
    <div class="messages-empty">
        <p>Henüz mesajınız yok.</p>
        <p class="messages-empty-hint">Bir profilden <strong>Mesaj Gönder</strong> ile sohbet başlatabilirsiniz.</p>
        <a href="{{ route('feed') }}" class="btn btn-primary btn-sm">Akışa Git</a>
    </div>
    @endif
</div>
@endsection
