# Sistem Request Download Data

## Overview
Sistem ini memungkinkan pengguna publik untuk mengajukan permintaan download data pendidikan dengan proses approval dari admin.

## Fitur Utama

### 1. Form Request Publik
- URL: `/request-download`
- Pengguna mengisi formulir dengan informasi:
  - Nama lengkap
  - Email
  - Instansi/Organisasi
  - Tujuan penggunaan data
  - Jenis data (Asesmen Nasional/Survei Lingkungan Belajar/Tes Kemampuan Akademik)
  - Tahun (2020 - sekarang)
  - Wilayah
  - Jenjang pendidikan

### 2. Admin Panel
- Resource: `Request Download` di admin panel
- Fitur:
  - Melihat semua request
  - Filter berdasarkan status (pending/approved/rejected)
  - Approve request dengan generate download token
  - Reject request dengan alasan penolakan
  - Tracking download (kapan data diunduh)

### 3. Email Notification
- **Approved**: Email berisi link download dengan token unik
- **Rejected**: Email berisi alasan penolakan
- Email dikirim secara otomatis via queue

### 4. Download System
- Link download berlaku 7 hari
- Token unik untuk setiap request
- Data di-export ke Excel berdasarkan filter yang diminta
- Tracking kapan data diunduh

## Database Schema

### Table: download_requests
```sql
- id
- nama (string)
- email (string)
- instansi (string)
- tujuan_penggunaan (text)
- data_type (enum: asesmen_nasional, survei_lingkungan_belajar, tes_kemampuan_akademik)
- tahun (year)
- wilayah_id (foreign key)
- jenjang_pendidikan_id (foreign key to jenjang_pendidikan: SD, SMP, SMA, SMK, SDLB, SMPLB, SMALB, PAKET A, PAKET B, PAKET C)
- status (enum: pending, approved, rejected)
- admin_notes (text, nullable)
- approved_by (foreign key to users, nullable)
- approved_at (datetime, nullable)
- download_token (string, nullable)
- token_expires_at (datetime, nullable)
- downloaded_at (datetime, nullable)
- timestamps
```

## Routes

### Public Routes
```php
GET  /request-download              - Form request
POST /request-download              - Submit request
GET  /request-download/success      - Success page
GET  /download/{token}              - Download data
```

### Admin Routes
```php
GET  /admin/download-requests                    - List requests
GET  /admin/download-requests/create             - Create request
GET  /admin/download-requests/{record}/edit      - Edit request
```

## Models & Classes

### Models
- `App\Models\DownloadRequest`

### Controllers
- `App\Http\Controllers\DownloadRequestController`

### Filament Resources
- `App\Filament\Resources\DownloadRequests\DownloadRequestResource`
- `App\Filament\Resources\DownloadRequests\Tables\DownloadRequestsTable`
- `App\Filament\Resources\DownloadRequests\Schemas\DownloadRequestForm`

### Mail Classes
- `App\Mail\DownloadRequestApproved`
- `App\Mail\DownloadRequestRejected`

### Export Classes
- `App\Exports\DataRequestExport`

## Views

### Public Views
- `resources/views/download-request/index.blade.php` - Form request
- `resources/views/download-request/success.blade.php` - Success page

### Email Templates
- `resources/views/emails/download-request-approved.blade.php`
- `resources/views/emails/download-request-rejected.blade.php`

## Workflow

1. **User Request**
   - User mengisi form di `/request-download`
   - Data tersimpan dengan status `pending`
   - User diarahkan ke halaman success

2. **Admin Review**
   - Admin melihat request di admin panel
   - Admin dapat approve atau reject
   - Jika approve: generate token dan kirim email
   - Jika reject: kirim email dengan alasan

3. **Download**
   - User klik link di email
   - System validasi token (valid & belum expired)
   - Generate Excel file sesuai request
   - Mark request sebagai downloaded
   - File otomatis terdownload

## Security Features

- Token unik untuk setiap request
- Token expired setelah 7 hari
- Validasi token sebelum download
- Tracking siapa yang approve/reject
- Email notification untuk transparency

## Future Improvements

- [ ] Implementasi export untuk Survei Lingkungan Belajar dan TKA
- [ ] Rate limiting untuk prevent spam request
- [ ] Dashboard statistik request
- [ ] Bulk approve/reject
- [ ] Custom expiry time per request
- [ ] Download history untuk user
- [ ] Captcha untuk form request
