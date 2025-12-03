# Implementation Plan

## Phase 1: Database & Model Foundation

-   [x] 1. Setup database migrations untuk user management

    -   [x] 1.1 Buat migration untuk menambah kolom role, sekolah_id, is_active pada tabel users
    -   [x] 1.2 Buat migration untuk tabel user_wilayah (pivot)
    -   [x] 1.3 Buat migration untuk tabel user_jenjang (pivot)
    -   [x] 1.4 Buat migration untuk tabel permissions
    -   [x] 1.5 Buat migration untuk tabel user_permission (pivot)

-   [x] 2. Update User model dengan relationships dan methods

    -   [x] 2.1 Tambah fillable attributes (role, sekolah_id, is_active)
    -   [x] 2.2 Tambah relationships (wilayahs, jenjangs, sekolah, permissions)
    -   [x] 2.3 Tambah role checker methods (isSuperAdmin, isAdminWilayah, isUserSekolah)
    -   [x] 2.4 Tambah helper methods (hasPermission, getWilayahIds, getJenjangIds)
    -   [x] 2.5 Update canAccessPanel() method untuk multi-panel routing
    -   [x] 2.6 Write property test untuk User model - Property 4: Role-Based Panel Access

-   [x] 3. Buat Permission model

    -   [x] 3.1 Buat model Permission dengan constants untuk available permissions
    -   [x] 3.2 Buat seeder untuk default permissions

-   [x] 4. Checkpoint - Pastikan semua migrations dan models berjalan

## Phase 2: Global Scopes & Middleware

-   [x] 5. Buat Global Scopes untuk data filtering

    -   [x] 5.1 Buat WilayahJenjangScope untuk Admin Wilayah
    -   [x] 5.2 Buat SekolahScope untuk User Sekolah
    -   [x] 5.3 Write property test untuk Admin Wilayah Data Isolation - Property 1
    -   [x] 5.4 Write property test untuk User Sekolah Data Isolation - Property 2

-   [x] 6. Buat Middleware untuk access control

    -   [x] 6.1 Buat EnsureUserHasRole middleware
    -   [x] 6.2 Buat EnsureUserIsActive middleware
    -   [x] 6.3 Buat CheckPermission middleware
    -   [x] 6.4 Write property test untuk User Deactivation Enforcement - Property 7

-   [x] 7. Checkpoint - Pastikan scopes dan middleware berfungsi

## Phase 3: Panel Providers

-   [x] 8. Update AdminPanelProvider untuk Super Admin

    -   [x] 8.1 Tambah middleware EnsureUserHasRole dengan role super_admin
    -   [x] 8.2 Tambah middleware EnsureUserIsActive
    -   [x] 8.3 Write property test untuk Super Admin Full Access - Property 3

-   [x] 9. Buat WilayahPanelProvider untuk Admin Wilayah

    -   [x] 9.1 Buat WilayahPanelProvider dengan path /wilayah
    -   [x] 9.2 Konfigurasi resources yang tersedia
    -   [x] 9.3 Tambah middleware EnsureUserHasRole dengan role admin_wilayah
    -   [x] 9.4 Tambah middleware EnsureUserIsActive
    -   [x] 9.5 Buat custom Login page untuk Wilayah Panel
    -   [x] 9.6 Buat Dashboard page dengan statistik wilayah dan jenjang

-   [x] 10. Buat SekolahPanelProvider untuk User Sekolah

    -   [x] 10.1 Buat SekolahPanelProvider dengan path /sekolah
    -   [x] 10.2 Konfigurasi resources berdasarkan permissions
    -   [x] 10.3 Tambah middleware EnsureUserHasRole dengan role user_sekolah
    -   [x] 10.4 Tambah middleware EnsureUserIsActive
    -   [x] 10.5 Buat custom Login page untuk Sekolah Panel
    -   [x] 10.6 Buat Dashboard page dengan data sekolah
    -   [x] 10.7 Write property test untuk Permission-Based Feature Access - Property 5

-   [x] 11. Checkpoint - Pastikan semua panel dapat diakses sesuai role

## Phase 4: User Management Resources

-   [x] 12. Buat UserResource untuk Super Admin

    -   [x] 12.1 Buat UserResource dengan form untuk create/edit user
    -   [x] 12.2 Buat table dengan kolom dan filters
    -   [x] 12.3 Tambah actions (edit, delete, toggle active)
    -   [x] 12.4 Integrasikan Activity Log untuk CRUD operations
    -   [x] 12.5 Write property test untuk User CRUD with Logging - Property 9

-   [x] 13. Checkpoint - Pastikan user management berfungsi

## Phase 5: Apply Scopes to Existing Resources

-   [x] 14. Update SekolahResource untuk multi-panel

    -   [x] 14.1 Apply WilayahJenjangScope untuk Admin Wilayah
    -   [x] 14.2 Apply SekolahScope untuk User Sekolah
    -   [x] 14.3 Kondisikan visibility berdasarkan panel

-   [x] 15. Update PelaksanaanAsesmenResource untuk multi-panel

    -   [x] 15.1 Apply WilayahJenjangScope untuk Admin Wilayah
    -   [x] 15.2 Apply SekolahScope untuk User Sekolah
    -   [x] 15.3 Kondisikan visibility berdasarkan panel

-   [x] 16. DownloadRequestResource - Khusus Super Admin Only

    -   [x] 16.1 Resource ini hanya tersedia di Admin Panel (Super Admin)
    -   [x] 16.2 Tidak perlu multi-panel karena fitur download khusus Super Admin
    -   [x] 16.3 Cross-Boundary Access sudah ter-handle oleh panel middleware

-   [x] 17. Checkpoint - Pastikan semua resources terfilter dengan benar

## Phase 6: Activity Log Enhancement

-   [x] 18. Update Activity Log untuk multi-panel
    -   [x] 18.1 Tambah logging untuk login/logout di semua panel
    -   [x] 18.2 Tambah filter role pada ActivityLogResource
    -   [x] 18.3 Pastikan semua CRUD operations ter-log
    -   [x] 18.4 Write property test untuk Activity Logging Completeness - Property 6

## Phase 7: Profile & Password Management

-   [x] 19. Update Profile page untuk semua panel

    -   [x] 19.1 Pastikan EditProfile tersedia di semua panel
    -   [x] 19.2 Implementasi password change dengan validasi password lama
    -   [x] 19.3 Implementasi avatar upload
    -   [x] 19.4 Write property test untuk Password Change Validation - Property 8

-   [x] 20. Final Checkpoint - Pastikan semua fitur berfungsi

## Phase 8: Seeder & Documentation

-   [x] 21. Buat seeder untuk testing
    -   [x] 21.1 Buat seeder untuk Super Admin default
    -   [x] 21.2 Buat seeder untuk sample Admin Wilayah
    -   [x] 21.3 Buat seeder untuk sample User Sekolah
    -   [x] 21.4 Buat seeder untuk permissions
