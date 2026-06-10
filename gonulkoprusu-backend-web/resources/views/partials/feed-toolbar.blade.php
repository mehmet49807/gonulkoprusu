<div class="feed-toolbar">
    <button type="button" class="feed-create-btn" data-open-compose="post" aria-label="Gönderi ekle">
        <span class="feed-create-icon">+</span>
        <span>Gönderi</span>
    </button>
    @if($viewer->canPostStories())
    <button type="button" class="feed-create-btn feed-create-btn--story" data-open-compose="story" aria-label="Hikaye ekle">
        <span class="feed-create-icon">✦</span>
        <span>Hikaye</span>
    </button>
    @elseif($viewer->gender === 'male')
    <a href="{{ route('premium') }}" class="feed-create-btn feed-create-btn--premium" aria-label="Premium paketler">
        <span class="feed-create-icon">★</span>
        <span>Premium</span>
    </a>
    @endif
</div>

@if($viewer->isOnTrial())
<div class="premium-feed-banner premium-feed-banner--trial">
    <p><strong>Deneme süresi:</strong> {{ $viewer->trialDaysRemaining() }} gün kaldı ({{ $viewer->trial_ends_at->format('d.m.Y') }}'e kadar)</p>
    <a href="{{ route('premium') }}" class="btn btn-outline btn-sm">Paketleri Gör</a>
</div>
@elseif($viewer->gender === 'male' && !$viewer->canPostStories())
<div class="premium-feed-banner">
    <p>Deneme süreniz bitti. Premium için <strong>Android veya iOS</strong> uygulamasını kullanın.</p>
    <a href="{{ route('premium') }}" class="btn btn-primary btn-sm">Paketleri İncele</a>
</div>
@endif
