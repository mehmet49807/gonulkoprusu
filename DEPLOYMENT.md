# Yükleme (Deployment) Rehberi — Gönül Köprüsü

Bu proje GitHub'a her `main` push'unda **otomatik olarak FTP ile** yüklenir:

| Site | Build hedefi | FTP klasörü | Adres |
| --- | --- | --- | --- |
| Web sitesi | `VITE_APP_TARGET=public` | FTP hesabının web kökü (`/`) | https://www.gonulkoprusu.com |
| Yönetici paneli | `VITE_APP_TARGET=admin` | `/admin.gonulkoprusu.com` | https://admin.gonulkoprusu.com |

Akış: `.github/workflows/deploy.yml` → frontend'i derler (`client/dist`) → `SamKirkland/FTP-Deploy-Action` ile sunucuya yükler.

Hosting FTP sunucusu FTPS/TLS komutunu desteklemediği için workflow `protocol: ftp` kullanır.
Web sitesi ve yönetici paneli deploy'unda `.htaccess` React `index.html` dosyasını öne alır; böylece hosting klasöründe eski `index.php` kalsa bile React uygulaması açılır.
Hosting `index.php` dosyasını zorunlu olarak çalıştırırsa diye her iki build de React girişini `index.php` olarak da yükler.
Eğer canlı `admin.gonulkoprusu.com` hâlâ eski PHP giriş ekranını gösterirse cPanel/Plesk'te subdomain document root ayarı `admin.gonulkoprusu.com/` klasörünü göstermiyor demektir. Document root'u bu klasöre bağlayın.
FTP'ye yüklenen test dosyaları canlı URL'de 404 dönüyorsa verilen FTP hesabı canlı yayın köküne bağlı değildir. Bu durumda hosting panelinden doğru FTP hesabını/yayın klasörünü seçin veya subdomain document root'u FTP'de deploy edilen klasöre yönlendirin.

---

## 1) ÖNEMLİ — Güvenlik

- **FTP parolaları sohbet içinde açık metin olarak paylaşıldı. Lütfen hosting panelinden (cPanel/Plesk) bu FTP parolalarını HEMEN değiştirin.**
- Parola **asla repoya yazılmaz**; yalnızca GitHub Secrets içinde saklanır.
- Ana site ve yönetici paneli ayrı FTP bilgileriyle çalışacak şekilde ayarlanmıştır. Sunucu veya kullanıcı adı aynı olsa bile parolaları ayrı secret olarak saklayın.

---

## 2) GitHub Secrets / Variables Ayarları

GitHub repo → **Settings → Secrets and variables → Actions**.

### Secrets (zorunlu)

Ana web sitesi (FTP hesabının web kökü) için:

| Adı | Değer |
| --- | --- |
| `FTP_PUBLIC_SERVER` | FTP sunucusu (opsiyonel; boşsa `ftp.gonulkoprusu.com`) |
| `FTP_PUBLIC_USERNAME` | Ana site FTP kullanıcı adı (opsiyonel; boşsa `web@gonulkoprusu.com`) |
| `FTP_PUBLIC_PASSWORD` | Ana site FTP parolası |

Yönetici paneli (`/admin.gonulkoprusu.com`) için:

| Adı | Değer |
| --- | --- |
| `FTP_ADMIN_SERVER` | FTP sunucusu (opsiyonel; boşsa `ftp.gonulkoprusu.com`) |
| `FTP_ADMIN_USERNAME` | Yönetici paneli FTP kullanıcı adı (opsiyonel; boşsa `panel@admin.gonulkoprusu.com`) |
| `FTP_ADMIN_PASSWORD` | Yönetici paneli FTP parolası |

> Eski kurulumlarla uyumluluk için workflow hâlâ `FTP_SERVER`, `FTP_USERNAME` ve `FTP_PASSWORD` secret'larını fallback olarak okuyabilir. Yeni kurulumda site bazlı secret adlarını kullanın; public ve admin parolaları birbirinin fallback'i değildir.
> Ana site ve yönetici paneli aynı FTP kullanıcı adını kullanıyorsa hosting tarafında tek geçerli parola olur. Bu durumda `FTP_PASSWORD` secret'ını tek ortak parola olarak girmeniz yeterlidir.

### Variables (opsiyonel)

| Adı | Açıklama |
| --- | --- |
| `VITE_API_BASE` | Backend API adresi (örn. `https://api.gonulkoprusu.com`). Boş bırakılırsa frontend kendi alan adındaki `/api`'yi çağırır. |

Secrets eklendikten sonra: **Actions** sekmesi → **Deploy (FTP)** → **Run workflow** ile elle de tetikleyebilirsiniz; ya da `main`'e push yapın.

---

## 3) Backend ile ilgili kritik nokta

Frontend (web sitesi + yönetici paneli) statik dosyalardır; paylaşımlı FTP hostinge sorunsuz yüklenir ve çalışır.

Ancak bu projenin **backend'i Node.js + Express + SQLite** ile yazılıdır. Tipik paylaşımlı FTP hosting (Pure-FTPd/cPanel) yalnızca statik dosya ve genelde PHP çalıştırır; **Node.js uygulamasını sadece FTP'ye dosya atarak çalıştıramazsınız.** Giriş/şikayet/bildirim işlemleri bir API gerektirdiğinden, canlı sitenin tam çalışması için backend'in bir yerde çalışması gerekir. Seçenekler:

1. **Backend'i ayrı bir yerde barındırın** (Render, Railway, Fly.io veya Node destekli bir VPS). Sonra `VITE_API_BASE` variable'ını o adrese ayarlayın. Frontend FTP'ye, backend ayrı sunucuya gider.
2. **cPanel "Setup Node.js App"** özelliği hostinginizde varsa, backend orada çalıştırılabilir (SQLite yerine kalıcı disk/MySQL gerekebilir).
3. **Backend'i PHP + MySQL'e dönüştürün.** Paylaşımlı hosting neredeyse her zaman PHP + MySQL destekler; bu durumda her şey tek hostingde çalışır. (Bunu yapmamı isterseniz MySQL veritabanı bilgileri gerekir.)

Şu anki pipeline frontend'i otomatik yükler; backend için yukarıdaki seçeneklerden birini belirlemeniz gerekir.

---

## 4) Yerel derleme (kontrol için)

```bash
npm ci
VITE_APP_TARGET=public npm run build --workspace client   # client/dist -> /public_html
VITE_APP_TARGET=admin  npm run build --workspace client   # client/dist -> /admin.gonulkoprusu.com
```
