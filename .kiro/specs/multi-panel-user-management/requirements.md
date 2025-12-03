# Requirements Document

## Introduction

Dokumen ini mendefinisikan kebutuhan untuk implementasi Multi-Panel User Management System pada aplikasi BIRMAN (Bank Informasi Rapor Mutu Asesmen Nasional). Sistem ini akan memisahkan akses antara tiga jenis pengguna:

1. **Super Admin** - Akses penuh ke semua data dan fitur
2. **Admin Wilayah** - Akses terbatas pada wilayah dan jenjang pendidikan tertentu yang ditugaskan
3. **User Sekolah** - Akses terbatas pada data sekolah sendiri dengan fitur yang dikonfigurasi oleh Super Admin

## Glossary

-   **Super Admin**: Pengguna dengan hak akses penuh ke seluruh sistem dan semua data
-   **Admin Wilayah**: Pengguna dengan hak akses terbatas pada wilayah dan jenjang pendidikan tertentu yang ditugaskan oleh Super Admin
-   **User Sekolah**: Pengguna dari sekolah tertentu dengan akses terbatas pada data sekolahnya sendiri
-   **Panel**: Interface terpisah dalam Filament untuk grup pengguna berbeda
-   **Wilayah**: Entitas geografis (Provinsi/Kabupaten/Kota) yang menjadi batasan akses data
-   **Jenjang Pendidikan**: Tingkat pendidikan (SD, SMP, SMA, SMK, dll) yang menjadi batasan akses data
-   **BIRMAN**: Bank Informasi Rapor Mutu Asesmen Nasional - aplikasi utama
-   **Activity Log**: Catatan aktivitas pengguna dalam sistem
-   **Role**: Peran pengguna yang menentukan hak akses (super_admin, admin_wilayah, user_sekolah)
-   **Permission**: Izin spesifik untuk melakukan aksi tertentu dalam sistem

## Requirements

### Requirement 1

**User Story:** Sebagai Super Admin, saya ingin memiliki akses penuh ke semua fitur dan data, sehingga saya dapat mengelola seluruh sistem tanpa batasan.

#### Acceptance Criteria

1. WHEN Super Admin login ke sistem THEN sistem SHALL menampilkan Admin Panel dengan akses ke semua resource
2. WHEN Super Admin mengakses data wilayah THEN sistem SHALL menampilkan data dari semua wilayah tanpa filter
3. WHEN Super Admin mengakses data jenjang pendidikan THEN sistem SHALL menampilkan data dari semua jenjang tanpa filter
4. WHEN Super Admin mengelola pengguna THEN sistem SHALL mengizinkan pembuatan, edit, dan hapus semua jenis pengguna
5. WHEN Super Admin mengakses Activity Log THEN sistem SHALL menampilkan log aktivitas dari semua pengguna
6. WHEN Super Admin mengakses Backup Manager THEN sistem SHALL mengizinkan backup dan restore database

### Requirement 2

**User Story:** Sebagai Admin Wilayah, saya ingin mengakses data sesuai wilayah dan jenjang pendidikan yang ditugaskan, sehingga saya dapat mengelola data dengan efektif.

#### Acceptance Criteria

1. WHEN Admin Wilayah login ke sistem THEN sistem SHALL menampilkan Wilayah Panel dengan resource terbatas
2. WHEN Admin Wilayah mengakses data sekolah THEN sistem SHALL hanya menampilkan sekolah dalam wilayah DAN jenjang pendidikan yang ditugaskan
3. WHEN Admin Wilayah mengakses data pelaksanaan asesmen THEN sistem SHALL hanya menampilkan data dari wilayah DAN jenjang pendidikan yang ditugaskan
4. WHEN Admin Wilayah mencoba mengakses data di luar wilayah atau jenjangnya THEN sistem SHALL menolak akses dan menampilkan pesan error
5. WHEN Admin Wilayah mengakses request download THEN sistem SHALL hanya menampilkan request dari wilayah DAN jenjang pendidikan yang ditugaskan
6. WHEN Admin Wilayah melihat dashboard THEN sistem SHALL menampilkan statistik hanya untuk wilayah DAN jenjang pendidikan yang ditugaskan

### Requirement 3

**User Story:** Sebagai User Sekolah, saya ingin mengakses data sekolah saya sendiri, sehingga saya dapat melihat dan mengelola informasi sekolah.

#### Acceptance Criteria

1. WHEN User Sekolah login ke sistem THEN sistem SHALL menampilkan Sekolah Panel dengan fitur yang dikonfigurasi oleh Super Admin
2. WHEN User Sekolah mengakses data sekolah THEN sistem SHALL hanya menampilkan data sekolah yang terkait dengan akun tersebut
3. WHEN User Sekolah mengakses data pelaksanaan asesmen THEN sistem SHALL hanya menampilkan data asesmen sekolahnya sendiri
4. WHEN User Sekolah mencoba mengakses data sekolah lain THEN sistem SHALL menolak akses dan menampilkan pesan error
5. WHEN User Sekolah mengakses fitur yang tidak diizinkan THEN sistem SHALL menyembunyikan menu dan menolak akses langsung

### Requirement 4

**User Story:** Sebagai Super Admin, saya ingin mengelola pengguna Admin Wilayah dengan pengaturan wilayah dan jenjang, sehingga saya dapat mengontrol akses data secara granular.

#### Acceptance Criteria

1. WHEN Super Admin membuat Admin Wilayah baru THEN sistem SHALL menyimpan data pengguna dengan role admin_wilayah, wilayah, dan jenjang pendidikan yang ditugaskan
2. WHEN Super Admin mengedit Admin Wilayah THEN sistem SHALL mengizinkan perubahan wilayah dan jenjang pendidikan yang ditugaskan
3. WHEN Super Admin menonaktifkan Admin Wilayah THEN sistem SHALL mencegah pengguna tersebut login ke sistem
4. WHEN Super Admin melihat daftar pengguna THEN sistem SHALL menampilkan semua pengguna dengan informasi role, wilayah, dan jenjang
5. WHEN Super Admin menghapus Admin Wilayah THEN sistem SHALL menghapus pengguna dan mencatat aktivitas di Activity Log

### Requirement 5

**User Story:** Sebagai Super Admin, saya ingin mengelola pengguna User Sekolah dengan pengaturan permission, sehingga saya dapat mengontrol fitur yang dapat diakses.

#### Acceptance Criteria

1. WHEN Super Admin membuat User Sekolah baru THEN sistem SHALL menyimpan data pengguna dengan role user_sekolah dan sekolah yang terkait
2. WHEN Super Admin mengatur permission User Sekolah THEN sistem SHALL menyimpan daftar fitur yang diizinkan untuk pengguna tersebut
3. WHEN Super Admin menonaktifkan User Sekolah THEN sistem SHALL mencegah pengguna tersebut login ke sistem
4. WHEN Super Admin melihat daftar User Sekolah THEN sistem SHALL menampilkan informasi sekolah dan permission yang dimiliki
5. WHEN Super Admin menghapus User Sekolah THEN sistem SHALL menghapus pengguna dan mencatat aktivitas di Activity Log

### Requirement 6

**User Story:** Sebagai sistem, saya ingin memvalidasi akses pengguna berdasarkan role, wilayah, jenjang, dan permission, sehingga keamanan data terjaga.

#### Acceptance Criteria

1. WHEN pengguna tanpa role yang valid mencoba login THEN sistem SHALL menolak akses dan menampilkan pesan error
2. WHEN Admin Wilayah mencoba mengakses Admin Panel THEN sistem SHALL menolak akses dan redirect ke Wilayah Panel
3. WHEN User Sekolah mencoba mengakses Admin Panel atau Wilayah Panel THEN sistem SHALL menolak akses dan redirect ke Sekolah Panel
4. WHEN pengguna mengakses resource THEN sistem SHALL memvalidasi permission berdasarkan role dan konfigurasi
5. WHEN pengguna login THEN sistem SHALL mencatat aktivitas login di Activity Log dengan IP dan user agent

### Requirement 7

**User Story:** Sebagai Super Admin, saya ingin melihat Activity Log yang mencatat semua aktivitas pengguna, sehingga saya dapat mengaudit penggunaan sistem.

#### Acceptance Criteria

1. WHEN pengguna melakukan login THEN sistem SHALL mencatat aktivitas dengan action "login", IP address, dan user agent
2. WHEN pengguna melakukan logout THEN sistem SHALL mencatat aktivitas dengan action "logout"
3. WHEN Admin melakukan approve/reject request THEN sistem SHALL mencatat aktivitas dengan detail request
4. WHEN Admin melakukan backup/restore THEN sistem SHALL mencatat aktivitas dengan detail operasi
5. WHEN Super Admin melihat Activity Log THEN sistem SHALL menampilkan filter berdasarkan user, action, role, dan tanggal

### Requirement 8

**User Story:** Sebagai pengguna, saya ingin dapat mengelola profil saya sendiri, sehingga saya dapat memperbarui informasi akun.

#### Acceptance Criteria

1. WHEN pengguna mengakses halaman profil THEN sistem SHALL menampilkan form edit dengan data pengguna saat ini
2. WHEN pengguna mengubah password THEN sistem SHALL memvalidasi password lama dan menyimpan password baru yang di-hash
3. WHEN pengguna mengupload avatar THEN sistem SHALL menyimpan file dan menampilkan avatar baru
4. WHEN pengguna menyimpan perubahan profil THEN sistem SHALL memvalidasi data dan menyimpan perubahan
