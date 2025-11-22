# Implementasi Struktur Menu Data Statistik dan Halaman Asesmen Nasional

## 📅 Tanggal: 21 November 2025

---

## 🎯 Tujuan

Mengubah struktur dashboard menjadi lebih profesional dengan:

1. Menu dropdown "Data Statistik" dengan sub-menu per tahun (2023, 2024, 2025)
2. Halaman agregat data per wilayah untuk setiap tahun
3. Halaman detail per wilayah dengan filter jenjang pendidikan
4. Desain modern dengan card layout dan statistik visual

---

## 🗂️ Perubahan Database

### Migration: `add_status_sekolah_to_sekolah_table`

-   Menambahkan kolom `status_sekolah` (enum: 'Negeri', 'Swasta') pada tabel `sekolah`

### Migration: `add_keterangan_to_pelaksanaan_asesmen_table`

-   Menambahkan kolom `keterangan` (text, nullable) pada tabel `pelaksanaan_asesmen`

**Jalankan migration:**

```bash
php artisan migrate
```

---

## 🛣️ Routes Baru

```php
// Halaman agregat per tahun
GET /asesmen-nasional/{tahun}
Route name: asesmen-nasional.index

// Halaman detail wilayah per tahun
GET /asesmen-nasional/{tahun}/wilayah/{wilayah}
Route name: asesmen-nasional.wilayah
```

---

## 🎨 Struktur Halaman

### 1. Halaman Agregat (`/asesmen-nasional/{tahun}`)

**Komponen:**

-   **Header Stats**: Kotak biru dengan jumlah satuan pendidikan per jenjang (SMA, SMK, SMP, SD, SLB, Kesetaraan)
-   **Tabel Agregat**: Menampilkan semua wilayah dengan jumlah sekolah per jenjang
-   **Fitur**: Search wilayah, pagination, clickable rows

**Livewire Components:**

-   `AsesmenStatsHeader` - Menampilkan stats header
-   `WilayahAggregateTable` - Menampilkan tabel agregat per wilayah

### 2. Halaman Detail Wilayah (`/asesmen-nasional/{tahun}/wilayah/{id}`)

**Komponen:**

-   **Breadcrumb**: Navigasi kembali
-   **Header dengan logo wilayah**
-   **Stats Header**: Stats khusus untuk wilayah tersebut
-   **Tab Filter**: Filter berdasarkan jenjang (SEMUA, SMA, SMK, SMP, SD, SLB, KESETARAAN)
-   **Tabel Detail Sekolah**: Menampilkan data per sekolah

**Kolom Tabel:**

-   Nama Sekolah
-   Status Sekolah (Negeri/Swasta)
-   Peserta
-   Partisipasi Literasi (%)
-   Partisipasi Numerasi (%)
-   Keterangan

**Livewire Components:**

-   `AsesmenStatsHeader` - Menampilkan stats header (dengan filter wilayah)
-   `WilayahDetailTable` - Menampilkan tabel detail sekolah

---

## 🧩 Controller

### `AsesmenNasionalController`

**Methods:**

1. `index($tahun)` - Menampilkan halaman agregat per tahun
2. `wilayah($tahun, $wilayahId)` - Menampilkan halaman detail wilayah

---

## 🎨 Livewire Components

### 1. `AsesmenStatsHeader`

-   **Props**: `$tahun`, `$wilayahId` (optional)
-   **Function**: Menampilkan statistik jumlah sekolah per jenjang
-   **View**: Kotak biru gradient responsive dengan hover effect

### 2. `WilayahAggregateTable`

-   **Props**: `$tahun`
-   **Features**: Search, pagination, clickable rows
-   **View**: Tabel responsive dengan badge untuk jumlah sekolah

### 3. `WilayahDetailTable`

-   **Props**: `$tahun`, `$wilayahId`
-   **Features**: Filter jenjang (tabs), search, pagination, color coding partisipasi
-   **View**: Tabel detail dengan badge status sekolah

---

## 📱 Navigation Menu

### Desktop Menu

-   Dropdown "Data Statistik" dengan hover effect
-   Sub-menu: Asesmen Nasional 2023, 2024, 2025
-   Responsive design

### Mobile Menu

-   Hamburger menu
-   Collapsible navigation
-   Touch-friendly

---

## 🎨 Design Highlights

### Color Scheme

-   **Primary**: Blue gradient (from-blue-600 to-blue-700)
-   **Hover**: Blue-50 background
-   **Status Negeri**: Green-100 badge
-   **Status Swasta**: Yellow-100 badge
-   **Partisipasi 100%**: Green text
-   **Partisipasi ≥80%**: Blue text
-   **Partisipasi <80%**: Orange text

### Interactive Elements

-   Hover effects on cards and rows
-   Loading indicator (blue progress bar)
-   Smooth transitions
-   Scale transform on stats cards

### Responsive Design

-   Mobile-first approach
-   Grid layout adjusts for screen size
-   Horizontal scroll for tables on mobile
-   Collapsible mobile menu

---

## 🔍 Fitur Tambahan

### Search

-   Real-time search dengan debounce 300ms
-   Search di halaman agregat: cari nama wilayah
-   Search di halaman detail: cari nama sekolah

### Pagination

-   Laravel default pagination
-   Livewire pagination support
-   15 items per page (agregat)
-   10 items per page (detail)

### Filter

-   Tab filter by jenjang di halaman detail
-   Reset page saat filter berubah

### Visual Feedback

-   Empty state dengan icon dan message
-   Loading indicator saat fetching data
-   Badge dan color coding untuk data status

---

## 📊 Data Flow

```
Route → Controller → View → Livewire Component
                              ↓
                         Query Database
                              ↓
                         Render Data
```

### Query Optimization

-   Eager loading relationships: `with(['sekolah.jenjangPendidikan'])`
-   Distinct count untuk menghindari duplikasi
-   Index pada foreign keys (sudah ada dari migration sebelumnya)

---

## 🚀 Testing

### URL untuk Testing:

1. **Halaman Agregat 2023**

    ```
    http://localhost/asesmen-nasional/2023
    ```

2. **Halaman Agregat 2024**

    ```
    http://localhost/asesmen-nasional/2024
    ```

3. **Halaman Detail Wilayah** (contoh: Kota Palu, wilayah_id = 1)
    ```
    http://localhost/asesmen-nasional/2023/wilayah/1
    ```

### Test Cases:

-   ✅ Navigation dropdown berfungsi
-   ✅ Stats header menampilkan data yang benar
-   ✅ Tabel agregat menampilkan semua wilayah
-   ✅ Search wilayah berfungsi
-   ✅ Click row membuka halaman detail
-   ✅ Filter jenjang di halaman detail berfungsi
-   ✅ Search sekolah berfungsi
-   ✅ Pagination berfungsi
-   ✅ Responsive design di mobile

---

## 📝 Catatan

### Data Requirements:

-   Pastikan ada data di tabel `siklus_asesmen` untuk tahun 2023, 2024, 2025
-   Pastikan ada data di tabel `pelaksanaan_asesmen` yang ter-relasi
-   Kolom `status_sekolah` dan `keterangan` bersifat nullable (bisa kosong)

### Future Enhancements:

-   Export data ke Excel/PDF
-   Grafik visualisasi data
-   Filter tambahan (status sekolah, moda pelaksanaan)
-   Print-friendly view
-   Share link untuk data tertentu

---

## 🔄 Git Workflow

```bash
# Branch: dev
git add .
git commit -m "Implementasi struktur menu Data Statistik dan halaman asesmen per tahun"
git push origin dev

# Untuk merge ke main setelah testing:
git checkout main
git merge dev
git push origin main
```

---

## 📞 Support

Jika ada kendala atau pertanyaan, hubungi developer atau cek dokumentasi Laravel Livewire:

-   https://laravel-livewire.com/docs
-   https://tailwindcss.com/docs

---

**Last Updated**: 21 November 2025
**Version**: 1.0.0
**Branch**: dev
