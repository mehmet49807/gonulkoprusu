@extends('layouts.app')

@section('title', 'Şikayet ve Engelleme — Gönül Köprüsü')

@section('content')
<article class="legal-page">
    <header class="legal-hero">
        <span class="legal-kicker">Güvenlik</span>
        <h1>Şikayet ve Engelleme</h1>
        <p class="legal-updated">Son güncelleme: {{ $lastUpdated }}</p>
        <p class="legal-intro">
            Gönül Köprüsü'nde kendinizi rahatsız hissettiğinizde veya kurallara aykırı davranış gördüğünüzde
            şikayet edebilir ve kullanıcıları engelleyebilirsiniz. Moderasyon ekibimiz bildirimleri inceler.
        </p>
    </header>

    <div class="legal-sections">
        <section class="legal-section">
            <h2>1. Kullanıcı Şikayeti</h2>
            <p>Aşağıdaki durumlarda bir kullanıcıyı şikayet edebilirsiniz:</p>
            <ul>
                <li>Taciz, hakaret veya tehdit içeren mesajlar</li>
                <li>Sahte profil veya yanıltıcı bilgiler</li>
                <li>Uygunsuz veya müstehcen fotoğraf / içerik</li>
                <li>Spam, dolandırıcılık veya para talebi</li>
                <li>Reşit olmayan kişi şüphesi</li>
                <li>Platform kurallarına aykırı diğer davranışlar</li>
            </ul>
        </section>

        <section class="legal-section">
            <h2>2. Şikayet Nasıl Yapılır?</h2>
            <p><strong>Web sitesinde:</strong></p>
            <ul>
                <li>Kullanıcı profiline gidin</li>
                <li><strong>«Şikayet Et»</strong> butonuna tıklayın</li>
                <li>Şikayet nedeninizi açık ve detaylı yazın</li>
                <li>Gönderin — moderasyon ekibine iletilir</li>
            </ul>
            <p><strong>Mobil uygulamada:</strong></p>
            <ul>
                <li>Kullanıcı profilini açın</li>
                <li>Şikayet seçeneğini kullanın ve gerekçenizi belirtin</li>
            </ul>
            <p>
                Acil ve ciddi durumlarda ayrıca <a href="mailto:{{ $contactEmail }}">{{ $contactEmail }}</a>
                adresine e-posta gönderebilirsiniz.
            </p>
        </section>

        <section class="legal-section">
            <h2>3. Engelleme</h2>
            <p>Bir kullanıcıyı engellediğinizde:</p>
            <ul>
                <li>Size mesaj gönderemez</li>
                <li>Profilinizi ve içeriklerinizi göremez</li>
                <li>Siz de o kullanıcının profilini ve mesajlarını görmezsiniz</li>
                <li>Engelleme karşı tarafa bildirilmez</li>
            </ul>
            <p>
                Engelleme, şikayetin yerini tutmaz. Ciddi ihlaller için mutlaka şikayet de oluşturun;
                böylece moderasyon gerekli işlemi yapabilir.
            </p>
        </section>

        <section class="legal-section">
            <h2>4. Moderasyon Süreci</h2>
            <ul>
                <li>Şikayetler moderasyon ekibi tarafından incelenir</li>
                <li>İhlal tespit edilirse uyarı, geçici veya kalıcı hesap kapatma uygulanabilir</li>
                <li>Ciddi suç şüphesi durumunda yetkili makamlara bildirim yapılabilir</li>
                <li>İnceleme süresi şikayet yoğunluğuna göre değişir; acil vakalar önceliklendirilir</li>
            </ul>
        </section>

        <section class="legal-section">
            <h2>5. Yanlış Şikayetler</h2>
            <p>
                Kasıtlı olarak yanıltıcı veya kötü niyetli şikayetler, şikayet eden hesabın
                kısıtlanmasına veya kapatılmasına yol açabilir. Lütfen yalnızca gerçek ihlalleri bildirin.
            </p>
        </section>

        <section class="legal-section">
            <h2>6. İtiraz</h2>
            <p>
                Hesabınız haksız yere kapatıldığını düşünüyorsanız
                <a href="mailto:{{ $contactEmail }}">{{ $contactEmail }}</a> adresine kullanıcı adınız
                ve açıklamanızla birlikte başvurabilirsiniz.
            </p>
        </section>
    </div>

    @include('partials.legal-footer')
</article>
@endsection
