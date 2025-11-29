# Perbaikan Duplikat Wilayah

## Masalah yang Ditemukan

Setiap kali upload data sekolah, sistem membuat wilayah baru jika nama wilayah ditulis dengan format berbeda. Contoh:
- "Kabupaten Donggala" vs "Kab. Donggala"
- "Kabupaten Toli-Toli" vs "Kab. Tolitoli"
- "Kabupaten Tojo Una-Una" vs "Kab. Tojo Unauna"

Ini menyebabkan tabel "Rekap Sulawesi Tengah" di dashboard menampilkan wilayah yang sama berkali-kali.

## Penyebab

File `app/Imports/SekolahImport.php` pada method `getWilayahId()` tidak melakukan normalisasi nama wilayah sebelum mencari atau membuat wilayah baru. Sehingga variasi penulisan nama wilayah dianggap sebagai wilayah yang berbeda.

## Solusi yang Diterapkan

### 1. Membersihkan Data Duplikat

Script `fix-duplicate-wilayah.php` telah dijalankan untuk:
- Menggabungkan 12 wilayah duplikat menjadi 13 wilayah unik
- Memperbarui referensi di tabel `sekolah` dan `pelaksanaan_asesmen`
- Menghapus wilayah duplikat

**Hasil:**
- Sebelum: 25 wilayah (banyak duplikat)
- Sesudah: 13 wilayah (sesuai jumlah kabupaten/kota di Sulawesi Tengah)

### 2. Mencegah Duplikat di Masa Depan

File `app/Imports/SekolahImport.php` telah diperbaiki dengan menambahkan method `normalizeWilayahName()` yang:

1. **Mengganti "Kab." atau "Kab " dengan "Kabupaten "**
   - "Kab. Donggala" → "Kabupaten Donggala"
   - "Kab Poso" → "Kabupaten Poso"

2. **Menormalisasi spasi**
   - "Kabupaten  Sigi" (double space) → "Kabupaten Sigi"

3. **Memperbaiki kasus khusus:**
   - "Tolitoli" → "Toli-Toli"
   - "Tojo Unauna" → "Tojo Una-Una"
   - "Tojo Una-una" → "Tojo Una-Una"

4. **Menerapkan Title Case**
   - "KABUPATEN POSO" → "Kabupaten Poso"
   - "kabupaten poso" → "Kabupaten Poso"

## Daftar Wilayah yang Benar

Berikut adalah 13 wilayah di Sulawesi Tengah yang sekarang ada di database:

1. Kota Palu
2. Kabupaten Donggala
3. Kabupaten Sigi
4. Kabupaten Parigi Moutong
5. Kabupaten Tojo Una-Una
6. Kabupaten Poso
7. Kabupaten Morowali
8. Kabupaten Morowali Utara
9. Kabupaten Banggai
10. Kabupaten Banggai Kepulauan
11. Kabupaten Banggai Laut
12. Kabupaten Toli-Toli
13. Kabupaten Buol

## Testing

Untuk memverifikasi tidak ada duplikat, jalankan:

```bash
docker-compose exec -T app php check-wilayah.php
```

Script ini akan menampilkan:
- Daftar semua wilayah
- Deteksi duplikat (jika ada)

## Catatan Penting

- **Jangan hapus** file `fix-duplicate-wilayah.php` dan `check-wilayah.php` - berguna untuk maintenance
- Setiap upload data sekolah baru, sistem akan otomatis menormalisasi nama wilayah
- Jika menemukan wilayah baru yang duplikat, tambahkan aturan normalisasi di method `normalizeWilayahName()`

## Tanggal Perbaikan

28 November 2025, 00:27 WIB
