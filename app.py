from __future__ import annotations

import json
import os
import uuid
from datetime import datetime
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
        default_now = datetime.now().isoformat(timespec="minutes")
        return render_template("index.html", entries=entries_sorted, default_now=default_now)

    @app.route("/add", methods=["POST"])  # Kayit ekleme
    def add():
        try:
            value_raw = request.form.get("value", "").strip()
            note = request.form.get("note", "").strip()
            dt_raw = request.form.get("datetime", "").strip()
            state_raw = request.form.get("state", "ac").strip().lower()

            if not value_raw:
                flash("Lütfen kan şekeri değerini girin.", "error")
                return redirect(url_for("index"))

            try:
                value = float(value_raw.replace(",", "."))
            except ValueError:
                flash("Geçersiz değer. Sadece sayı girin.", "error")
                return redirect(url_for("index"))

            if dt_raw:
                try:
                    # HTML datetime-local => %Y-%m-%dT%H:%M
                    dt = datetime.strptime(dt_raw, "%Y-%m-%dT%H:%M")
                except ValueError:
                    flash("Tarih/saat formatı geçersiz.", "error")
                    return redirect(url_for("index"))
            else:
                dt = datetime.now()

            state = state_raw if state_raw in {"ac", "tok"} else "ac"

            entry = {
                "id": str(uuid.uuid4()),
                "timestamp": dt.isoformat(timespec="minutes"),
                "value": value,
                "note": note,
                "state": state,
            }

            entries = read_entries()
            entries.append(entry)
            write_entries(entries)
            flash("Kayıt eklendi.", "success")
        except Exception as exc:  # yalnızca kullanıcıya dost mesaj
            flash(f"Beklenmeyen bir hata oluştu: {exc}", "error")
        return redirect(url_for("index"))

    @app.route("/delete/<entry_id>", methods=["POST"])  # Kayit silme
    def delete(entry_id: str):
        entries = read_entries()
        new_entries = [e for e in entries if e.get("id") != entry_id]
        if len(new_entries) == len(entries):
            flash("Kayıt bulunamadı.", "error")
        else:
            write_entries(new_entries)
            flash("Kayıt silindi.", "success")
        return redirect(url_for("index"))

    return app


app = create_app()


if __name__ == "__main__":
    app.run(host="0.0.0.0", port=int(os.environ.get("PORT", 5000)), debug=True)


