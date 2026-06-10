@extends('layouts.app')

@section('title', 'Güvenli Tanışma — Gönül Köprüsü')

@section('content')
<article class="legal-page">
    <header class="legal-hero">
        <span class="legal-kicker">Güvenlik</span>
        <h1>Güvenli Tanışma</h1>
        <p class="legal-updated">Son güncelleme: {{ $lastUpdated }}</p>
        <p class="legal-intro">
            Gönül Köprüsü'nde online tanışma güzel bir başlangıç olabilir; ancak yüz yüze buluşurken
            güvenliğiniz her şeyden önemlidir. Aşağıdaki önerileri dikkate almanızı rica ederiz.
        </p>
    </header>

    <div class="legal-sections">
        <section class="legal-section">
            <h2>1. İlk Mesajlaşma</h2>
            <ul>
                <li>Kişisel bilgilerinizi (adres, iş yeri, finansal bilgiler) hemen paylaşmayın</li>
                <li>Platform dışına çok hızlı yönlendirme isteyenlere dikkat edin</li>
                <li>Para, hediye kartı veya yatırım taleplerine kesinlikle yanıt vermeyin</li>
                <li>Rahatsız edici davranışta <strong>engelleyin</strong> ve <strong>şikayet edin</strong></li>
            </ul>
        </section>

        <section class="legal-section">
            <h2>2. Buluşmaya Karar Vermeden Önce</h2>
            <ul>
                <li>Karşı tarafı platform üzerinden yeterince tanıyın</li>
                <li>Görüntülü görüşme ile kimliğe dair güven oluşturun</li>
                <li>Profil fotoğrafı ve bilgilerin tutarlı olup olmadığını gözlemleyin</li>
                <li>Bir arkadaşınıza buluşma planınızı bildirin</li>
            </ul>
        </section>

        <section class="legal-section">
            <h2>3. İlk Buluşma İpuçları</h2>
            <ul>
                <li><strong>Kalabalık ve halka açık</strong> bir yerde buluşun (kafe, AVM, park)</li>
                <li>İlk buluşmayı gündüz saatlerine planlayın</li>
                <li>Kendi ulaşımınızı ayarlayın; yabancı bir araçla gitmeyin</li>
                <li>Alkol tüketimini sınırlayın veya hiç tüketmeyin</li>
                <li>Çantanızı ve içeceğinizi gözetimsiz bırakmayın</li>
                <li>Buluşmayı erken bitirmek istediğinizde çekinmeden ayrılın</li>
            </ul>
        </section>

        <section class="legal-section">
            <h2>4. Reddedilmesi Gereken Durumlar</h2>
            <ul>
                <li>Ev, otel odası veya tenha mekânlarda buluşma teklifleri</li>
                <li>Acil para talepleri veya «yardım» hikâyeleri</li>
                <li>Kimlik doğrulamaktan kaçınan davranışlar</li>
                <li>Baskı, tehdit veya suçluluk hissi uyandıran iletişim</li>
            </ul>
            <p>Bu tür durumlarda buluşmayı reddedin, kullanıcıyı engelleyin ve şikayet edin.</p>
        </section>

        <section class="legal-section">
            <h2>5. Acil Durumlar</h2>
            <p>
                Kendinizi tehlikede hissediyorsanız önce <strong>112</strong>'yi arayın.
                Ardından <a href="mailto:{{ $contactEmail }}">{{ $contactEmail }}</a> üzerinden
                bize bilgi verebilirsiniz; moderasyon ekibimiz kayıtları inceleyebilir.
            </p>
        </section>

        <section class="legal-section">
            <h2>6. Platform Sorumluluğu</h2>
            <p>
                Gönül Köprüsü, kullanıcılar arasındaki yüz yüze buluşmaları organize etmez ve bu
                buluşmalardan doğan olaylardan sorumlu tutulamaz. Güvenliğiniz için yukarıdaki
                önerilere uymanız ve sağduyunuzu kullanmanız önemlidir.
            </p>
        </section>
    </div>

    @include('partials.legal-footer')
</article>
@endsection
