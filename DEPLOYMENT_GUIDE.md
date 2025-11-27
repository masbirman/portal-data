# Panduan Deployment ke VPS dengan Komodo

## Ringkasan Project

-   **Aplikasi**: Portal Data AN-TKA Disdik Sulteng
-   **Framework**: Laravel 12 + Filament 4
-   **PHP**: 8.2+
-   **Database**: MySQL
-   **Web Server**: Nginx (via Komodo)

---

## Persiapan VPS

### 1. Requirement Server

-   PHP 8.2 atau lebih tinggi
-   MySQL 8.0+
-   Nginx
-   Composer
-   Node.js 18+ & NPM
-   Git
-   Supervisor (untuk queue worker)

### 2. PHP Extensions yang Diperlukan

```bash
php8.2-cli
php8.2-fpm
php8.2-mysql
php8.2-mbstring
php8.2-xml
php8.2-curl
php8.2-zip
php8.2-gd
php8.2-bcmath
php8.2-intl
php8.2-redis (optional)
```

---

## Langkah Deployment

### STEP 1: Setup Database di Komodo

1. Login ke Komodo Panel
2. Buat database baru:
    - Database Name: `data_anbksulteng_prod`
    - Database User: `anbk_user`
    - Password: [generate strong password]
3. Catat credentials untuk konfigurasi `.env`

### STEP 2: Setup Project di Komodo

1. **Buat Deployment baru di Komodo:**

    - Pilih "Create Deployment"
    - Pilih tipe: Git Repository
    - Masukkan repository URL
    - Branch: `main` atau `production`
    - Build Command: (lihat di bawah)

2. **Build Command untuk Komodo:**

```bash
composer install --no-dev --optimize-autoloader
npm install
npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan filament:optimize
```

3. **Environment Variables di Komodo:**
   Tambahkan environment variables berikut di Komodo panel:

```env
APP_NAME="Portal Data AN-TKA Disdik Sulteng"
APP_ENV=production
APP_KEY=[generate dengan: php artisan key:generate --show]
APP_DEBUG=false
APP_URL=https://yourdomain.com

APP_LOCALE=id
APP_FALLBACK_LOCALE=id

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=data_anbksulteng_prod
DB_USERNAME=anbk_user
DB_PASSWORD=[password dari step 1]

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_SECURE_COOKIE=true

CACHE_STORE=database
QUEUE_CONNECTION=database

FILESYSTEM_DISK=local

MAIL_MAILER=smtp
MAIL_HOST=[smtp host]
MAIL_PORT=587
MAIL_USERNAME=[email username]
MAIL_PASSWORD=[email password]
MAIL_FROM_ADDRESS="noreply@yourdomain.com"
MAIL_FROM_NAME="${APP_NAME}"
```

### STEP 3: Konfigurasi Web Server (Nginx)

Komodo biasanya auto-generate nginx config, tapi pastikan ada:

```nginx
server {
    listen 80;
    server_name yourdomain.com;
    root /path/to/your/project/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### STEP 4: Setup SSL dengan Let's Encrypt

Di Komodo panel:

1. Pilih deployment Anda
2. Klik "SSL/TLS"
3. Pilih "Let's Encrypt"
4. Enable auto-renewal

### STEP 5: Setup Queue Worker dengan Supervisor

Buat file konfigurasi supervisor (biasanya via Komodo atau manual):

**File: `/etc/supervisor/conf.d/laravel-worker.conf`**

```ini
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/your/project/artisan queue:work database --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/path/to/your/project/storage/logs/worker.log
stopwaitsecs=3600
```

Jalankan:

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start laravel-worker:*
```

### STEP 6: Setup Cron Job untuk Laravel Scheduler

Tambahkan di crontab (via Komodo atau manual):

```bash
* * * * * cd /path/to/your/project && php artisan schedule:run >> /dev/null 2>&1
```

### STEP 7: Set Permissions

```bash
cd /path/to/your/project
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

### STEP 8: Run Migrations

Setelah deployment pertama kali:

```bash
php artisan migrate --force
php artisan db:seed --force  # jika ada seeder
```

---

## Post-Deployment Checklist

-   [ ] Database terkoneksi dengan baik
-   [ ] SSL certificate aktif (HTTPS)
-   [ ] Queue worker berjalan (cek: `sudo supervisorctl status`)
-   [ ] Cron job terdaftar
-   [ ] File permissions sudah benar
-   [ ] Cache sudah di-clear dan di-optimize
-   [ ] Test upload file (storage writable)
-   [ ] Test export Excel & PDF
-   [ ] Test login Filament admin panel
-   [ ] Monitoring logs: `tail -f storage/logs/laravel.log`

---

## Maintenance Commands

### Update Aplikasi

```bash
git pull origin main
composer install --no-dev --optimize-autoloader
npm install && npm run build
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan filament:optimize
sudo supervisorctl restart laravel-worker:*
```

### Clear Cache

```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Backup Database

```bash
mysqldump -u anbk_user -p data_anbksulteng_prod > backup_$(date +%Y%m%d).sql
```

---

## Troubleshooting

### Error 500

-   Cek `storage/logs/laravel.log`
-   Pastikan `APP_DEBUG=false` di production
-   Cek permissions storage & bootstrap/cache

### Queue tidak jalan

-   Cek supervisor: `sudo supervisorctl status`
-   Restart worker: `sudo supervisorctl restart laravel-worker:*`
-   Cek logs: `storage/logs/worker.log`

### Asset tidak load

-   Pastikan `npm run build` sudah dijalankan
-   Cek `APP_URL` di `.env` sesuai domain
-   Clear browser cache

---

## Monitoring & Logs

### Log Files

-   Laravel: `storage/logs/laravel.log`
-   Nginx: `/var/log/nginx/access.log` & `error.log`
-   PHP-FPM: `/var/log/php8.2-fpm.log`
-   Queue Worker: `storage/logs/worker.log`

### Monitoring Commands

```bash
# Cek queue jobs
php artisan queue:monitor

# Cek failed jobs
php artisan queue:failed

# Retry failed jobs
php artisan queue:retry all
```

---

## Security Checklist

-   [ ] `APP_DEBUG=false` di production
-   [ ] `APP_ENV=production`
-   [ ] Strong `APP_KEY` generated
-   [ ] Database credentials aman
-   [ ] `.env` tidak ter-commit ke git
-   [ ] Firewall aktif (hanya port 80, 443, 22)
-   [ ] SSH key-based authentication
-   [ ] Regular backup database & files
-   [ ] Update dependencies secara berkala
-   [ ] Monitoring logs untuk suspicious activity
