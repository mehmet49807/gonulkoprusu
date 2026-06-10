@extends('layouts.app')

@section('title', 'Kullanım Koşulları — Gönül Köprüsü')

@section('content')
<article class="legal-page">
    <header class="legal-hero">
        <span class="legal-kicker">Yasal</span>
        <h1>Kullanım Koşulları</h1>
        <p class="legal-updated">Son güncelleme: {{ $lastUpdated }}</p>
        <p class="legal-intro">
            Gönül Köprüsü web sitesi ve mobil uygulamalarını kullanarak aşağıdaki koşulları kabul etmiş sayılırsınız.
            Lütfen kayıt olmadan veya hizmeti kullanmadan önce bu metni dikkatlice okuyun.
        </p>
    </header>

    <div class="legal-sections">
        <section class="legal-section">
            <h2>1. Hizmet Tanımı</h2>
            <p>
                Gönül Köprüsü, Türkiye'deki yetişkin kullanıcıların karşı cinsiyetten üyelerle tanışmasını,
                mesajlaşmasını ve güvenli bir ortamda iletişim kurmasını sağlayan bir tanışma platformudur.
                Hizmet; web (<strong>gonulkoprusu.com</strong>), Android ve iOS uygulamaları üzerinden sunulur.
            </p>
        </section>

        <section class="legal-section">
            <h2>2. Üyelik Şartları</h2>
            <ul>
                <li>Platform yalnızca <strong>18 yaş ve üzeri</strong> bireylere açıktır.</li>
                <li>Kayıt sırasında verdiğiniz bilgilerin doğru ve güncel olması gerekir.</li>
                <li>Her kullanıcı yalnızca <strong>bir hesap</strong> oluşturabilir.</li>
                <li>Hesabınızın güvenliğinden ve şifrenizin gizliliğinden siz sorumlusunuz.</li>
                <li>Hesabınızı üçüncü kişilere devredemez veya satamazsınız.</li>
            </ul>
        </section>

        <section class="legal-section">
            <h2>3. Kabul Edilebilir Kullanım</h2>
            <p>Platformu kullanırken aşağıdaki kurallara uymayı kabul edersiniz:</p>
            <ul>
                <li>Diğer üyelere saygılı ve nezaketli davranmak</li>
                <li>Gerçek kimliğinizi yansıtan profil ve içerikler paylaşmak</li>
                <li>Yasadışı, müstehcen, nefret söylemi içeren veya taciz niteliğinde içerik paylaşmamak</li>
                <li>Spam, dolandırıcılık, sahte profil veya ticari reklam amaçlı kullanım yapmamak</li>
                <li>Diğer kullanıcıların kişisel bilgilerini izinsiz paylaşmamak</li>
            </ul>
        </section>

        <section class="legal-section">
            <h2>4. İçerik ve Fikri Mülkiyet</h2>
            <p>
                Yüklediğiniz fotoğraf, metin ve diğer içeriklerin size ait olduğunu veya paylaşma hakkına
                sahip olduğunuzu beyan edersiniz. İçerikleriniz platformda görüntülenebilir; hizmetin sunulması
                için gerekli teknik işlemler (depolama, önbellekleme vb.) yapılabilir.
            </p>
            <p>
                Gönül Köprüsü markası, logosu, arayüz tasarımı ve yazılımı platforma aittir; izinsiz
                kopyalanamaz veya ticari amaçla kullanılamaz.
            </p>
        </section>

        <section class="legal-section">
            <h2>5. Premium Hizmetler</h2>
            <ul>
                <li>Premium paketler (Pro, Gold, Platinum) ek özellikler sunar; süre ve fiyatlar uygulama içinde belirtilir.</li>
                <li>Erkek üyeler kayıt sonrası sınırlı süreli deneme hakkından yararlanabilir.</li>
                <li>Ödemeler mobil uygulama mağazaları (Google Play / App Store) üzerinden işlenir.</li>
                <li>İade koşulları ilgili mağaza politikalarına tabidir.</li>
            </ul>
        </section>

        <section class="legal-section">
            <h2>6. Hesap Askıya Alma ve Sonlandırma</h2>
            <p>
                Kullanım koşullarını ihlal eden, şikayet alan veya güvenlik riski oluşturan hesaplar
                uyarı yapılmaksızın askıya alınabilir veya kalıcı olarak kapatılabilir. Gönül Köprüsü,
                hizmeti herhangi bir zamanda değiştirme veya sonlandırma hakkını saklı tutar.
            </p>
        </section>

        <section class="legal-section">
            <h2>7. Sorumluluk Sınırı</h2>
            <p>
                Platform, kullanıcılar arasındaki iletişim ve yüz yüze buluşmalardan doğrudan sorumlu değildir.
                Hizmet «olduğu gibi» sunulur; kesintisiz veya hatasız çalışacağı garanti edilmez.
                Yasal çerçevede izin verilen azami ölçüde sorumluluk sınırlandırılmıştır.
            </p>
        </section>

        <section class="legal-section">
            <h2>8. Uygulanacak Hukuk</h2>
            <p>
                Bu koşullar Türkiye Cumhuriyeti kanunlarına tabidir. Uyuşmazlıklarda Türkiye mahkemeleri
                ve icra daireleri yetkilidir.
            </p>
        </section>

        <section class="legal-section">
            <h2>9. Değişiklikler</h2>
            <p>
                Kullanım koşulları güncellenebilir. Güncel metin bu sayfada yayımlanır.
                Önemli değişiklikler sitede veya uygulama içinde duyurulur.
            </p>
        </section>
    </div>

    @include('partials.legal-footer')
</article>
@endsection
