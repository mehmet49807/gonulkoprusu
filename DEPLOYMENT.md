# Yükleme (Deployment) Rehberi — Gönül Köprüsü

Bu proje GitHub'a her `main` push'unda **otomatik olarak FTP ile** yüklenir:

| Site | Build hedefi | FTP klasörü | Adres |
| --- | --- | --- | --- |
| Web sitesi | `VITE_APP_TARGET=public` | `/public_html` | https://www.gonulkoprusu.com |
| Yönetici paneli | `VITE_APP_TARGET=admin` | `/admin.gonulkoprusu.com` | https://admin.gonulkoprusu.com |

Akış: `.github/workflows/deploy.yml` → frontend'i derler (`client/dist`) → `SamKirkland/FTP-Deploy-Action` ile sunucuya yükler.

---

## 1) ÖNEMLİ — Güvenlik

- **FTP parolanız sohbet içinde açık metin olarak paylaşıldı. Lütfen hosting panelinden (cPanel/Plesk) FTP parolasını HEMEN değiştirin.**
- Parola **asla repoya yazılmaz**; yalnızca GitHub Secrets içinde saklanır.
- Verdiğiniz iki parola farklıydı (`Mhmt498071` ve `Mehmt498071`). Kullanıcı adı aynı olduğundan (`cursor@gonulkoprusu.com`) tek bir parola olması gerekir; biri yazım hatası olabilir. Doğru parolayı tek bir secret olarak girin.

---

## 2) GitHub Secrets / Variables Ayarları

GitHub repo → **Settings → Secrets and variables → Actions**.

### Secrets (zorunlu)

| Adı | Değer |
| --- | --- |
| `FTP_SERVER` | `ftp.gonulkoprusu.com` |
| `FTP_USERNAME` | `cursor@gonulkoprusu.com` |
| `FTP_PASSWORD` | (yeni FTP parolanız) |
| `FTP_ADMIN_PASSWORD` | (yalnızca admin için **farklı** bir FTP hesabı varsa; aksi halde girmeyin) |

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
