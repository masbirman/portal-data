# Implementasi Backup/Restore dengan Google Drive

## Overview

Fitur backup dan restore database MySQL yang terintegrasi dengan Google Drive sebagai storage, diakses melalui admin panel Filament.

## Fitur Utama

### 1. Backup Database

-   Backup manual dari admin panel (1 klik)
-   Backup otomatis terjadwal (daily/weekly)
-   Upload otomatis ke Google Drive
-   Kompresi file backup (.sql.gz)
-   Naming convention: `backup_{env}_{timestamp}.sql.gz`

### 2. Restore Database

-   List backup files dari Google Drive
-   Preview info backup (size, tanggal, environment)
-   Restore dengan konfirmasi
-   Download backup ke local sebelum restore

### 3. Manajemen Backup

-   List semua backup di Google Drive
-   Download backup ke local
-   Delete backup lama
-   Retention policy (auto-delete backup > 30 hari)

## Tech Stack

### Package yang Dibutuhkan

```bash
composer require spatie/laravel-backup
composer require google/apiclient
```

### Google Drive Setup

1. Buat project di Google Cloud Console
2. Enable Google Drive API
3. Buat Service Account
4. Download credentials JSON
5. Share folder Google Drive ke service account email

## Struktur File

```
app/
├── Filament/
│   └── Pages/
│       └── BackupManager.php          # Halaman admin untuk backup/restore
├── Services/
│   ├── BackupService.php              # Logic backup/restore database
│   └── GoogleDriveService.php         # Integrasi Google Drive API
└── Console/
    └── Commands/
        └── ScheduledBackup.php        # Command untuk backup terjadwal

config/
└── backup.php                         # Konfigurasi backup

storage/
└── app/
    └── backups/                       # Temporary storage untuk backup
```

## Database Schema

Tidak perlu migration baru - menggunakan file system dan Google Drive.

## UI Admin Panel

### Halaman Backup Manager

```
┌─────────────────────────────────────────────────────────────┐
│  Backup & Restore Database                                  │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  [🔄 Backup Sekarang]    [⚙️ Pengaturan]                    │
│                                                             │
│  ┌─────────────────────────────────────────────────────┐   │
│  │ Daftar Backup di Google Drive                       │   │
│  ├──────────────────┬──────────┬──────────┬───────────┤   │
│  │ Nama File        │ Ukuran   │ Tanggal  │ Aksi      │   │
│  ├──────────────────┼──────────┼──────────┼───────────┤   │
│  │ backup_prod_...  │ 2.5 MB   │ 01 Dec   │ [↓][🔄][🗑]│   │
│  │ backup_prod_...  │ 2.4 MB   │ 30 Nov   │ [↓][🔄][🗑]│   │
│  │ backup_prod_...  │ 2.3 MB   │ 29 Nov   │ [↓][🔄][🗑]│   │
│  └──────────────────┴──────────┴──────────┴───────────┘   │
│                                                             │
│  Aksi: [↓] Download  [🔄] Restore  [🗑] Hapus              │
│                                                             │
└─────────────────────────────────────────────────────────────┘
```

## Environment Variables

```env
# Google Drive
GOOGLE_DRIVE_FOLDER_ID=your_folder_id
GOOGLE_SERVICE_ACCOUNT_JSON=path/to/credentials.json

# Backup Settings
BACKUP_RETENTION_DAYS=30
BACKUP_SCHEDULE=daily  # daily, weekly
```

## Flow Diagram

### Backup Flow

```
User klik "Backup Sekarang"
    ↓
BackupService::create()
    ↓
mysqldump → file .sql
    ↓
gzip compress → file .sql.gz
    ↓
GoogleDriveService::upload()
    ↓
Hapus file temporary local
    ↓
Notifikasi sukses
```

### Restore Flow

```
User pilih backup dari list
    ↓
Konfirmasi restore
    ↓
GoogleDriveService::download()
    ↓
gunzip decompress
    ↓
BackupService::restore()
    ↓
mysql import
    ↓
Hapus file temporary
    ↓
Notifikasi sukses
```

## Security Considerations

-   Hanya admin yang bisa akses halaman backup
-   Konfirmasi sebelum restore (akan overwrite data)
-   Backup di-encrypt sebelum upload (opsional)
-   Service account dengan akses minimal (hanya folder tertentu)

## Timeline Estimasi

1. Setup Google Drive API & Service Account - 30 menit
2. GoogleDriveService - 1 jam
3. BackupService - 1 jam
4. Filament BackupManager Page - 1-2 jam
5. Scheduled backup command - 30 menit
6. Testing - 1 jam

**Total: ~5-6 jam**

## Pengaturan Admin (Configurable)

Semua fitur berikut tersedia dan bisa di-toggle on/off oleh admin melalui Settings page:

| Setting                 | Default | Keterangan                                  |
| ----------------------- | ------- | ------------------------------------------- |
| Backup Terjadwal        | OFF     | Aktifkan untuk backup otomatis daily/weekly |
| Jadwal Backup           | Daily   | Pilihan: Daily, Weekly                      |
| Waktu Backup            | 02:00   | Jam backup otomatis dijalankan              |
| Enkripsi Backup         | OFF     | Encrypt file backup sebelum upload          |
| Auto-Delete Backup Lama | ON      | Hapus backup lebih dari X hari              |
| Retention Period        | 30 hari | Berapa lama backup disimpan                 |
| Notifikasi Telegram     | ON      | Kirim notif saat backup sukses/gagal        |

## Settings Storage

Menggunakan `spatie/laravel-settings` untuk menyimpan konfigurasi di database:

```php
// app/Settings/BackupSettings.php
class BackupSettings extends Settings
{
    public bool $scheduled_backup_enabled = false;
    public string $backup_schedule = 'daily'; // daily, weekly
    public string $backup_time = '02:00';
    public bool $encryption_enabled = false;
    public string $encryption_password = '';
    public bool $auto_delete_enabled = true;
    public int $retention_days = 30;
    public bool $telegram_notification_enabled = true;

    public static function group(): string
    {
        return 'backup';
    }
}
```

## UI Settings Page

```
┌─────────────────────────────────────────────────────────────┐
│  Pengaturan Backup                                          │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  Backup Otomatis                                            │
│  ┌─────────────────────────────────────────────────────┐   │
│  │ [Toggle] Aktifkan Backup Terjadwal                  │   │
│  │ Jadwal: [Daily ▼]  Waktu: [02:00]                   │   │
│  └─────────────────────────────────────────────────────┘   │
│                                                             │
│  Keamanan                                                   │
│  ┌─────────────────────────────────────────────────────┐   │
│  │ [Toggle] Enkripsi File Backup                       │   │
│  │ Password: [••••••••••]                              │   │
│  └─────────────────────────────────────────────────────┘   │
│                                                             │
│  Retention                                                  │
│  ┌─────────────────────────────────────────────────────┐   │
│  │ [Toggle] Auto-Delete Backup Lama                    │   │
│  │ Simpan backup selama: [30] hari                     │   │
│  └─────────────────────────────────────────────────────┘   │
│                                                             │
│  Notifikasi                                                 │
│  ┌─────────────────────────────────────────────────────┐   │
│  │ [Toggle] Kirim Notifikasi Telegram                  │   │
│  └─────────────────────────────────────────────────────┘   │
│                                                             │
│  [Simpan Pengaturan]                                        │
│                                                             │
└─────────────────────────────────────────────────────────────┘
```

## Updated Struktur File

```
app/
├── Filament/
│   └── Pages/
│       ├── BackupManager.php          # Halaman list & aksi backup
│       └── BackupSettings.php         # Halaman pengaturan backup
├── Services/
│   ├── BackupService.php              # Logic backup/restore database
│   └── GoogleDriveService.php         # Integrasi Google Drive API
├── Settings/
│   └── BackupSettings.php             # Settings model (spatie/laravel-settings)
└── Console/
    └── Commands/
        └── ScheduledBackup.php        # Command untuk backup terjadwal

database/
└── settings/
    └── 2025_12_01_create_backup_settings.php  # Migration untuk settings
```
