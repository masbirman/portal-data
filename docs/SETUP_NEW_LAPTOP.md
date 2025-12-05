# Prosedur Setup Project di Laptop Baru

## Prasyarat
Pastikan sudah terinstall:
- Git
- Docker Desktop (untuk menjalankan container)
- Kiro Editor (opsional)

## Langkah-langkah

### 1. Clone Repository
```bash
git clone <url-repository-kamu>
cd app
```

### 2. Copy Environment File
```bash
cp .env.example .env
```

Edit `.env` jika perlu (biasanya default sudah sesuai untuk Docker).

### 3. Jalankan Docker Containers
```bash
docker compose up -d
```

Tunggu sampai semua container running:
- `laravel-app` - PHP/Laravel
- `laravel-mysql` - MySQL Database
- `laravel-nginx` - Web Server
- `laravel-queue` - Queue Worker

### 4. Import Database
Gunakan backup terbaru `backup_for_cloning_v5.3.0_20251205.sql`:

```bash
# Copy file backup ke container
docker cp backup_for_cloning_v5.3.0_20251205.sql laravel-mysql:/tmp/backup.sql

# Import ke database
docker exec laravel-mysql mysql -u root -proot data_anbksulteng -e "SOURCE /tmp/backup.sql;"
```

### 5. Install Dependencies (jika belum ada di image)
```bash
docker exec laravel-app composer install
docker exec laravel-app npm install
```

### 6. Generate App Key (jika belum ada)
```bash
docker exec laravel-app php artisan key:generate
```

### 7. Clear Cache
```bash
docker exec laravel-app php artisan config:clear
docker exec laravel-app php artisan cache:clear
docker exec laravel-app php artisan view:clear
```

### 8. Storage Link
```bash
docker exec laravel-app php artisan storage:link
```

### 9. Akses Aplikasi
Buka browser: `http://localhost`

---

## File Backup Database
- `backup_for_cloning_v5.3.0_20251205.sql` - Backup lengkap dengan:
  - Data sekolah (4367 records)
  - Data wilayah dengan koordinat (13 kabupaten/kota)
  - Kolom baru: latitude, longitude, alamat, foto di tabel sekolah
  - Settings dan konfigurasi

## Yang Otomatis Ikut Ter-clone
- ✅ Semua source code
- ✅ Folder `.kiro/specs` (specs Kiro)
- ✅ File konfigurasi (compose.yaml, Dockerfile, dll)
- ✅ Dokumentasi (folder docs)
- ✅ File backup database

## Yang Perlu Setup Manual
- ❌ Docker containers → `docker compose up -d`
- ❌ Database → import dari backup SQL
- ❌ `.env` → copy dari `.env.example`

## Troubleshooting

### Container tidak jalan
```bash
docker compose down
docker compose up -d --build
```

### Database connection error
Pastikan container MySQL sudah running:
```bash
docker ps
docker logs laravel-mysql
```

### Permission error pada storage
```bash
docker exec laravel-app chmod -R 775 storage bootstrap/cache
docker exec laravel-app chown -R www-data:www-data storage bootstrap/cache
```
