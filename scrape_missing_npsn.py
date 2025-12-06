"""
Script untuk mencari NPSN sekolah yang belum ada di database
dengan cara scraping dari API Kemendikdasmen berdasarkan nama sekolah.

Cara kerja:
1. Ambil daftar sekolah tanpa NPSN dari database
2. Untuk setiap sekolah, search di API Kemendikdasmen
3. Match nama sekolah dengan fuzzy matching
4. Update database dengan NPSN, alamat, dan koordinat yang ditemukan
"""

import requests
import time
import re
import mysql.connector
from difflib import SequenceMatcher

# Database config
DB_CONFIG = {
    "host": "localhost",
    "port": 3306,
    "user": "root",
    "password": "root",
    "database": "data_anbksulteng"
}

API_URL = "https://sekolah.data.kemendikdasmen.go.id/v1/sekolah-service/sekolah/cari-sekolah-by-location"

# Koordinat pusat per kabupaten/kota untuk pencarian
WILAYAH_COORDS = {
    "Kota Palu": [(-0.8917, 119.8707)],
    "Kabupaten Sigi": [(-1.2000, 119.9000), (-1.4000, 120.1000), (-1.1000, 120.0500)],
    "Kabupaten Donggala": [(-0.6500, 119.7500), (-0.3000, 119.8000), (-1.0000, 119.7000)],
    "Kabupaten Parigi Moutong": [(-0.8500, 120.1500), (-0.5000, 120.5000), (-0.2000, 121.0000)],
    "Kabupaten Poso": [(-1.4000, 120.7500), (-1.8000, 120.6000), (-1.5000, 121.0000)],
    "Kabupaten Morowali": [(-2.4000, 121.6000), (-2.8000, 121.4000), (-2.2000, 121.8000)],
    "Kabupaten Morowali Utara": [(-1.8000, 121.4000), (-1.5000, 121.6000)],
    "Kabupaten Tojo Una-Una": [(-1.2000, 121.5000), (-1.5000, 121.3000), (-1.0000, 121.8000)],
    "Kabupaten Tolitoli": [(1.0000, 120.8000), (0.8000, 121.0000), (1.2000, 121.2000)],
    "Kabupaten Buol": [(0.9000, 121.4000), (1.1000, 121.6000)],
    "Kabupaten Banggai": [(-1.0000, 122.8000), (-1.3000, 122.5000), (-0.8000, 123.0000)],
    "Kabupaten Banggai Kepulauan": [(-1.2000, 123.5000), (-1.0000, 123.2000)],
    "Kabupaten Banggai Laut": [(-1.6000, 123.5000), (-1.4000, 123.8000)],
}

# Alias wilayah untuk matching
WILAYAH_ALIAS = {
    "Tojo Una-Una": "Kabupaten Tojo Una-Una",
    "Tojo Unauna": "Kabupaten Tojo Una-Una",
    "Tojo-Una-Una": "Kabupaten Tojo Una-Una",
    "Tolitoli": "Kabupaten Tolitoli",
    "Toli-Toli": "Kabupaten Tolitoli",
    "Palu": "Kota Palu",
    "Sigi": "Kabupaten Sigi",
    "Donggala": "Kabupaten Donggala",
    "Parigi Moutong": "Kabupaten Parigi Moutong",
    "Poso": "Kabupaten Poso",
    "Morowali": "Kabupaten Morowali",
    "Morowali Utara": "Kabupaten Morowali Utara",
    "Buol": "Kabupaten Buol",
    "Banggai": "Kabupaten Banggai",
    "Banggai Kepulauan": "Kabupaten Banggai Kepulauan",
    "Banggai Laut": "Kabupaten Banggai Laut",
}


def normalize_name(name):
    """Normalisasi nama sekolah untuk matching"""
    if not name:
        return ""

    name = name.upper().strip()

    # Hapus karakter khusus
    name = re.sub(r'[^\w\s]', ' ', name)

    # Standardisasi prefix
    replacements = [
        (r'\bSD\s+NEGERI\b', 'SDN'),
        (r'\bSD\s+INPRES\b', 'SD INPRES'),
        (r'\bSMP\s+NEGERI\b', 'SMPN'),
        (r'\bSMA\s+NEGERI\b', 'SMAN'),
        (r'\bSMK\s+NEGERI\b', 'SMKN'),
        (r'\bMI\s+NEGERI\b', 'MIN'),
        (r'\bMTS\s+NEGERI\b', 'MTSN'),
        (r'\bMA\s+NEGERI\b', 'MAN'),
    ]

    for pattern, replacement in replacements:
        name = re.sub(pattern, replacement, name)

    # Hapus spasi berlebih
    name = ' '.join(name.split())

    return name


def similarity(a, b):
    """Hitung similarity antara dua string"""
    return SequenceMatcher(None, normalize_name(a), normalize_name(b)).ratio()


def fetch_schools_by_coord(lat, lon):
    """Fetch sekolah dari API berdasarkan koordinat"""
    payload = {
        "keyword": "",
        "bentuk_pendidikan": "",
        "status_sekolah": "",
        "lintang": lat,
        "bujur": lon,
        "use_geo_kabupaten_kota": False
    }

    try:
        r = requests.post(API_URL, json=payload, timeout=30)
        r.raise_for_status()
        d = r.json()

        if isinstance(d, dict) and "data" in d:
            return d["data"]
        return d if isinstance(d, list) else []
    except Exception as e:
        print(f"  Error fetching: {e}")
        return []


def search_school_by_name(school_name, wilayah_name, jenjang):
    """Cari sekolah di API berdasarkan nama dan wilayah"""

    # Normalize wilayah name
    wilayah_key = wilayah_name
    for alias, full_name in WILAYAH_ALIAS.items():
        if alias.lower() in wilayah_name.lower():
            wilayah_key = full_name
            break

    if wilayah_key not in WILAYAH_COORDS:
        # Coba cari partial match
        for key in WILAYAH_COORDS.keys():
            if wilayah_name.lower() in key.lower() or key.lower() in wilayah_name.lower():
                wilayah_key = key
                break

    if wilayah_key not in WILAYAH_COORDS:
        print(f"  Wilayah tidak ditemukan: {wilayah_name}")
        return None

    coords = WILAYAH_COORDS[wilayah_key]
    all_schools = []

    # Fetch dari semua koordinat wilayah
    for lat, lon in coords:
        schools = fetch_schools_by_coord(lat, lon)
        all_schools.extend(schools)
        time.sleep(0.2)

    # Filter hanya Sulawesi Tengah
    all_schools = [s for s in all_schools if "Sulawesi Tengah" in (s.get("provinsi") or "")]

    # Deduplicate by NPSN
    seen = set()
    unique_schools = []
    for s in all_schools:
        npsn = s.get("npsn")
        if npsn and npsn not in seen:
            seen.add(npsn)
            unique_schools.append(s)

    # Cari match terbaik
    best_match = None
    best_score = 0

    normalized_search = normalize_name(school_name)

    for s in unique_schools:
        api_name = s.get("nama", "")
        score = similarity(school_name, api_name)

        # Bonus jika jenjang sama
        api_jenjang = s.get("bentuk_pendidikan", "")
        if jenjang and api_jenjang:
            if jenjang.upper() in api_jenjang.upper() or api_jenjang.upper() in jenjang.upper():
                score += 0.1

        if score > best_score:
            best_score = score
            best_match = s

    if best_match and best_score >= 0.7:
        return {
            "npsn": best_match.get("npsn"),
            "nama_api": best_match.get("nama"),
            "alamat": best_match.get("alamat_jalan") or best_match.get("alamat"),
            "latitude": best_match.get("lintang"),
            "longitude": best_match.get("bujur"),
            "score": best_score
        }

    return None


def get_schools_without_npsn(conn):
    """Ambil daftar sekolah tanpa NPSN dari database"""
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


def update_school_npsn(conn, school_id, npsn, alamat, latitude, longitude):
    """Update NPSN dan data lainnya di database"""
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


def main():
    print("=" * 60)
    print("SCRAPER NPSN UNTUK SEKOLAH YANG BELUM ADA")
    print("=" * 60)

    # Connect ke database
    print("\nConnecting to database...")
    try:
        conn = mysql.connector.connect(**DB_CONFIG)
        print("Connected!")
    except Exception as e:
        print(f"Error connecting to database: {e}")
        print("\nJika menggunakan Docker, jalankan dengan port forwarding:")
        print("  docker exec -it laravel-mysql mysql -u root -proot")
        return

    # Ambil sekolah tanpa NPSN
    schools = get_schools_without_npsn(conn)
    total = len(schools)

    print(f"\nDitemukan {total} sekolah tanpa NPSN")

    if total == 0:
        print("Semua sekolah sudah memiliki NPSN!")
        conn.close()
        return

    # Proses setiap sekolah
    found = 0
    not_found = []

    for i, school in enumerate(schools, 1):
        print(f"\n[{i}/{total}] {school['nama']}")
        print(f"  Wilayah: {school['wilayah_nama']}, Jenjang: {school['jenjang']}")

        result = search_school_by_name(
            school['nama'],
            school['wilayah_nama'],
            school['jenjang']
        )

        if result:
            print(f"  ✓ FOUND: {result['nama_api']} (NPSN: {result['npsn']}, Score: {result['score']:.2f})")

            # Update database
            update_school_npsn(
                conn,
                school['id'],
                result['npsn'],
                result['alamat'],
                result['latitude'],
                result['longitude']
            )
            found += 1
        else:
            print(f"  ✗ NOT FOUND")
            not_found.append(school)

        time.sleep(0.3)

    conn.close()

    # Summary
    print("\n" + "=" * 60)
    print("SUMMARY")
    print("=" * 60)
    print(f"Total sekolah diproses: {total}")
    print(f"Berhasil ditemukan: {found}")
    print(f"Tidak ditemukan: {len(not_found)}")

    if not_found:
        print(f"\nSekolah yang tidak ditemukan:")
        for s in not_found[:20]:  # Tampilkan 20 pertama
            print(f"  - {s['nama']} ({s['wilayah_nama']})")
        if len(not_found) > 20:
            print(f"  ... dan {len(not_found) - 20} lainnya")


if __name__ == "__main__":
    main()

