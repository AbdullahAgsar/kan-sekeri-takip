# Kan Şekeri Takip

Basit bir kişisel kan şekeri takip uygulaması. Kayıtlar `glucose_data.json` dosyasına yazılır ve oradan okunur. Tek kullanıcı içindir.

## Özellikler
- Kayıt ekleme (değer, tarih/saat, not)
- Kayıtları tablo halinde listeleme (yeniden eskiye)
- Kayıt silme
- JSON dosyasına okuma/yazma

## Kurulum ve Çalıştırma

1. Python 3.10+ sürümünün kurulu olduğundan emin olun.
2. Sanal ortam (opsiyonel) ve bağımlılıklar:
   ```bash
   python -m venv .venv && source .venv/bin/activate
   pip install -r requirements.txt
   ```
3. Uygulamayı çalıştırın:
   ```bash
   python app.py
   ```
4. Tarayıcıda açın: `http://127.0.0.1:5000`

### Notlar
- Veriler proje kökündeki `glucose_data.json` dosyasında tutulur.
- `datetime` alanı boş bırakılırsa anlık tarih/saat kullanılır.
- Üretimde `FLASK_SECRET_KEY` ortam değişkenini ayarlamanız önerilir.
