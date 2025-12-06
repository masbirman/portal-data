"""
Script CEPAT: Match NPSN dari CSV yang sudah ada
Tidak perlu scraping ulang - langsung pakai data CSV hasil scraping sebelumnya

Cara kerja:
1. Baca semua file CSV di kabupaten_csv/
2. Ambil sekolah tanpa NPSN dari database
3. Match dengan fuzzy matching
4. Update database
"""

import os
import csv
import re
import mysql.connector
from difflib import SequenceMatcher
from collections import defaultdict

# Database config
DB_CONFIG = {
    "host": "127.0.0.1",
    "port": 3306,
    "user": "root",
    "password": "root",
    "database": "data_anbksulteng",
}

CSV_DIR = "kabupaten_csv"


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
        (r"\bMTSS\b", "MTS"),
        (r"\bMA\s+NEGERI\b", "MAN"),
        (r"\bMAS\b", "MA"),
    ]

    for pattern, replacement in replacements:
        name = re.sub(pattern, replacement, name)

    name = " ".join(name.split())
    return name


def similarity(a, b):
    """Hitung similarity"""
    na, nb = normalize_name(a), normalize_name(b)
    return SequenceMatcher(None, na, nb).ratio()


def load_all_csv():
    """Load semua data dari CSV files"""
    all_schools = []

    if not os.path.exists(CSV_DIR):
        print(f"ERROR: Folder {CSV_DIR} tidak ditemukan!")
        return []

    for filename in os.listdir(CSV_DIR):
        if not filename.endswith(".csv"):
            continue

        filepath = os.path.join(CSV_DIR, filename)
        print(f"  Loading {filename}...")

        with open(filepath, "r", encoding="utf-8") as f:
            reader = csv.DictReader(f)
            for row in reader:
                all_schools.append(
                    {
                        "npsn": row.get("npsn", ""),
                        "nama": row.get("nama", ""),
                        "alamat": row.get("alamat", ""),
                        "kabupaten": row.get("kabupaten", ""),
                        "latitude": row.get("latitude", ""),
                        "longitude": row.get("longitude", ""),
                        "bentuk_pendidikan": row.get("bentuk_pendidikan", ""),
                    }
                )

    # Deduplicate by NPSN
    seen = set()
    unique = []
    for s in all_schools:
        if s["npsn"] and s["npsn"] not in seen:
            seen.add(s["npsn"])
            unique.append(s)

    return unique


def get_schools_without_npsn(conn):
    """Ambil sekolah tanpa NPSN dari database"""
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

    return results


def update_school(conn, school_id, npsn, alamat, latitude, longitude):
    """Update sekolah di database"""
    cursor = conn.cursor()

    # Convert empty strings to None
    lat = float(latitude) if latitude else None
    lon = float(longitude) if longitude else None

    query = """
        UPDATE sekolah
        SET npsn = %s,
            alamat = COALESCE(%s, alamat),
            latitude = COALESCE(%s, latitude),
            longitude = COALESCE(%s, longitude)
        WHERE id = %s
    """

    cursor.execute(query, (npsn, alamat, lat, lon, school_id))
    conn.commit()
    cursor.close()


def extract_school_number(name):
    """Extract nomor sekolah dari nama (SDN 1, SMPN 2, dll)"""
    if not name:
        return None
    name_upper = name.upper()
    # Pattern untuk menangkap nomor sekolah
    match = re.search(
        r"\b(?:SDN|SMPN|SMAN|SMKN|MIN|MTSN|MAN|SD INPRES|SMP|SMA|SMK|MI|MTS|MA|SD)\s*(\d+)\b",
        name_upper,
    )
    if match:
        return match.group(1)
    return None


def match_school(db_school, csv_schools, threshold=0.95):
    """Cari match - SANGAT KETAT untuk menghindari salah match"""
    best_match = None
    best_score = 0

    db_name = db_school["nama"]
    db_name_norm = normalize_name(db_name)
    db_wilayah = (db_school.get("wilayah_nama") or "").lower()
    db_number = extract_school_number(db_name)

    for csv_school in csv_schools:
        csv_name = csv_school["nama"]
        csv_name_norm = normalize_name(csv_name)
        csv_kab = (csv_school.get("kabupaten") or "").lower()
        csv_number = extract_school_number(csv_name)

        # STRICT 1: Jika keduanya punya nomor, HARUS SAMA
        if db_number and csv_number and db_number != csv_number:
            continue

        # Hitung similarity nama (normalized)
        score = SequenceMatcher(None, db_name_norm, csv_name_norm).ratio()

        # Skip jika score terlalu rendah
        if score < 0.88:
            continue

        # Bonus kecil jika wilayah sama
        if db_wilayah and csv_kab:
            db_wil_clean = (
                db_wilayah.replace("kabupaten", "").replace("kota", "").strip()
            )
            csv_wil_clean = csv_kab.replace("kab.", "").replace("kota", "").strip()
            if db_wil_clean in csv_wil_clean or csv_wil_clean in db_wil_clean:
                score += 0.05

        if score > best_score:
            best_score = score
            best_match = csv_school

    if best_match and best_score >= threshold:
        return best_match, best_score

    return None, 0


def main():
    print("=" * 60)
    print("MATCH NPSN DARI CSV - VERSI CEPAT")
    print("=" * 60)

    # Load CSV
    print("\nLoading data dari CSV...")
    csv_schools = load_all_csv()
    print(f"Total {len(csv_schools)} sekolah dari CSV")

    if not csv_schools:
        print("ERROR: Tidak ada data CSV!")
        return

    # Connect database
    print("\nConnecting to database...")
    try:
        conn = mysql.connector.connect(**DB_CONFIG)
        print("Connected!")
    except Exception as e:
        print(f"Error: {e}")
        return

    # Ambil sekolah tanpa NPSN
    db_schools = get_schools_without_npsn(conn)
    total = len(db_schools)
    print(f"\nTotal {total} sekolah tanpa NPSN di database")

    if total == 0:
        print("Semua sekolah sudah punya NPSN!")
        conn.close()
        return

    # Proses matching
    print("\nMemproses matching...")
    found = 0
    not_found = []

    for i, db_school in enumerate(db_schools, 1):
        print(f"[{i}/{total}] {db_school['nama'][:50]}...", end=" ")

        match, score = match_school(db_school, csv_schools)

        if match:
            print(f"✓ {match['npsn']} (score: {score:.2f})")

            update_school(
                conn,
                db_school["id"],
                match["npsn"],
                match["alamat"],
                match["latitude"],
                match["longitude"],
            )
            found += 1
        else:
            print("✗ NOT FOUND")
            not_found.append(db_school)

    conn.close()

    # Summary
    print("\n" + "=" * 60)
    print("SUMMARY")
    print("=" * 60)
    print(f"Total diproses: {total}")
    print(f"Berhasil match: {found}")
    print(f"Tidak ditemukan: {len(not_found)}")

    if not_found:
        print(f"\nSekolah tidak ditemukan di CSV:")
        for s in not_found[:30]:
            print(f"  - {s['nama']} ({s['wilayah_nama']})")
        if len(not_found) > 30:
            print(f"  ... dan {len(not_found) - 30} lainnya")

        # Save to file
        with open("schools_not_in_csv.txt", "w", encoding="utf-8") as f:
            for s in not_found:
                f.write(f"{s['nama']}\t{s['wilayah_nama']}\t{s.get('jenjang', '')}\n")
        print(f"\nDaftar disimpan di: schools_not_in_csv.txt")


if __name__ == "__main__":
    main()
