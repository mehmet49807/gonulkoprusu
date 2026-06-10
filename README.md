# Gönül Köprüsü

İyiliği büyüten dayanışma platformunun web sitesi.

## Proje Yapısı

```
.
├── index.html        # Ana sayfa (tek sayfa tasarım)
├── css/style.css     # Tüm stiller (responsive + animasyonlar)
├── js/main.js        # Etkileşimler (menü, sayaç, form, scroll reveal)
└── assets/           # Favicon ve görseller
```

## Yerelde Çalıştırma

Herhangi bir derleme adımı gerekmez; statik bir sitedir.

```bash
# Python ile
python3 -m http.server 8080

# veya Node ile
npx serve .
```

Ardından tarayıcıda `http://localhost:8080` adresini açın.

## Bölümler

- **Hero** — Tanıtım, istatistik sayaçları ve çağrı butonları
- **Hakkımızda** — Misyon ve öne çıkan özellikler
- **Nasıl Çalışır?** — 3 adımlı süreç kartları
- **Kampanyalar** — İlerleme çubuklu kampanya kartları
- **Hikâyeler** — Gönüllü ve faydalanıcı yorumları
- **Gönüllü Ol** — Başvuru formu (istemci tarafı doğrulama)
- **İletişim & Footer**

## Tasarım Notları

- Renk paleti: petrol yeşili (`#0f766e`) + mercan (`#ff7a59`)
- Yazı tipleri: Plus Jakarta Sans (genel), Lora (alıntılar)
- Tamamen responsive (mobil menü dahil), `prefers-reduced-motion` desteği
