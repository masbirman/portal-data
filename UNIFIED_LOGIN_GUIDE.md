# Panduan Unified Login System

## Perubahan yang Dilakukan

Sistem login telah diubah dari multiple login pages menjadi single unified login page untuk semua user.

### Sebelumnya:
- `/admin/login` - untuk super_admin
- `/sekolah/login` - untuk user_sekolah
- `/wilayah/login` - untuk admin_wilayah

### Sekarang:
- `/login` - untuk semua user (super_admin, admin_wilayah, user_sekolah)

## Cara Kerja

1. **Semua user login di `/login`**
   - User memasukkan username/email dan password
   - Sistem akan memvalidasi kredensial

2. **Auto-redirect berdasarkan role**
   - Setelah login berhasil, sistem otomatis mengarahkan user ke panel yang sesuai:
     - `super_admin` → `/admin`
     - `admin_wilayah` → `/wilayah`
     - `user_sekolah` → `/sekolah`

3. **Logout**
   - Semua panel akan logout dan redirect ke `/login`

## File yang Diubah/Dibuat

### File Baru:
1. `app/Http/Controllers/Auth/UnifiedLoginController.php` - Controller untuk unified login
2. `app/Http/Responses/LogoutResponse.php` - Custom logout response
3. `app/Http/Middleware/Authenticate.php` - Custom authenticate middleware
4. `resources/views/auth/login.blade.php` - Unified login view

### File yang Dimodifikasi:
1. `routes/web.php` - Menambahkan route `/login` dan `/logout`
2. `app/Providers/Filament/AdminPanelProvider.php` - Menonaktifkan login page
3. `app/Providers/Filament/SekolahPanelProvider.php` - Menonaktifkan login page
4. `app/Providers/Filament/WilayahPanelProvider.php` - Menonaktifkan login page
5. `bootstrap/app.php` - Menambahkan redirect guests to `/login`
6. `resources/views/public/layout.blade.php` - Menambahkan link login di navbar

## Fitur

- ✅ Single login page untuk semua user
- ✅ Auto-redirect berdasarkan role user
- ✅ Rate limiting (5 percobaan per menit)
- ✅ Remember me functionality
- ✅ Activity logging untuk login/logout
- ✅ Validasi user aktif/non-aktif
- ✅ Support login dengan username atau email
- ✅ Responsive design
- ✅ Error handling yang baik

## Testing

### Test Login:
1. Buka browser dan akses `http://localhost/login` atau `https://yourdomain.com/login`
2. Login dengan kredensial user:
   - Super Admin → akan diarahkan ke `/admin`
   - Admin Wilayah → akan diarahkan ke `/wilayah`
   - User Sekolah → akan diarahkan ke `/sekolah`

### Test Logout:
1. Klik tombol logout di panel masing-masing
2. User akan diarahkan kembali ke `/login`

### Test Unauthenticated Access:
1. Coba akses `/admin`, `/wilayah`, atau `/sekolah` tanpa login
2. User akan diarahkan ke `/login`

## Keamanan

- Rate limiting mencegah brute force attack
- Session regeneration setelah login
- CSRF protection
- Password tidak pernah di-log
- Activity logging untuk audit trail

## Catatan

- Old login pages (`/admin/login`, `/sekolah/login`, `/wilayah/login`) masih bisa diakses tetapi akan redirect ke panel jika sudah login
- Filament panel configuration tetap terpisah untuk setiap role
- Middleware authorization tetap berfungsi untuk memastikan user hanya bisa akses panel sesuai role mereka
