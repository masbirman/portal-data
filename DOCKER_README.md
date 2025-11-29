# Docker Setup untuk Portal Data AN-TKA Disdik Sulteng

## Prasyarat
- Docker Desktop sudah terinstall dan berjalan
- Port 8080 dan 3307 tidak digunakan oleh aplikasi lain

## Cara Menjalankan

### 1. Setup Awal (Hanya Sekali)

```powershell
# Copy file .env untuk Docker
Copy-Item .env.docker .env

# Build dan jalankan container
docker-compose up -d --build

# Install dependencies
docker-compose exec app composer install

# Generate application key (jika belum ada)
docker-compose exec app php artisan key:generate

# Jalankan migrasi database
docker-compose exec app php artisan migrate --seed

# Set permissions
docker-compose exec app chmod -R 775 storage bootstrap/cache
```

### 2. Akses Aplikasi

- **Web Application**: http://localhost:8080
- **Database**: localhost:3307 (dari host machine)
  - Username: root
  - Password: root
  - Database: data_anbksulteng

### 3. Perintah Berguna

```powershell
# Melihat status container
docker-compose ps

# Melihat logs
docker-compose logs -f

# Masuk ke container PHP
docker-compose exec app bash

# Menjalankan artisan commands
docker-compose exec app php artisan [command]

# Stop containers
docker-compose down

# Stop dan hapus volumes (HATI-HATI: akan menghapus data database)
docker-compose down -v

# Restart containers
docker-compose restart
```

### 4. Troubleshooting

**Error: Port sudah digunakan**
```powershell
# Ubah port di docker-compose.yml
# Ganti "8080:80" menjadi "8081:80" atau port lain
```

**Error: Permission denied**
```powershell
docker-compose exec app chmod -R 775 storage bootstrap/cache
docker-compose exec app chown -R www-data:www-data storage bootstrap/cache
```

**Clear cache**
```powershell
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan view:clear
```

## Struktur Docker

- **app**: PHP 8.3-FPM dengan semua extensions (mbstring, intl, dll)
- **webserver**: Nginx
- **db**: MySQL 8.0

## Network

Semua container berjalan di network `anbk_network` yang terisolasi dari project Docker lain.
