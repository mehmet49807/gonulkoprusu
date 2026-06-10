<footer class="site-footer" id="iletisim">
    <div class="footer-wave" aria-hidden="true"></div>

    <div class="footer-cta">
        <div class="container footer-cta-inner">
            <div class="footer-cta-text">
                <h3>Gerçek bağlar, güzel yarınlar.</h3>
                <p>Güvenli profiller, samimi sohbetler ve anlamlı ilişkiler seni bekliyor.</p>
            </div>
            @guest
                <a href="{{ route('register') }}" class="btn btn-primary footer-cta-btn">Ücretsiz Üye Ol</a>
            @else
                <a href="{{ route('feed') }}" class="btn btn-primary footer-cta-btn">Akışa Git</a>
            @endguest
        </div>
    </div>

    <div class="container">
        <div class="footer-grid">
            <div class="footer-brand">
                <a href="{{ route('home') }}" class="logo footer-logo">
                    @include('partials.logo')
                </a>
                <p class="footer-tagline">Türkiye'nin güvenli ve samimi tanışma platformu. Ciddi ilişki arayanları saygılı bir ortamda buluşturuyoruz.</p>
                <div class="footer-stats">
                    <div class="footer-stat">
                        <strong>10K+</strong>
                        <span>Aktif üye</span>
                    </div>
                    <div class="footer-stat">
                        <strong>500+</strong>
                        <span>Mutlu çift</span>
                    </div>
                    <div class="footer-stat">
                        <strong>%100</strong>
                        <span>Güvenli</span>
                    </div>
                </div>
            </div>

            <div class="footer-col">
                <h4>Keşfet</h4>
                <ul>
                    <li><a href="{{ route('home') }}">Ana Sayfa</a></li>
                    <li><a href="{{ route('about') }}">Hakkımızda</a></li>
                    @auth
                        <li><a href="{{ route('feed') }}">Akış</a></li>
                        <li><a href="{{ route('profile') }}">Profilim</a></li>
                    @else
                        <li><a href="{{ route('register') }}">Üye Ol</a></li>
                        <li><a href="{{ route('login') }}">Giriş Yap</a></li>
                    @endauth
                </ul>
            </div>

            <div class="footer-col">
                <h4>Güvenlik</h4>
                <ul>
                    <li><a href="{{ route('privacy') }}">Gizlilik Politikası</a></li>
                    <li><a href="{{ route('terms') }}">Kullanım Koşulları</a></li>
                    <li><a href="{{ route('complaints') }}">Şikayet &amp; Engelleme</a></li>
                    <li><a href="{{ route('safe-meeting') }}">Güvenli Tanışma</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h4>İletişim</h4>
                <ul class="footer-contact">
                    <li>
                        <svg class="footer-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M4 4h16a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2zm0 2v.01L12 13l8-6.99V6H4zm16 12V9.25l-8 6.99-8-6.99V18h16z"/></svg>
                        <a href="mailto:destek@gonulkoprusu.com">destek@gonulkoprusu.com</a>
                    </li>
                    <li>
                        <svg class="footer-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5A2.5 2.5 0 1 1 12 6a2.5 2.5 0 0 1 0 5.5z"/></svg>
                        <span>Türkiye</span>
                    </li>
                </ul>

                <p class="footer-apps-label">Mobil uygulamalar</p>
                @include('partials.store-badges', ['class' => 'store-badges--footer'])

                <div class="footer-social">
                    <a href="#" class="social-link" aria-label="Instagram">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 2h10a5 5 0 0 1 5 5v10a5 5 0 0 1-5 5H7a5 5 0 0 1-5-5V7a5 5 0 0 1 5-5zm10 2H7a3 3 0 0 0-3 3v10a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3V7a3 3 0 0 0-3-3zm-5 3.5A5.5 5.5 0 1 1 6.5 13 5.5 5.5 0 0 1 12 7.5zm0 2A3.5 3.5 0 1 0 15.5 13 3.5 3.5 0 0 0 12 9.5zM18 6.2a1.2 1.2 0 1 1-1.2 1.2A1.2 1.2 0 0 1 18 6.2z"/></svg>
                    </a>
                    <a href="#" class="social-link" aria-label="X">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M18.9 2H22l-6.8 7.8L23 22h-6.7l-5.2-6.8L5.2 22H2l7.3-8.4L1 2h6.9l4.7 6.2zM17.5 20h1.8L6.6 3.9H4.7z"/></svg>
                    </a>
                    <a href="#" class="social-link" aria-label="Facebook">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M13 22v-8h3l1-4h-3V8.5C14 7.1 14.5 6 16 6h2V2h-3c-3.3 0-5 2-5 5v3H7v4h3v8z"/></svg>
                    </a>
                </div>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copy; {{ date('Y') }} Gönül Köprüsü. Tüm hakları saklıdır.</p>
            <nav class="footer-legal">
                <a href="{{ route('privacy') }}">Gizlilik</a>
                <a href="{{ route('terms') }}">Koşullar</a>
                <a href="{{ route('about') }}">Hakkımızda</a>
                <a href="mailto:destek@gonulkoprusu.com">Destek</a>
            </nav>
            <p class="footer-made">Türkiye'de sevgiyle yapıldı</p>
        </div>
    </div>
</footer>
