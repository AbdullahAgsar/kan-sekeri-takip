from __future__ import annotations

import json
import os
import uuid
from datetime import datetime, timedelta
from typing import Any, Dict, List

from flask import Flask, redirect, render_template, request, url_for, flash


APP_ROOT = os.path.dirname(os.path.abspath(__file__))
DATA_FILE = os.path.join(APP_ROOT, "glucose_data.json")


def ensure_data_file_exists() -> None:
    if not os.path.exists(DATA_FILE):
        with open(DATA_FILE, "w", encoding="utf-8") as f:
            json.dump([], f, ensure_ascii=False, indent=2)


def read_entries() -> List[Dict[str, Any]]:
    ensure_data_file_exists()
    try:
        with open(DATA_FILE, "r", encoding="utf-8") as f:
            data = json.load(f)
            if isinstance(data, list):
                return data
            return []
    except json.JSONDecodeError:
        return []


def write_entries(entries: List[Dict[str, Any]]) -> None:
    with open(DATA_FILE, "w", encoding="utf-8") as f:
        json.dump(entries, f, ensure_ascii=False, indent=2)


def create_app() -> Flask:
    app = Flask(__name__)
    app.secret_key = os.environ.get("FLASK_SECRET_KEY", "dev-secret-key")
    
    # Proxy desteği için
    from werkzeug.middleware.proxy_fix import ProxyFix
    app.wsgi_app = ProxyFix(app.wsgi_app, x_for=1, x_proto=1, x_host=1, x_port=1, x_prefix=1)
    
    # Güvenlik headers
    @app.after_request
    def after_request(response):
        response.headers['X-Content-Type-Options'] = 'nosniff'
        response.headers['X-Frame-Options'] = 'DENY'
        response.headers['X-XSS-Protection'] = '1; mode=block'
        response.headers['Strict-Transport-Security'] = 'max-age=31536000; includeSubDomains'
        return response

    @app.route("/", methods=["GET"])  # Listeleme ve form
    def index():
        entries = read_entries()
        # Görsel sunum icin TR: "15 ekim salı 2025" ve saat "HH:MM"
        tr_months = [
            "ocak", "şubat", "mart", "nisan", "mayıs", "haziran",
            "temmuz", "ağustos", "eylül", "ekim", "kasım", "aralık",
        ]
        tr_weekdays = [
            "pazartesi", "salı", "çarşamba", "perşembe", "cuma", "cumartesi", "pazar",
        ]
        def format_display_parts(ts: str) -> Dict[str, str]:
            try:
                dt = datetime.fromisoformat(ts)
                day = dt.day
                month_name = tr_months[dt.month - 1]
                weekday_name = tr_weekdays[dt.weekday()]
                date_human = f"{day} {month_name} {weekday_name} {dt.year}"
                time_human = dt.strftime("%H:%M")
                return {"date_human": date_human, "time_human": time_human}
            except Exception:
                # Fallback
                base = ts.replace("T", " ") if isinstance(ts, str) else ""
                return {"date_human": base, "time_human": ""}
        for e in entries:
            ts = e.get("timestamp")
            parts = format_display_parts(ts) if ts else {"date_human": "", "time_human": ""}
            e["date_human"] = parts["date_human"]
            e["time_human"] = parts["time_human"]
        # En yeni kayitlar ustte
        entries_sorted = sorted(
            entries, key=lambda e: e.get("timestamp", ""), reverse=True
        )
        # GMT+3 için 3 saat ekle
        now_plus_3h = datetime.now() + timedelta(hours=3)
        default_now = now_plus_3h.strftime("%Y-%m-%dT%H:%M")
        return render_template("index.html", entries=entries_sorted, default_now=default_now)

    @app.route("/add", methods=["POST"])  # Kayit ekleme
    def add():
        try:
            # Request method kontrolü
            if request.method != "POST":
                flash("Geçersiz istek metodu.", "error")
                return redirect(url_for("index"))
            
            # Content-Type kontrolü
            if not request.is_json and request.content_type not in ['application/x-www-form-urlencoded', 'multipart/form-data']:
                flash("Geçersiz içerik türü.", "error")
                return redirect(url_for("index"))
            
            # Form data'sını güvenli şekilde al
            value_raw = request.form.get("value", "").strip()
            note = request.form.get("note", "").strip()
            dt_raw = request.form.get("datetime", "").strip()
            state_raw = request.form.get("state", "ac").strip().lower()

            # Değer kontrolü
            if not value_raw:
                flash("Lütfen kan şekeri değerini girin.", "error")
                return redirect(url_for("index"))

            try:
                value = float(value_raw.replace(",", "."))
                if value <= 0 or value > 1000:
                    flash("Kan şekeri değeri 0-1000 arasında olmalıdır.", "error")
                    return redirect(url_for("index"))
            except ValueError:
                flash("Geçersiz değer. Sadece sayı girin.", "error")
                return redirect(url_for("index"))

            # Tarih kontrolü
            if dt_raw:
                try:
                    # HTML datetime-local => %Y-%m-%dT%H:%M
                    dt = datetime.strptime(dt_raw, "%Y-%m-%dT%H:%M")
                except ValueError:
                    flash("Tarih/saat formatı geçersiz.", "error")
                    return redirect(url_for("index"))
            else:
                dt = datetime.now()

            # State kontrolü
            state = state_raw if state_raw in {"ac", "tok"} else "ac"

            # Entry oluştur
            entry = {
                "id": str(uuid.uuid4()),
                "timestamp": dt.isoformat(timespec="minutes"),
                "value": value,
                "note": note,
                "state": state,
            }

            # Veriyi kaydet
            entries = read_entries()
            entries.append(entry)
            write_entries(entries)
            flash("Kayıt başarıyla eklendi.", "success")
            
        except Exception as exc:
            app.logger.error(f"Add entry error: {exc}", exc_info=True)
            flash("Beklenmeyen bir hata oluştu. Lütfen tekrar deneyin.", "error")
        
        return redirect(url_for("index"))

    @app.route("/delete/<entry_id>", methods=["POST"])  # Kayit silme
    def delete(entry_id: str):
        try:
            # Form'dan delete parametresini kontrol et
            if not request.form.get("delete"):
                flash("Geçersiz silme isteği.", "error")
                return redirect(url_for("index"))
                
            entries = read_entries()
            original_count = len(entries)
            new_entries = [e for e in entries if e.get("id") != entry_id]
            
            if len(new_entries) == original_count:
                flash("Kayıt bulunamadı.", "error")
            else:
                write_entries(new_entries)
                flash("Kayıt silindi.", "success")
        except Exception as exc:
            flash(f"Silme işleminde hata oluştu: {exc}", "error")
        return redirect(url_for("index"))

    return app


app = create_app()

# Error handler
@app.errorhandler(400)
def bad_request(error):
    return "Geçersiz istek", 400

@app.errorhandler(404)
def not_found(error):
    return "Sayfa bulunamadı", 404

@app.errorhandler(405)
def method_not_allowed(error):
    return "İzin verilmeyen method", 405

@app.errorhandler(500)
def internal_error(error):
    return "Sunucu hatası", 500

if __name__ == "__main__":
    # Production için debug=False
    debug_mode = os.environ.get("FLASK_DEBUG", "False").lower() == "true"
    app.run(host="0.0.0.0", port=int(os.environ.get("PORT", 5000)), debug=debug_mode)


