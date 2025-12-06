# Deployment Guide - VPS Docker

## Quick Start

```bash
# 1. Clone repository
git clone <repo-url> portal-data
cd portal-data

# 2. Copy dan edit environment
cp .env.example .env
# Edit .env sesuai kebutuhan (DB_PASSWORD, APP_KEY, APP_URL, dll)

# 3. Generate APP_KEY (jalankan di local dulu atau generate manual)
# base64:xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx

# 4. Build dan jalankan
docker compose up -d --build

# 5. Jalankan migration (pertama kali)
docker compose exec app php artisan migrate --force
docker compose exec app php artisan db:seed --force  # jika perlu

# 6. Optimize
docker compose exec app php artisan config:cache
docker compose exec app php artisan route:cache
docker compose exec app php artisan view:cache
```

## Update Deployment

```bash
git pull
docker compose up -d --build
docker compose exec app php artisan migrate --force
docker compose exec app php artisan config:cache
docker compose exec app php artisan route:cache
docker compose exec app php artisan view:cache
docker compose restart queue
```

## Useful Commands

```bash
# Lihat logs
docker compose logs -f app
docker compose logs -f nginx

# Masuk ke container
docker compose exec app bash

# Clear cache
docker compose exec app php artisan cache:clear

# Restart queue worker
docker compose restart queue

# Backup database
docker compose exec mysql mysqldump -u root -p portal_data > backup.sql
```

## SSL dengan Reverse Proxy

Untuk HTTPS, gunakan reverse proxy seperti Nginx atau Caddy di depan container ini.

Contoh Caddy (di host):

```
yourdomain.com {
    reverse_proxy localhost:80
}
```
