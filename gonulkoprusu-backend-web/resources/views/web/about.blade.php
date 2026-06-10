@extends('layouts.app')

@section('title', 'Hakkımızda — Gönül Köprüsü')

@section('content')
<article class="legal-page">
    <header class="legal-hero">
        <span class="legal-kicker">Gönül Köprüsü</span>
        <h1>Hakkımızda</h1>
        <p class="legal-updated">Son güncelleme: {{ $lastUpdated }}</p>
        <p class="legal-intro">
            Gönül Köprüsü, Türkiye'de ciddi ilişki ve anlamlı bağlar arayan yetişkinleri güvenli,
            saygılı ve modern bir ortamda buluşturan bir tanışma platformudur.
        </p>
    </header>

    <div class="legal-sections">
        <section class="legal-section">
            <h2>Misyonumuz</h2>
            <p>
                Teknolojiyi samimiyet ve güvenle birleştirerek insanların gerçek bağlar kurmasına
                aracılık etmek. Yüzeysel kaydırmalar yerine; profil, mesajlaşma ve güvenli tanışma
                kültürünü ön planda tutuyoruz.
            </p>
        </section>

        <section class="legal-section">
            <h2>Ne Sunuyoruz?</h2>
            <ul>
                <li><strong>Profil ve keşif:</strong> Şehir ve ilçe bazlı, karşı cinsiyetten üye keşfi</li>
                <li><strong>Akış ve gönderiler:</strong> Fotoğraf paylaşımı ve etkileşim</li>
                <li><strong>Hikayeler:</strong> 24 saatlik anlar (premium üyelik veya kadın hesapları)</li>
                <li><strong>Mesajlaşma:</strong> Üyeler arası özel ve güvenli sohbet</li>
                <li><strong>Premium paketler:</strong> Pro, Gold ve Platinum ile genişletilmiş özellikler</li>
                <li><strong>Mobil uygulamalar:</strong> Android ve iOS ile her yerden erişim</li>
            </ul>
        </section>

        <section class="legal-section">
            <h2>Güvenlik ve Moderasyon</h2>
            <p>
                Platformumuzda şikayet, engelleme ve moderasyon süreçleri aktiftir. Uygunsuz davranışlar
                incelenir; gerekli durumlarda hesaplar askıya alınır veya kapatılır. Kişisel verileriniz
                <a href="{{ route('privacy') }}">Gizlilik Politikamız</a> kapsamında korunur.
            </p>
        </section>

        <section class="legal-section">
            <h2>Kimler İçin?</h2>
            <p>
                Gönül Köprüsü, 18 yaş ve üzeri, Türkiye'de yaşayan ve karşı cinsiyetten ciddi tanışma
                arayan bireyler için tasarlanmıştır. Erkek üyeler kayıt sonrası sınırlı süreli deneme
                ile premium özellikleri deneyebilir; kadın üyeler temel hikaye özelliklerinden yararlanır.
            </p>
        </section>

        <section class="legal-section">
            <h2>İletişim</h2>
            <p>
                Soru, öneri ve destek talepleriniz için:
                <a href="mailto:{{ $contactEmail }}">{{ $contactEmail }}</a>
            </p>
            <p>
                Web: <a href="https://gonulkoprusu.com">gonulkoprusu.com</a><br>
                Mobil: Google Play ve App Store (yakında)
            </p>
        </section>

        <section class="legal-section">
            <h2>Vizyonumuz</h2>
            <p>
                Türkiye'nin en güvenilir tanışma platformu olmak; her eşleşmenin bir hikâyeye,
                her hikâyenin ise saygı ve güven üzerine kurulmasına katkı sağlamak.
            </p>
        </section>
    </div>

    @include('partials.legal-footer')
</article>
@endsection
