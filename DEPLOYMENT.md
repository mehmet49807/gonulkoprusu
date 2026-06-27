# Yükleme (Deployment) Rehberi — Gönül Köprüsü

Bu proje GitHub'a her `main` push'unda **otomatik olarak FTP ile** yalnızca ana web sitesini yükler:

| Site | Build hedefi | FTP klasörü | Adres |
| --- | --- | --- | --- |
| Web sitesi | `VITE_APP_TARGET=public` | `/public_html` | https://www.gonulkoprusu.com |

Akış: `.github/workflows/deploy.yml` → frontend'i derler (`client/dist`) → `SamKirkland/FTP-Deploy-Action` ile sunucuya yükler.

Hosting FTP sunucusu FTPS/TLS komutunu desteklemediği için workflow `protocol: ftp` kullanır.
Yönetici paneli bu workflow tarafından yüklenmez; otomatik yükleme yalnızca `/public_html` klasörüne yapılır.
FTP'ye yüklenen test dosyaları canlı URL'de 404 dönüyorsa verilen FTP hesabı canlı yayın köküne bağlı değildir. Bu durumda hosting panelinden doğru FTP hesabını veya `/public_html` yayın klasörünü seçin.

---

## 1) ÖNEMLİ — Güvenlik

- **FTP parolaları sohbet içinde açık metin olarak paylaşıldı. Lütfen hosting panelinden (cPanel/Plesk) bu FTP parolalarını HEMEN değiştirin.**
- Parola **asla repoya yazılmaz**; yalnızca GitHub Secrets içinde saklanır.
- Parola değiştirildikten sonra ana web sitesi FTP bilgisini GitHub Secrets'a tekrar girin.

---

## 2) GitHub Secrets / Variables Ayarları

GitHub repo → **Settings → Secrets and variables → Actions**.

### Secrets (zorunlu)

Ana web sitesi (`/public_html`) için:

| Adı | Değer |
| --- | --- |
| `FTP_PUBLIC_SERVER` | FTP sunucusu (opsiyonel; boşsa `ftp.gonulkoprusu.com`) |
| `FTP_PUBLIC_USERNAME` | Ana site FTP kullanıcı adı (opsiyonel; boşsa `panel@admin.gonulkoprusu.com`) |
| `FTP_PUBLIC_PASSWORD` | Ana site FTP parolası |

> Eski kurulumlarla uyumluluk için workflow hâlâ `FTP_SERVER`, `FTP_USERNAME` ve `FTP_PASSWORD` secret'larını fallback olarak okuyabilir. Yeni kurulumda ana site için `FTP_PUBLIC_*` secret adlarını kullanın.

### Variables (opsiyonel)

| Adı | Açıklama |
| --- | --- |
| `VITE_API_BASE` | Backend API adresi (örn. `https://api.gonulkoprusu.com`). Boş bırakılırsa frontend kendi alan adındaki `/api`'yi çağırır. |

Secrets eklendikten sonra: **Actions** sekmesi → **Deploy website (FTP)** → **Run workflow** ile elle de tetikleyebilirsiniz; ya da `main`'e push yapın.

---

## 3) Backend ile ilgili kritik nokta

Frontend statik dosyaları paylaşımlı FTP hostinge yüklenir ve çalışır.

Ancak bu projenin **backend'i Node.js + Express + SQLite** ile yazılıdır. Tipik paylaşımlı FTP hosting (Pure-FTPd/cPanel) yalnızca statik dosya ve genelde PHP çalıştırır; **Node.js uygulamasını sadece FTP'ye dosya atarak çalıştıramazsınız.** Giriş/şikayet/bildirim işlemleri bir API gerektirdiğinden, canlı sitenin tam çalışması için backend'in bir yerde çalışması gerekir. Seçenekler:

1. **Backend'i ayrı bir yerde barındırın** (Render, Railway, Fly.io veya Node destekli bir VPS). Sonra `VITE_API_BASE` variable'ını o adrese ayarlayın. Frontend FTP'ye, backend ayrı sunucuya gider.
2. **cPanel "Setup Node.js App"** özelliği hostinginizde varsa, backend orada çalıştırılabilir (SQLite yerine kalıcı disk/MySQL gerekebilir).
3. **Backend'i PHP + MySQL'e dönüştürün.** Paylaşımlı hosting neredeyse her zaman PHP + MySQL destekler; bu durumda her şey tek hostingde çalışır. (Bunu yapmamı isterseniz MySQL veritabanı bilgileri gerekir.)

Şu anki pipeline yalnızca ana web sitesi frontend'ini otomatik yükler; backend için yukarıdaki seçeneklerden birini belirlemeniz gerekir.

---

## 4) Yerel derleme (kontrol için)

```bash
npm ci
VITE_APP_TARGET=public npm run build --workspace client   # client/dist -> /public_html
```
