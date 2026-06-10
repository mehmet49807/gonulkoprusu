@extends('layouts.app-with-sidebar')

@php $activeNav = 'feed'; @endphp

@section('title', 'Akış — Gönül Köprüsü')

@section('app-content')
@php
    $allStoryGroups = collect();
    if ($ownStoryGroup) {
        $allStoryGroups->push($ownStoryGroup);
    }
    $allStoryGroups = $allStoryGroups->merge($storyGroups);
@endphp

<div class="feed-container">
    @if($allStoryGroups->isNotEmpty())
    <section class="stories-section" aria-label="Hikayeler">
        <div class="stories-strip">
            @if($ownStoryGroup)
            <button type="button" class="story-item story-item--own" data-story-index="0" data-user-id="{{ $viewer->id }}" aria-label="Hikayeni görüntüle">
                <span class="story-ring story-ring--unseen story-ring--own">
                    <span class="story-avatar">
                        @if($viewer->profile_photo_url)
                            <img src="{{ $viewer->profile_photo_url }}" alt="">
                        @else
                            {{ strtoupper(substr($viewer->username, 0, 1)) }}
                        @endif
                    </span>
                </span>
                <span class="story-username">Hikayen</span>
            </button>
            @endif

            @foreach($storyGroups as $index => $group)
            @php $storyIndex = ($ownStoryGroup ? 1 : 0) + $index; @endphp
            <button type="button" class="story-item" data-story-index="{{ $storyIndex }}" data-user-id="{{ $group['user_id'] }}" aria-label="{{ $group['username'] }} hikayesi">
                <span class="story-ring story-ring--unseen">
                    <span class="story-avatar">
                        @if($group['profile_photo_url'])
                            <img src="{{ $group['profile_photo_url'] }}" alt="">
                        @else
                            {{ strtoupper(substr($group['username'], 0, 1)) }}
                        @endif
                    </span>
                </span>
                <span class="story-username">{{ $group['username'] }}</span>
            </button>
            @endforeach
        </div>
        @error('story') <small class="form-error story-error">{{ $message }}</small> @enderror
    </section>
    @endif

    @forelse($posts as $post)
    <article class="post-card {{ in_array($post->id, $likedPostIds) ? 'post-card--liked' : '' }}" data-post-id="{{ $post->id }}">
        <div class="post-header">
            <div class="post-header-user">
                <a href="{{ route('users.show', $post->user->username) }}" class="post-header-avatar" aria-hidden="true" tabindex="-1">
                    @if($post->user->profile_photo_url)
                        <img src="{{ $post->user->profile_photo_url }}" alt="">
                    @else
                        <span>{{ strtoupper(substr($post->user->username, 0, 1)) }}</span>
                    @endif
                </a>
                <a href="{{ route('users.show', $post->user->username) }}" class="post-username">{{ $post->user->username }}</a>
            </div>
            <div class="post-header-end">
                @include('partials.location-link', [
                    'country' => $post->user->country ?? 'Türkiye',
                    'city' => $post->user->city,
                    'district' => $post->user->district,
                    'class' => 'post-location',
                ])
                @if($post->user_id === $viewer->id)
                <div class="post-menu">
                    <button type="button" class="post-menu-btn" aria-label="Gönderi menüsü" data-post-menu>⋯</button>
                    <div class="post-menu-dropdown" hidden>
                        <form method="POST" action="{{ route('posts.destroy', $post) }}" onsubmit="return confirm('Bu gönderiyi silmek istediğinize emin misiniz?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="post-menu-delete">Sil</button>
                        </form>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <div class="post-image">
            @if($post->image_url)
                <img src="{{ $post->image_url }}" alt="Gönderi">
            @else
                Gönderi Görseli
            @endif
        </div>

        <div class="post-footer">
            @if($post->caption)
            <div class="post-caption">
                <a href="{{ route('users.show', $post->user->username) }}" class="post-caption-user">{{ $post->user->username }}</a>
                <span>{{ $post->caption }}</span>
            </div>
            @endif
            <div class="post-actions">
                <button type="button"
                    class="like-btn {{ in_array($post->id, $likedPostIds) ? 'like-btn--active' : '' }}"
                    data-like-url="{{ route('posts.like', $post) }}"
                    aria-label="Beğen"
                    aria-pressed="{{ in_array($post->id, $likedPostIds) ? 'true' : 'false' }}">
                    <span class="like-icon" aria-hidden="true">♥</span>
                    <span class="like-count">{{ $post->likes_count }}</span>
                </button>
            </div>
        </div>
    </article>
    @empty
    <p class="feed-empty">Henüz gönderi yok. İlk gönderiyi sen paylaş!</p>
    @endforelse

    {{ $posts->links() }}
</div>

@if($allStoryGroups->isNotEmpty())
<div class="ig-story-viewer" id="igStoryViewer" hidden data-groups="{{ $allStoryGroups->toJson() }}">
    <div class="ig-story-frame">
        <div class="ig-story-progress" id="igStoryProgress"></div>

        <header class="ig-story-header">
            <a href="#" id="igStoryUserLink" class="ig-story-user">
                <span class="ig-story-user-avatar" id="igStoryUserAvatar"></span>
                <span class="ig-story-user-meta">
                    <strong id="igStoryUserName"></strong>
                    <small id="igStoryTime">Şimdi</small>
                </span>
            </a>
            <div class="ig-story-header-actions">
                <button type="button" class="ig-story-delete" id="igStoryDelete" hidden aria-label="Hikayeyi sil">🗑</button>
                <button type="button" class="ig-story-close" data-close-story aria-label="Kapat">×</button>
            </div>
        </header>

        <div class="ig-story-stage" id="igStoryStage">
            <button type="button" class="ig-story-tap ig-story-tap--prev" id="igStoryTapPrev" aria-label="Önceki"></button>
            <div class="ig-story-media" id="igStoryMedia"></div>
            <button type="button" class="ig-story-tap ig-story-tap--next" id="igStoryTapNext" aria-label="Sonraki"></button>
        </div>
    </div>
</div>
@endif

<script src="{{ asset('js/feed.js') }}?v=feed-post-2"></script>
@if($allStoryGroups->isNotEmpty())
<script src="{{ asset('js/stories.js') }}?v=ig-stories-2"></script>
@endif
@endsection
