# Gönül Köprüsü

İyiliği büyüten, gönülden gönüle köprü kuran yardımlaşma ve dayanışma derneği için
modern, responsive tek sayfa (landing page) web sitesi.

## Özellikler

- Tamamen responsive, mobil uyumlu tasarım (mobil menü dahil)
- Hero bölümü + canlı kampanya kartı (ilerleme çubuğu)
- Animasyonlu istatistik sayaçları
- Hakkımızda, Çalışmalarımız, Etkimiz, Gönüllü ve İletişim bölümleri
- Etkileşimli bağış formu (tutar seçimi, doğrulama, demo onay mesajı)
- Bülten abonelik formu
- Scroll ile beliren (reveal) animasyonları
- Erişilebilirlik: "içeriğe geç" linki, ARIA etiketleri, `prefers-reduced-motion` desteği

## Teknolojiler

Bağımlılık gerektirmez — saf **HTML + CSS + JavaScript** (vanilla).

## Yerel Çalıştırma

```bash
python3 -m http.server 8000
# Tarayıcıda: http://localhost:8000
```

## Dosya Yapısı

```
.
├── index.html
└── assets/
    ├── css/styles.css
    └── js/main.js
```

> Not: Bağış ve bülten formları demo amaçlıdır; gerçek bir ödeme/abonelik
> servisine bağlı değildir.
