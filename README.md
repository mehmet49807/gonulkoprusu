# Gönül Köprüsü

Gönül Köprüsü, topluluk yardımlaşması için geliştirilmiş bir şikayet ve bildirim platformudur. Kullanıcılar şikayet oluşturur, yönetici bu şikayetleri sonuçlandırır ve sonuç kullanıcıya bildirim olarak iletilir.

## Özellikler

- Kullanıcı girişi ve rol tabanlı görünüm
- Kullanıcıların şikayet oluşturması ve durum takibi
- Yönetici panelinden şikayet sonuçlandırma
- Sonuçların kullanıcıya bildirim olarak gönderilmesi
- Premium kullanıcı yönetimi
- Mesaj moderasyonu
- SQLite tabanlı demo veri deposu

## Teknolojiler

- React + Vite frontend
- Express REST API
- better-sqlite3 veritabanı
- npm workspaces
- GitHub Actions ile FTP deploy

## Proje yapısı

| Klasör | Açıklama |
| --- | --- |
| `client/` | React + Vite SPA |
| `server/` | Express API ve SQLite şeması |
| `.github/workflows/` | GitHub Actions deploy akışı |
| `DEPLOYMENT.md` | Canlıya alma notları |

## Kurulum

```bash
npm install
```

## Yerel geliştirme

API ve frontend'i birlikte başlatmak için:

```bash
npm run dev
```

Servisler:

- Frontend: `http://localhost:5173`
- API: `http://localhost:4000`

Tek tek çalıştırmak için:

```bash
npm run dev:server
npm run dev:client
```

## Demo kullanıcıları

Veritabanı ilk çalıştırmada otomatik oluşturulur ve aşağıdaki demo kullanıcıları eklenir:

| Kullanıcı adı | Rol |
| --- | --- |
| `admin` | Yönetici |
| `ayse` | Kullanıcı |
| `mehmet` | Kullanıcı |

Giriş demo amaçlıdır ve parola istemez.

## Temel akış

1. `ayse` veya `mehmet` ile giriş yapın.
2. Yeni bir şikayet oluşturun.
3. `admin` ile giriş yapın.
4. Şikayeti sonuçlandırın.
5. Kullanıcı hesabına dönün ve bildirim zilinde sonucu görün.

Frontend bildirimleri belirli aralıklarla yeniler, bu yüzden sonuçlar sayfa yenilemeden görünebilir.

## Komutlar

| Komut | Açıklama |
| --- | --- |
| `npm run dev` | API ve frontend'i birlikte başlatır |
| `npm run dev:server` | Yalnızca Express API'yi başlatır |
| `npm run dev:client` | Yalnızca Vite frontend'i başlatır |
| `npm run build` | Frontend production build alır |
| `npm run lint` | Server ve client lint kontrollerini çalıştırır |
| `npm start` | Production server başlatır |

## Deploy

GitHub Actions, `main` branch'ine push geldiğinde frontend'i build eder ve FTP ile yalnızca ana web sitesini `/public_html/` klasörüne yükler.

Gerekli GitHub Secrets:

- `FTP_PUBLIC_SERVER`
- `FTP_PUBLIC_USERNAME`
- `FTP_PUBLIC_PASSWORD`

Opsiyonel değişken:

- `VITE_API_BASE`: Canlı backend API adresi

Detaylar için `DEPLOYMENT.md` dosyasına bakın.

## Backend notu

Frontend statik olarak FTP hostinge yüklenebilir. Express + SQLite backend ise standart paylaşımlı FTP hosting üzerinde yalnızca dosya yükleyerek çalışmaz. Canlı ortamda login, şikayet ve bildirim akışlarının çalışması için backend'in Node.js destekli ayrı bir ortamda yayınlanması gerekir.

## Lisans

Bu proje özel bir demo uygulamasıdır.