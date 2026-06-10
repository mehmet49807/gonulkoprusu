@extends('layouts.app')

@section('title', 'Gizlilik Politikası — Gönül Köprüsü')

@section('content')
<article class="legal-page">
    <header class="legal-hero">
        <span class="legal-kicker">Yasal</span>
        <h1>Gizlilik Politikası</h1>
        <p class="legal-updated">Son güncelleme: {{ $lastUpdated }}</p>
        <p class="legal-intro">
            Gönül Köprüsü olarak kişisel verilerinizin güvenliğine önem veriyoruz. Bu politika; web sitesi
            (<strong>gonulkoprusu.com</strong>) ve mobil uygulamalar (Android / iOS) kapsamında hangi verileri
            topladığımızı, nasıl kullandığımızı ve haklarınızı açıklar.
        </p>
    </header>

    <div class="legal-sections">
        <section class="legal-section" id="veri-sorumlusu">
            <h2>1. Veri Sorumlusu</h2>
            <p>
                6698 sayılı Kişisel Verilerin Korunması Kanunu (KVKK) kapsamında veri sorumlusu:
                <strong>Gönül Köprüsü</strong> — Türkiye.
            </p>
            <p>İletişim: <a href="mailto:{{ $contactEmail }}">{{ $contactEmail }}</a></p>
        </section>

        <section class="legal-section" id="toplanan-veriler">
            <h2>2. Toplanan Veriler</h2>
            <ul>
                <li><strong>Hesap bilgileri:</strong> kullanıcı adı, ad, soyad, e-posta, telefon, cinsiyet, şehir, ilçe</li>
                <li><strong>Profil içerikleri:</strong> profil fotoğrafı, gönderiler, hikayeler (premium üyeler)</li>
                <li><strong>Mesajlaşma:</strong> kullanıcılar arası metin mesajları</li>
                <li><strong>Premium:</strong> paket türü, ödeme referansı, abonelik süresi (mobil uygulama üzerinden)</li>
                <li><strong>Güvenlik:</strong> şikayet kayıtları, engelleme listesi, oturum bilgileri</li>
                <li><strong>Teknik veriler:</strong> IP adresi, tarayıcı/uygulama bilgisi, çerezler (web)</li>
            </ul>
        </section>

        <section class="legal-section" id="kullanim-amaclari">
            <h2>3. Verilerin Kullanım Amaçları</h2>
            <ul>
                <li>Üyelik oluşturma, kimlik doğrulama ve hesap yönetimi</li>
                <li>Karşı cins kullanıcılarla güvenli tanışma ve mesajlaşma hizmeti sunma</li>
                <li>Akış, beğeni, bildirim ve premium özelliklerin çalıştırılması</li>
                <li>Şikayet, ban ve moderasyon süreçlerinin yürütülmesi</li>
                <li>Yasal yükümlülüklerin yerine getirilmesi ve dolandırıcılığın önlenmesi</li>
                <li>Hizmet kalitesinin artırılması ve teknik destek sağlanması</li>
            </ul>
        </section>

        <section class="legal-section" id="hukuki-dayanak">
            <h2>4. Hukuki Dayanak</h2>
            <p>Kişisel verileriniz KVKK md. 5 kapsamında aşağıdaki hukuki sebeplere dayanılarak işlenir:</p>
            <ul>
                <li>Bir sözleşmenin kurulması veya ifası (üyelik sözleşmesi)</li>
                <li>Veri sorumlusunun meşru menfaati (platform güvenliği, moderasyon)</li>
                <li>Açık rızanız (gerekli hallerde, örn. pazarlama iletişimi)</li>
                <li>Hukuki yükümlülüklerin yerine getirilmesi</li>
            </ul>
        </section>

        <section class="legal-section" id="paylasim">
            <h2>5. Verilerin Paylaşımı</h2>
            <p>Kişisel verileriniz üçüncü taraflara <strong>satılmaz</strong>. Veriler yalnızca şu durumlarda paylaşılabilir:</p>
            <ul>
                <li>Yasal zorunluluk veya yetkili makam talebi</li>
                <li>Barındırma, e-posta ve teknik altyapı hizmet sağlayıcıları (gizlilik sözleşmeli)</li>
                <li>Mobil uygulama mağazaları (Google Play / App Store) ödeme doğrulama süreçleri</li>
            </ul>
            <p>Diğer kullanıcılara yalnızca <strong>profil görünürlük kuralları</strong> kapsamında bilgi gösterilir (kullanıcı adı, şehir, ilçe, fotoğraf vb.).</p>
        </section>

        <section class="legal-section" id="saklama">
            <h2>6. Saklama Süreleri</h2>
            <ul>
                <li>Hesap verileri: üyelik süresince ve hesap silindikten sonra yasal süreler boyunca</li>
                <li>Mesajlar: hizmetin sunulması için gerekli süre</li>
                <li>Yönetim bildirimleri: 24 saat sonra otomatik silinir</li>
                <li>Şikayet ve güvenlik kayıtları: mevzuat ve güvenlik gereksinimleri kadar</li>
            </ul>
        </section>

        <section class="legal-section" id="haklar">
            <h2>7. KVKK Kapsamındaki Haklarınız</h2>
            <p>KVKK md. 11 uyarınca;</p>
            <ul>
                <li>Verilerinizin işlenip işlenmediğini öğrenme</li>
                <li>İşlenmişse bilgi talep etme</li>
                <li>Amacına uygun kullanılıp kullanılmadığını öğrenme</li>
                <li>Eksik veya yanlış işlenmişse düzeltilmesini isteme</li>
                <li>Silinmesini veya yok edilmesini isteme</li>
                <li>İtiraz etme ve zararın giderilmesini talep etme</li>
            </ul>
            <p>Taleplerinizi <a href="mailto:{{ $contactEmail }}">{{ $contactEmail }}</a> adresine iletebilirsiniz.</p>
        </section>

        <section class="legal-section" id="guvenlik">
            <h2>8. Güvenlik</h2>
            <p>
                Verileriniz şifreli bağlantı (HTTPS), erişim kontrolleri ve güvenli sunucu altyapısı ile korunur.
                Şifreler hash algoritmaları ile saklanır; düz metin olarak tutulmaz.
            </p>
        </section>

        <section class="legal-section" id="cocuklar">
            <h2>9. Çocukların Gizliliği</h2>
            <p>
                Gönül Köprüsü 18 yaş altındaki kişilere yönelik değildir. Bilerek 18 yaş altından veri toplanmaz.
                Böyle bir durum tespit edilirse hesap kapatılır.
            </p>
        </section>

        <section class="legal-section" id="cerezler">
            <h2>10. Çerezler (Web)</h2>
            <p>
                Web sitemizde oturum yönetimi ve güvenlik için zorunlu çerezler kullanılır. Tarayıcı ayarlarınızdan
                çerezleri yönetebilirsiniz; ancak bazı özellikler çalışmayabilir.
            </p>
        </section>

        <section class="legal-section" id="mobil">
            <h2>11. Mobil Uygulama</h2>
            <p>
                Android ve iOS uygulamaları aynı hesap ve veritabanını kullanır. Oturum bilgisi cihazınızda güvenli
                şekilde saklanır. Uygulama izinleri (kamera, galeri vb.) yalnızca ilgili özellik kullanıldığında
                istenir.
            </p>
        </section>

        <section class="legal-section" id="degisiklikler">
            <h2>12. Politika Değişiklikleri</h2>
            <p>
                Bu politika güncellenebilir. Önemli değişiklikler sitede veya uygulama içinde duyurulur.
                Güncel metin her zaman bu sayfada yayımlanır.
            </p>
        </section>
    </div>

    @include('partials.legal-footer')
</article>
@endsection
