@extends('layouts.app')

@section('title', $locationLabel . ' — Gönül Köprüsü')

@section('content')
<div class="location-users-page">
    <header class="location-users-header">
        <h1>{{ $locationLabel }}</h1>
        <p class="location-users-subtitle">{{ $users->total() }} üye</p>
    </header>

    @if($users->isNotEmpty())
    <div class="location-users-grid">
        @foreach($users as $user)
        <a href="{{ route('users.show', $user->username) }}" class="location-user-card">
            <div class="location-user-avatar">
                @if($user->profile_photo_url)
                    <img src="{{ $user->profile_photo_url }}" alt="{{ $user->username }}">
                @else
                    {{ strtoupper(substr($user->username, 0, 1)) }}
                @endif
            </div>
            <span class="location-user-name">{{ $user->username }}</span>
        </a>
        @endforeach
    </div>

    {{ $users->links() }}
    @else
    <p class="feed-empty">Bu bölgede henüz üye bulunmuyor.</p>
    @endif
</div>
@endsection
