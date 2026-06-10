{{-- Gönderi oluşturma modalı --}}
<div class="ig-compose-modal" id="postComposeModal" hidden>
    <div class="ig-compose-backdrop" data-close-compose></div>
    <div class="ig-compose-sheet" role="dialog" aria-labelledby="postComposeTitle">
        <header class="ig-compose-header">
            <button type="button" class="ig-compose-close" data-close-compose aria-label="Kapat">×</button>
            <h2 id="postComposeTitle">Yeni Gönderi</h2>
            <button type="submit" form="postComposeForm" class="ig-compose-submit" id="postComposeSubmit" disabled>Paylaş</button>
        </header>
        <form method="POST" action="{{ route('posts.store') }}" enctype="multipart/form-data" id="postComposeForm" class="ig-compose-body">
            @csrf
            <div class="ig-compose-preview" id="postComposePreview">
                <label class="ig-compose-picker">
                    <input type="file" name="image" id="postImageInput" accept="image/*" required>
                    <span class="ig-compose-picker-icon">📷</span>
                    <span>Fotoğraf seç</span>
                </label>
            </div>
            <textarea name="caption" class="ig-compose-caption" maxlength="500" rows="3" placeholder="Bir açıklama yaz... (isteğe bağlı)"></textarea>
        </form>
    </div>
</div>

@if($viewer->canPostStories())
{{-- Hikaye oluşturma modalı --}}
<div class="ig-compose-modal" id="storyComposeModal" hidden>
    <div class="ig-compose-backdrop" data-close-compose></div>
    <div class="ig-compose-sheet ig-compose-sheet--story" role="dialog" aria-labelledby="storyComposeTitle">
        <header class="ig-compose-header">
            <button type="button" class="ig-compose-close" data-close-compose aria-label="Kapat">×</button>
            <h2 id="storyComposeTitle">Yeni Hikaye</h2>
            <button type="submit" form="storyComposeForm" class="ig-compose-submit" id="storyComposeSubmit" disabled>Paylaş</button>
        </header>
        <form method="POST" action="{{ route('stories.store') }}" enctype="multipart/form-data" id="storyComposeForm" class="ig-compose-body">
            @csrf
            <div class="ig-compose-preview ig-compose-preview--story" id="storyComposePreview">
                <label class="ig-compose-picker">
                    <input type="file" name="media" id="storyMediaInput" accept="image/*,video/*" required>
                    <span class="ig-compose-picker-icon">✦</span>
                    <span>Fotoğraf veya video seç</span>
                    <small>24 saat sonra kaybolur</small>
                </label>
            </div>
        </form>
    </div>
</div>
@endif
