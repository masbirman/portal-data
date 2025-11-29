# Perbaikan Duplikat Wilayah - FINAL

## ⚠️ Masalah yang Ditemukan

Setiap kali upload data sekolah, sistem membuat wilayah baru jika nama wilayah ditulis dengan format berbeda. Contoh:
- "Kabupaten Donggala" vs "Kab. Donggala"
- "Kabupaten Toli-Toli" vs "Kab. Tolitoli"
- "Kabupaten Tojo Una-Una" vs "Kab. Tojo Unauna"

Ini menyebabkan tabel "Rekap Sulawesi Tengah" di dashboard menampilkan wilayah yang sama berkali-kali.

## 🔍 Penyebab Root Cause

Ada **DUA masalah** di file `app/Imports/SekolahImport.php`:

### 1. Method `getWilayahId()` tidak menormalisasi nama
```php
// SEBELUM (SALAH)
$wilayah = Wilayah::create(['nama' => ucwords($lowerName)]);
// "kab. donggala" -> "Kab. Donggala" (SALAH!)
```

### 2. Method `preloadCache()` tidak menormalisasi cache
```php
// SEBELUM (SALAH)
$this->wilayahCache = Wilayah::all()->mapWithKeys(function ($item) {
    return [strtolower($item->nama) => $item->id];
});
// Cache: "kab. donggala" => ID 14
// Lookup: "kabupaten donggala" => TIDAK DITEMUKAN! -> Buat baru!
```

**Akibatnya:** Meskipun ada normalisasi di `getWilayahId()`, cache tidak cocok sehingga tetap membuat wilayah baru!

## ✅ Solusi yang Diterapkan

### 1. Menambahkan Method `normalizeWilayahName()`

Method ini menormalisasi nama wilayah dengan aturan:
- ✅ Mengganti "Kab." atau "Kab " → "Kabupaten "
- ✅ Menormalisasi spasi ekstra
- ✅ Memperbaiki kasus khusus:
  - "Tolitoli" → "Toli-Toli"
  - "Tojo Unauna" → "Tojo Una-Una"
  - "Tojo Una-una" → "Tojo Una-Una"
- ✅ Menerapkan Title Case yang konsisten

### 2. Memperbaiki `getWilayahId()`

```php
// SESUDAH (BENAR)
protected function getWilayahId($name)
{
    // Normalize wilayah name to prevent duplicates
    $normalizedName = $this->normalizeWilayahName($name);
    $lowerName = strtolower($normalizedName);
    
    if (isset($this->wilayahCache[$lowerName])) {
        return $this->wilayahCache[$lowerName];
    }

    // Create new if not found
    $wilayah = Wilayah::create(['nama' => $normalizedName]);
    $this->wilayahCache[$lowerName] = $wilayah->id;
    return $wilayah->id;
}
```

### 3. Memperbaiki `preloadCache()` - **KUNCI UTAMA!**

```php
// SESUDAH (BENAR)
protected function preloadCache()
{
    // Cache all Wilayah: normalized lowercase name -> id
    $this->wilayahCache = Wilayah::all()->mapWithKeys(function ($item) {
        // Normalize the wilayah name from database before caching
        $normalizedName = $this->normalizeWilayahName($item->nama);
        return [strtolower($normalizedName) => $item->id];
    })->toArray();
    // ...
}
```

**Sekarang:**
- Database punya: "Kab. Donggala" (ID: 2)
- Cache: "kabupaten donggala" => ID 2
- Input: "Kab. Donggala" → normalized: "Kabupaten Donggala" → lowercase: "kabupaten donggala"
- Lookup cache: DITEMUKAN! → Gunakan ID 2 (TIDAK BUAT BARU!)

### 4. Membersihkan Data Duplikat

Script `fix-duplicate-wilayah-v2.php` telah dijalankan untuk:
- Mendeteksi duplikat secara dinamis berdasarkan nama yang dinormalisasi
- Menggabungkan 12 wilayah duplikat menjadi 13 wilayah unik
- Memperbarui referensi di tabel `sekolah` dan `pelaksanaan_asesmen`
- Menghapus wilayah duplikat

## 📊 Hasil Akhir

**Wilayah yang tersisa (13 wilayah):**
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

## 🧪 Testing

### 1. Cek Wilayah di Database
```bash
docker-compose exec -T app php check-wilayah.php
```

### 2. Test Normalisasi
```bash
docker-compose exec -T app php test-normalisasi-wilayah.php
```

### 3. Bersihkan Duplikat (jika ada)
```bash
docker-compose exec -T app php fix-duplicate-wilayah-v2.php
```

## 📝 File yang Dibuat/Dimodifikasi

1. ✏️ `app/Imports/SekolahImport.php` - **PERBAIKAN UTAMA**
   - Ditambahkan method `normalizeWilayahName()`
   - Diperbaiki `getWilayahId()` untuk menggunakan normalisasi
   - **Diperbaiki `preloadCache()` untuk menormalisasi cache** ← KUNCI!
   
2. 📄 `fix-duplicate-wilayah-v2.php` - Script pembersihan dinamis
3. 📄 `check-wilayah.php` - Script verifikasi
4. 📄 `test-normalisasi-wilayah.php` - Script testing normalisasi
5. 📄 `docs/PERBAIKAN_DUPLIKAT_WILAYAH_FINAL.md` - Dokumentasi ini

## ⚠️ Catatan Penting

- **Upload data baru sekarang TIDAK akan membuat duplikat**
- Jika menemukan duplikat baru, jalankan `fix-duplicate-wilayah-v2.php`
- Jika ada wilayah baru yang perlu aturan khusus, tambahkan di `normalizeWilayahName()`

## 🎯 Verifikasi

Silakan:
1. **Refresh halaman dashboard** di browser (http://localhost:8080/dashboard)
2. **Upload data baru** untuk memastikan tidak ada duplikat
3. **Cek tabel "Rekap Sulawesi Tengah"** - harus menampilkan **13 baris** saja

## Tanggal Perbaikan

28 November 2025, 00:38 WIB

---

**Status: ✅ SELESAI & TESTED**
