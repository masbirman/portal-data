# Requirements Document

## Introduction

Fitur Direktori Sekolah adalah halaman publik yang menampilkan daftar sekolah dalam bentuk card grid dengan kemampuan pencarian dan filter. Pengguna dapat melihat informasi dasar sekolah dan mengklik untuk melihat halaman detail yang menampilkan statistik lengkap, lokasi peta, dan data asesmen sekolah tersebut.

## Glossary

- **Direktori_Sekolah**: Sistem yang menampilkan daftar dan detail informasi sekolah
- **Card_Sekolah**: Komponen UI yang menampilkan ringkasan informasi sekolah dalam format kartu
- **Detail_Sekolah**: Halaman yang menampilkan informasi lengkap dan statistik sekolah
- **NPSN**: Nomor Pokok Sekolah Nasional (menggunakan field kode_sekolah)
- **Jenjang_Pendidikan**: Tingkat pendidikan sekolah (SD, SMP, SMA, dll)
- **Wilayah**: Kabupaten/Kota lokasi sekolah
- **Status_Sekolah**: Klasifikasi sekolah (Negeri/Swasta)
- **Statistik_Sekolah**: Data agregat dari pelaksanaan asesmen sekolah

## Requirements

### Requirement 1

**User Story:** As a public user, I want to view a list of schools in a card grid format, so that I can browse and discover schools easily.

#### Acceptance Criteria

1. WHEN a user visits the school directory page THEN the Direktori_Sekolah SHALL display schools in a responsive card grid layout with pagination
2. WHEN displaying a Card_Sekolah THEN the Direktori_Sekolah SHALL show school name, kode_sekolah (NPSN), wilayah name, status_sekolah, and jenjang_pendidikan
3. WHEN a Card_Sekolah is rendered THEN the Direktori_Sekolah SHALL display a single illustration image as placeholder for all schools
4. IF alamat data is available THEN the Card_Sekolah SHALL display the alamat below the school name
5. WHEN the page loads THEN the Direktori_Sekolah SHALL show the total count of schools matching current filters
6. WHEN pagination is used THEN the Direktori_Sekolah SHALL load the next set of schools without full page reload

### Requirement 2

**User Story:** As a public user, I want to search and filter schools, so that I can find specific schools quickly.

#### Acceptance Criteria

1. WHEN a user enters a search term THEN the Direktori_Sekolah SHALL filter schools by name or kode_sekolah containing the search term
2. WHEN a user selects a wilayah filter THEN the Direktori_Sekolah SHALL display only schools from that wilayah
3. WHEN a user selects a jenjang_pendidikan filter THEN the Direktori_Sekolah SHALL display only schools of that education level
4. WHEN a user selects a status_sekolah filter (Negeri/Swasta/Semua) THEN the Direktori_Sekolah SHALL display only schools matching that status
5. WHEN a user clicks the reset button THEN the Direktori_Sekolah SHALL clear all filters and show all schools
6. WHEN filters are applied THEN the Direktori_Sekolah SHALL update the school count and results immediately

### Requirement 3

**User Story:** As a public user, I want to view detailed information about a specific school, so that I can learn more about that school.

#### Acceptance Criteria

1. WHEN a user clicks "Lihat" button on a Card_Sekolah THEN the Direktori_Sekolah SHALL navigate to the Detail_Sekolah page for that school
2. WHEN the Detail_Sekolah page loads THEN the Direktori_Sekolah SHALL display school header with name, wilayah, status_sekolah, jenjang_pendidikan, and kode_sekolah
3. IF alamat data is available THEN the Detail_Sekolah page SHALL display the alamat in the header section
4. WHEN the Detail_Sekolah page loads THEN the Direktori_Sekolah SHALL display a breadcrumb navigation showing "Beranda > Sekolah > Detail Sekolah"
5. WHEN the Detail_Sekolah page loads THEN the Direktori_Sekolah SHALL display school location on an interactive map using Leaflet.js with OpenStreetMap tiles and the school's latitude and longitude coordinates
6. WHEN the map is displayed THEN the Direktori_Sekolah SHALL show a marker at the school location with a popup containing the school name
7. WHEN a user clicks "Buka di Google Maps" link THEN the Direktori_Sekolah SHALL open Google Maps in a new tab with the school coordinates
8. IF the school has no latitude or longitude data THEN the Direktori_Sekolah SHALL display a message indicating location is not available instead of the map

### Requirement 4

**User Story:** As a public user, I want to see school statistics and assessment data, so that I can understand the school's performance.

#### Acceptance Criteria

1. WHEN the Detail_Sekolah page loads THEN the Direktori_Sekolah SHALL display Statistik_Sekolah section with aggregated assessment data
2. WHEN displaying Statistik_Sekolah THEN the Direktori_Sekolah SHALL show total jumlah_peserta from all pelaksanaan_asesmen records
3. WHEN displaying Statistik_Sekolah THEN the Direktori_Sekolah SHALL show average partisipasi_literasi and partisipasi_numerasi percentages
4. WHEN displaying Statistik_Sekolah THEN the Direktori_Sekolah SHALL show assessment history by siklus_asesmen in a table or chart format
5. IF the school has no assessment data THEN the Direktori_Sekolah SHALL display a message indicating no assessment data is available

### Requirement 5

**User Story:** As a public user, I want to see nearby schools, so that I can discover other schools in the same area.

#### Acceptance Criteria

1. WHEN the Detail_Sekolah page loads THEN the Direktori_Sekolah SHALL display a "Sekolah Sekitar" section showing schools in the same wilayah
2. WHEN displaying nearby schools THEN the Direktori_Sekolah SHALL show up to 5 schools from the same wilayah excluding the current school
3. WHEN a user clicks on a nearby school THEN the Direktori_Sekolah SHALL navigate to that school's Detail_Sekolah page

### Requirement 6

**User Story:** As a public user, I want the school directory to be accessible from the main navigation, so that I can easily find and access the feature.

#### Acceptance Criteria

1. WHEN the public layout is rendered THEN the Direktori_Sekolah menu item SHALL appear in the main navigation bar
2. WHEN a user clicks the "Sekolah" menu item THEN the Direktori_Sekolah SHALL navigate to the school directory listing page
3. WHEN the mobile menu is opened THEN the Direktori_Sekolah menu item SHALL appear in the mobile navigation

### Requirement 7

**User Story:** As a public user, I want the school directory to be responsive and performant, so that I can use it on any device.

#### Acceptance Criteria

1. WHEN the page is viewed on mobile devices THEN the Direktori_Sekolah SHALL display cards in a single column layout
2. WHEN the page is viewed on tablet devices THEN the Direktori_Sekolah SHALL display cards in a two-column layout
3. WHEN the page is viewed on desktop devices THEN the Direktori_Sekolah SHALL display cards in a four-column layout
4. WHEN loading school data THEN the Direktori_Sekolah SHALL implement lazy loading for pagination to optimize performance
5. WHEN the page loads THEN the Direktori_Sekolah SHALL display a loading indicator while fetching data
