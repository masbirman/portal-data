# Panduan Deployment ke Dokploy

## File yang Dibuat

| File                             | Fungsi                                                      |
| -------------------------------- | ----------------------------------------------------------- |
| `Dockerfile.dokploy`             | Dockerfile all-in-one (nginx + php-fpm + queue + scheduler) |
| `compose.dokploy.yml`            | Docker Compose untuk deployment dengan MySQL                |
| `docker/nginx/dokploy.conf`      | Konfigurasi Nginx optimized                                 |
| `docker/supervisor/dokploy.conf` | Supervisor untuk manage semua proses                        |
| `docker/entrypoint.sh`           | Script inisialisasi (migration, cache, dll)                 |
| `.env.production`                | Template environment production                             |

## Opsi Deployment di Dokploy

### Opsi 1: Single Container (Recommended)

Gunakan jika database MySQL sudah ada (external database):

1. Di Dokploy, buat project baru → pilih **Application**
2. Source: Git repository
3. Build Type: **Dockerfile**
4. Dockerfile Path: `Dockerfile.dokploy`
5. Set environment variables:
    ```
    APP_KEY=base64:xxxxx (generate dengan: php artisan key:generate --show)
    APP_URL=https://yourdomain.com
    DB_HOST=your-mysql-host
    DB_DATABASE=portal_data
    DB_USERNAME=root
    DB_PASSWORD=your_password
    TURNSTILE_SITE_KEY=xxx
    TURNSTILE_SECRET_KEY=xxx
    ```

### Opsi 2: Docker Compose (App + MySQL)

Gunakan jika ingin MySQL dalam satu stack:

1. Di Dokploy, buat project baru → pilih **Compose**
2. Source: Git repository
3. Compose File: `compose.dokploy.yml`
4. Set environment variables sama seperti Opsi 1

## Environment Variables Wajib

```env
APP_KEY=base64:xxxxx          # Generate: php artisan key:generate --show
APP_URL=https://yourdomain.com
DB_HOST=mysql                  # atau hostname database external
DB_DATABASE=portal_data
DB_PASSWORD=secure_password
```

## Environment Variables Optional

```env
TURNSTILE_SITE_KEY=xxx        # Cloudflare Turnstile
TURNSTILE_SECRET_KEY=xxx
TELEGRAM_BOT_TOKEN=xxx        # Notifikasi Telegram
TELEGRAM_CHAT_ID=xxx
```

## Generate APP_KEY

Jalankan di local:

```bash
php artisan key:generate --show
```

Atau generate online: https://generate-random.org/laravel-key-generator

## Port & Domain

-   Container expose port **80**
-   Dokploy akan handle SSL/HTTPS via Traefik
-   Set domain di Dokploy dashboard

## Health Check

Endpoint `/health` tersedia untuk monitoring.

## Persistent Storage

Volume `app-storage` menyimpan:

-   Upload files
-   Cache files
-   Log files

## Troubleshooting

### Cek logs

```bash
docker logs <container_id>
```

### Masuk ke container

```bash
docker exec -it <container_id> bash
```

### Clear cache manual

```bash
docker exec -it <container_id> php artisan cache:clear
docker exec -it <container_id> php artisan config:clear
```

### Re-run migrations

```bash
docker exec -it <container_id> php artisan migrate --force
```
