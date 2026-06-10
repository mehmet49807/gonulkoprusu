@extends('layouts.app-with-sidebar')

@php $activeNav = 'notifications'; @endphp

@section('title', 'Bildirimler — Gönül Köprüsü')

@section('app-content')
<div class="notifications-page">
    <header class="notifications-header">
        <h1>Bildirimler</h1>
        <p class="notifications-subtitle">Yönetim duyuruları — bildirimler 24 saat sonra otomatik silinir</p>
    </header>

    @if($items->isNotEmpty())
    <ul class="notification-list">
        @foreach($items as $item)
        <li class="notification-item {{ $item['is_read'] ? '' : 'notification-item--unread' }}">
            <div class="notification-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 3a5 5 0 00-5 5v3.5L5 14.5h14l-2-3V8a5 5 0 00-5-5z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                    <path d="M10 18a2 2 0 004 0" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                </svg>
            </div>
            <div class="notification-body">
                <div class="notification-top">
                    <span class="notification-tag">Duyuru</span>
                    <time datetime="{{ $item['created_at']->toIso8601String() }}">
                        {{ $item['created_at']->format('d.m.Y H:i') }}
                    </time>
                </div>
                <h2 class="notification-title">{{ $item['title'] }}</h2>
                <p class="notification-text">{{ $item['message_text'] }}</p>
            </div>
        </li>
        @endforeach
    </ul>
    @else
    <div class="notifications-empty">
        <div class="notifications-empty-icon" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 3a5 5 0 00-5 5v3.5L5 14.5h14l-2-3V8a5 5 0 00-5-5z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                <path d="M10 18a2 2 0 004 0" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
            </svg>
        </div>
        <p>Henüz bildiriminiz yok.</p>
        <p class="notifications-empty-hint">Yönetim duyuruları burada görünecek.</p>
    </div>
    @endif
</div>
@endsection
