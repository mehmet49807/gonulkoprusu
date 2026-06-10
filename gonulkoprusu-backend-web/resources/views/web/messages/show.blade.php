@extends('layouts.app-with-sidebar')

@php $activeNav = 'messages'; @endphp

@section('title', $partner->username . ' — Mesajlar')

@section('app-content')
<div class="chat-page">
    <header class="chat-header">
        <a href="{{ route('messages.index') }}" class="chat-back" aria-label="Mesajlara dön">←</a>
        <a href="{{ route('users.show', $partner->username) }}" class="chat-partner">
            <div class="chat-partner-avatar">
                @if($partner->profile_photo_url)
                    <img src="{{ $partner->profile_photo_url }}" alt="{{ $partner->username }}">
                @else
                    {{ strtoupper(substr($partner->username, 0, 1)) }}
                @endif
            </div>
            <div class="chat-partner-info">
                <span class="chat-partner-name">{{ $partner->username }}</span>
                <span class="chat-partner-location">
                    @include('partials.location-link', [
                        'country' => $partner->country ?? 'Türkiye',
                        'city' => $partner->city,
                        'district' => $partner->district,
                    ])
                </span>
            </div>
        </a>
    </header>

    <div class="chat-messages" id="chat-messages">
        @forelse($messages as $message)
        <div class="chat-bubble {{ $message->sender_id === $viewer->id ? 'chat-bubble--sent' : 'chat-bubble--received' }}">
            <p class="chat-bubble-text">{{ $message->message_text }}</p>
            <time class="chat-bubble-time" datetime="{{ $message->created_at->toIso8601String() }}">
                {{ $message->created_at->format('d.m.Y H:i') }}
            </time>
        </div>
        @empty
        <p class="chat-empty">Henüz mesaj yok. İlk mesajı siz gönderin.</p>
        @endforelse
    </div>

    <form method="POST" action="{{ route('messages.store', $partner->username) }}" class="chat-compose">
        @csrf
        <label for="message_text" class="sr-only">Mesaj</label>
        <textarea
            id="message_text"
            name="message_text"
            class="chat-input {{ $errors->has('message_text') ? 'chat-input--error' : '' }}"
            rows="2"
            maxlength="2000"
            placeholder="Mesajınızı yazın…"
            required
        >{{ old('message_text') }}</textarea>
        <button type="submit" class="btn btn-primary chat-send">Gönder</button>
        @error('message_text') <small class="form-error chat-error">{{ $message }}</small> @enderror
    </form>
</div>

<script>
document.getElementById('chat-messages')?.scrollTo(0, document.getElementById('chat-messages').scrollHeight);
</script>
@endsection
