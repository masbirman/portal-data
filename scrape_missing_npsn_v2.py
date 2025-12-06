"""
Script V2: Scrape NPSN untuk sekolah yang belum ada
Menggunakan grid scraping per wilayah, lalu match dengan database

Lebih efisien karena:
1. Hanya scrape wilayah yang punya sekolah tanpa NPSN
2. Scrape sekali per wilayah, lalu match semua sekolah
3. Langsung update database
"""

import requests
import time
import re
import csv
import os
import mysql.connector
from difflib import SequenceMatcher
from collections import defaultdict

# Database config - sesuaikan jika perlu
DB_CONFIG = {
    "host": "127.0.0.1",  # Docker MySQL port forwarded ke localhost
    "port": 3306,
    "user": "root",
    "password": "root",
    "database": "data_anbksulteng",
}

API_URL = "https://sekolah.data.kemendikdasmen.go.id/v1/sekolah-service/sekolah/cari-sekolah-by-location"

# Bounding box per kabupaten/kota
KAB_BOX = {
    "Kota Palu": {
        "min_lat": -0.945,
        "max_lat": -0.780,
        "min_lon": 119.770,
        "max_lon": 119.950,
    },
    "Kabupaten Sigi": {
        "min_lat": -2.050,
        "max_lat": -0.800,
        "min_lon": 119.570,
        "max_lon": 120.300,
    },
    "Kabupaten Donggala": {
        "min_lat": -1.200,
        "max_lat": 0.300,
        "min_lon": 119.500,
        "max_lon": 120.200,
    },
    "Kabupaten Parigi Moutong": {
        "min_lat": -1.200,
        "max_lat": 0.500,
        "min_lon": 120.000,
        "max_lon": 121.900,
    },
    "Kabupaten Poso": {
        "min_lat": -2.300,
        "max_lat": -0.900,
        "min_lon": 120.200,
        "max_lon": 122.050,
    },
    "Kabupaten Morowali": {
        "min_lat": -3.600,
        "max_lat": -1.800,
        "min_lon": 121.000,
        "max_lon": 122.100,
    },
    "Kabupaten Morowali Utara": {
        "min_lat": -2.400,
        "max_lat": -1.100,
        "min_lon": 121.000,
        "max_lon": 122.300,
    },
    "Kabupaten Tojo Una-Una": {
        "min_lat": -2.100,
        "max_lat": -0.900,
        "min_lon": 120.500,
        "max_lon": 122.100,
    },
    "Kabupaten Tolitoli": {
        "min_lat": 0.500,
        "max_lat": 1.500,
        "min_lon": 120.500,
        "max_lon": 121.700,
    },
    "Kabupaten Buol": {
        "min_lat": 0.300,
        "max_lat": 1.500,
        "min_lon": 121.000,
        "max_lon": 122.500,
    },
    "Kabupaten Banggai": {
        "min_lat": -2.100,
        "max_lat": -0.600,
        "min_lon": 122.100,
        "max_lon": 124.000,
    },
    "Kabupaten Banggai Kepulauan": {
        "min_lat": -1.500,
        "max_lat": 0.300,
        "min_lon": 122.400,
        "max_lon": 124.500,
    },
    "Kabupaten Banggai Laut": {
        "min_lat": -2.300,
        "max_lat": -0.800,
        "min_lon": 123.000,
        "max_lon": 124.600,
    },
}

# Mapping nama wilayah di database ke nama di KAB_BOX
WILAYAH_MAP = {
    "Palu": "Kota Palu",
    "Kota Palu": "Kota Palu",
    "Sigi": "Kabupaten Sigi",
    "Kabupaten Sigi": "Kabupaten Sigi",
    "Donggala": "Kabupaten Donggala",
    "Kabupaten Donggala": "Kabupaten Donggala",
    "Parigi Moutong": "Kabupaten Parigi Moutong",
    "Kabupaten Parigi Moutong": "Kabupaten Parigi Moutong",
    "Poso": "Kabupaten Poso",
    "Kabupaten Poso": "Kabupaten Poso",
    "Morowali": "Kabupaten Morowali",
    "Kabupaten Morowali": "Kabupaten Morowali",
    "Morowali Utara": "Kabupaten Morowali Utara",
    "Kabupaten Morowali Utara": "Kabupaten Morowali Utara",
    "Tojo Una-Una": "Kabupaten Tojo Una-Una",
    "Tojo Unauna": "Kabupaten Tojo Una-Una",
    "Kabupaten Tojo Una-Una": "Kabupaten Tojo Una-Una",
    "Tolitoli": "Kabupaten Tolitoli",
    "Toli-Toli": "Kabupaten Tolitoli",
    "Kabupaten Tolitoli": "Kabupaten Tolitoli",
    "Buol": "Kabupaten Buol",
    "Kabupaten Buol": "Kabupaten Buol",
    "Banggai": "Kabupaten Banggai",
    "Kabupaten Banggai": "Kabupaten Banggai",
    "Banggai Kepulauan": "Kabupaten Banggai Kepulauan",
    "Kabupaten Banggai Kepulauan": "Kabupaten Banggai Kepulauan",
    "Banggai Laut": "Kabupaten Banggai Laut",
    "Kabupaten Banggai Laut": "Kabupaten Banggai Laut",
}

STEP = 0.05  # Grid step - lebih besar = lebih cepat tapi mungkin miss beberapa


def frange(a, b, step):
    x = a
    while x <= b + 1e-9:
        yield round(x, 6)
        x += step


def normalize_name(name):
    """Normalisasi nama sekolah untuk matching"""
    if not name:
        return ""

    name = name.upper().strip()
    name = re.sub(r"[^\w\s]", " ", name)

    # Standardisasi prefix
    replacements = [
        (r"\bSD\s+NEGERI\b", "SDN"),
        (r"\bSD\s+INPRES\b", "SD INPRES"),
        (r"\bSMP\s+NEGERI\b", "SMPN"),
        (r"\bSMA\s+NEGERI\b", "SMAN"),
        (r"\bSMK\s+NEGERI\b", "SMKN"),
        (r"\bMI\s+NEGERI\b", "MIN"),
        (r"\bMTS\s+NEGERI\b", "MTSN"),
        (r"\bMA\s+NEGERI\b", "MAN"),
    ]

    for pattern, replacement in replacements:
        name = re.sub(pattern, replacement, name)

    name = " ".join(name.split())
    return name


def similarity(a, b):
    """Hitung similarity antara dua string"""
    na, nb = normalize_name(a), normalize_name(b)
    return SequenceMatcher(None, na, nb).ratio()


def fetch(lat, lon):
    """Fetch sekolah dari API"""
    p = {
        "keyword": "",
        "bentuk_pendidikan": "",
        "status_sekolah": "",
        "lintang": lat,
        "bujur": lon,
        "use_geo_kabupaten_kota": False,
    }
    r = requests.post(API_URL, json=p, timeout=20)
    r.raise_for_status()
    d = r.json()
    if isinstance(d, dict) and "data" in d:
        return d["data"]
    return d if isinstance(d, list) else []


def scrape_wilayah(nama, box):
    """Scrape semua sekolah di satu wilayah"""
    print(f"\n  Scraping {nama}...")

    seen = set()
    result = []

    grid_lat = list(frange(box["min_lat"], box["max_lat"], STEP))
    grid_lon = list(frange(box["min_lon"], box["max_lon"], STEP))
    total = len(grid_lat) * len(grid_lon)
    count = 0

    for lat in grid_lat:
        for lon in grid_lon:
            count += 1
            print(f"    [{count}/{total}] lat={lat:.4f} lon={lon:.4f}    ", end="\r")

            try:
                data = fetch(lat, lon)
            except Exception as e:
                time.sleep(1)
                continue

            for s in data:
                if "Sulawesi Tengah" not in (s.get("provinsi") or ""):
                    continue

                npsn = s.get("npsn")
                if not npsn or npsn in seen:
                    continue

                seen.add(npsn)
                result.append(
                    {
                        "npsn": npsn,
                        "nama": s.get("nama"),
                        "alamat": s.get("alamat_jalan") or s.get("alamat"),
                        "latitude": s.get("lintang"),
                        "longitude": s.get("bujur"),
                        "bentuk_pendidikan": s.get("bentuk_pendidikan"),
                    }
                )

            time.sleep(0.1)

    print(f"    Ditemukan {len(result)} sekolah dari API")
    return result


def get_schools_without_npsn(conn):
    """Ambil sekolah tanpa NPSN, grouped by wilayah"""
    cursor = conn.cursor(dictionary=True)

    query = """
        SELECT s.id, s.nama, s.kode_sekolah, w.nama as wilayah_nama, jp.nama as jenjang
        FROM sekolah s
        LEFT JOIN wilayah w ON s.wilayah_id = w.id
        LEFT JOIN jenjang_pendidikan jp ON s.jenjang_pendidikan_id = jp.id
        WHERE s.npsn IS NULL OR s.npsn = ''
        ORDER BY w.nama, s.nama
    """

    cursor.execute(query)
    results = cursor.fetchall()
    cursor.close()

    # Group by wilayah
    by_wilayah = defaultdict(list)
    for r in results:
        wilayah = r["wilayah_nama"] or "Unknown"
        by_wilayah[wilayah].append(r)

    return by_wilayah


def update_school(conn, school_id, npsn, alamat, latitude, longitude):
    """Update sekolah di database"""
    cursor = conn.cursor()

    query = """
        UPDATE sekolah
        SET npsn = %s,
            alamat = COALESCE(%s, alamat),
            latitude = COALESCE(%s, latitude),
            longitude = COALESCE(%s, longitude)
        WHERE id = %s
    """

    cursor.execute(query, (npsn, alamat, latitude, longitude, school_id))
    conn.commit()
    cursor.close()


def match_schools(db_schools, api_schools):
    """Match sekolah dari database dengan hasil API"""
    matches = []
    not_found = []

    for db_school in db_schools:
        best_match = None
        best_score = 0

        for api_school in api_schools:
            score = similarity(db_school["nama"], api_school["nama"])

            # Bonus jika jenjang sama
            if db_school.get("jenjang") and api_school.get("bentuk_pendidikan"):
                db_jenjang = db_school["jenjang"].upper()
                api_jenjang = api_school["bentuk_pendidikan"].upper()
                if db_jenjang in api_jenjang or api_jenjang in db_jenjang:
                    score += 0.1

            if score > best_score:
                best_score = score
                best_match = api_school

        if best_match and best_score >= 0.65:
            matches.append(
                {"db_school": db_school, "api_school": best_match, "score": best_score}
            )
        else:
            not_found.append(db_school)

    return matches, not_found


def main():
    print("=" * 60)
    print("SCRAPER NPSN V2 - UNTUK SEKOLAH YANG BELUM ADA")
    print("=" * 60)

    # Connect database
    print("\nConnecting to database...")
    try:
        conn = mysql.connector.connect(**DB_CONFIG)
        print("Connected!")
    except Exception as e:
        print(f"Error: {e}")
        print("\nPastikan MySQL bisa diakses. Jika pakai Docker:")
        print("  1. Forward port: docker exec -it laravel-mysql mysql")
        print("  2. Atau jalankan script ini di dalam container")
        return

    # Ambil sekolah tanpa NPSN per wilayah
    schools_by_wilayah = get_schools_without_npsn(conn)

    total_schools = sum(len(s) for s in schools_by_wilayah.values())
    print(
        f"\nTotal {total_schools} sekolah tanpa NPSN di {len(schools_by_wilayah)} wilayah"
    )

    if total_schools == 0:
        print("Semua sekolah sudah punya NPSN!")
        conn.close()
        return

    # Proses per wilayah
    total_found = 0
    total_not_found = []

    for wilayah_db, db_schools in schools_by_wilayah.items():
        print(f"\n{'='*50}")
        print(f"WILAYAH: {wilayah_db} ({len(db_schools)} sekolah)")
        print("=" * 50)

        # Map ke nama wilayah di KAB_BOX
        wilayah_key = WILAYAH_MAP.get(wilayah_db)

        if not wilayah_key or wilayah_key not in KAB_BOX:
            print(f"  ⚠ Wilayah tidak ditemukan di mapping: {wilayah_db}")
            total_not_found.extend(db_schools)
            continue

        # Scrape wilayah ini
        api_schools = scrape_wilayah(wilayah_key, KAB_BOX[wilayah_key])

        if not api_schools:
            print(f"  ⚠ Tidak ada data dari API untuk {wilayah_key}")
            total_not_found.extend(db_schools)
            continue

        # Match sekolah
        matches, not_found = match_schools(db_schools, api_schools)

        print(f"\n  Hasil matching:")
        print(f"    ✓ Ditemukan: {len(matches)}")
        print(f"    ✗ Tidak ditemukan: {len(not_found)}")

        # Update database
        for m in matches:
            db_s = m["db_school"]
            api_s = m["api_school"]

            print(f"    → {db_s['nama']}")
            print(f"      NPSN: {api_s['npsn']} (score: {m['score']:.2f})")

            update_school(
                conn,
                db_s["id"],
                api_s["npsn"],
                api_s["alamat"],
                api_s["latitude"],
                api_s["longitude"],
            )

        total_found += len(matches)
        total_not_found.extend(not_found)

    conn.close()

    # Summary
    print("\n" + "=" * 60)
    print("SUMMARY")
    print("=" * 60)
    print(f"Total diproses: {total_schools}")
    print(f"Berhasil update: {total_found}")
    print(f"Tidak ditemukan: {len(total_not_found)}")

    if total_not_found:
        print(f"\nSekolah tidak ditemukan (perlu input manual):")
        for s in total_not_found[:30]:
            print(f"  - {s['nama']} ({s['wilayah_nama']})")
        if len(total_not_found) > 30:
            print(f"  ... dan {len(total_not_found) - 30} lainnya")

        # Save to file
        with open("schools_not_found.txt", "w", encoding="utf-8") as f:
            for s in total_not_found:
                f.write(f"{s['nama']}\t{s['wilayah_nama']}\t{s.get('jenjang', '')}\n")
        print(f"\nDaftar lengkap disimpan di: schools_not_found.txt")


if __name__ == "__main__":
    main()
