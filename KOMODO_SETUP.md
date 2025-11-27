# Setup Deployment dengan Komodo Panel

## Langkah-langkah Setup di Komodo

### 1. Login ke Komodo Dashboard

Akses Komodo panel Anda di browser

### 2. Buat Server Baru (jika belum ada)

-   Klik "Servers" → "Add Server"
-   Masukkan IP VPS dan SSH credentials
-   Komodo akan install dependencies otomatis

### 3. Buat Deployment Baru

#### A. Pilih Deployment Type

-   Klik "Deployments" → "Create"
-   Pilih "Git Repository"

#### B. Repository Configuration

```
Repository URL: [URL git repository Anda]
Branch: main
```

#### C. Build Configuration

**Build Command:**

```bash
composer install --no-dev --optimize-autoloader && npm install && npm run build && php artisan config:cache && php artisan route:cache && php artisan view:cache && php artisan filament:optimize
```

**Start Command:** (kosongkan, karena PHP-FPM yang handle)

#### D. Environment Variables

Klik "Environment" dan tambahkan semua variable dari file `.env.production`:

| Key           | Value                                       |
| ------------- | ------------------------------------------- |
| APP_NAME      | Portal Data AN-TKA Disdik Sulteng           |
| APP_ENV       | production                                  |
| APP_KEY       | [generate: php artisan key:generate --show] |
| APP_DEBUG     | false                                       |
| APP_URL       | https://yourdomain.com                      |
| DB_CONNECTION | mysql                                       |
| DB_HOST       | 127.0.0.1                                   |
| DB_DATABASE   | data_anbksulteng_prod                       |
| DB_USERNAME   | anbk_user                                   |
| DB_PASSWORD   | [password database]                         |
| ...           | [dan seterusnya]                            |

### 4. Setup Database

#### Via Komodo Panel:

1. Klik "Resources" → "Databases"
2. Klik "Create Database"
3. Pilih MySQL
4. Isi:
    - Database Name: `data_anbksulteng_prod`
    - Username: `anbk_user`
    - Password: [generate strong password]
5. Save dan catat credentials

#### Via SSH (alternatif):

```bash
mysql -u root -p
CREATE DATABASE data_anbksulteng_prod CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'anbk_user'@'localhost' IDENTIFIED BY 'your_password';
GRANT ALL PRIVILEGES ON data_anbksulteng_prod.* TO 'anbk_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### 5. Setup Domain & SSL

#### A. Point Domain ke VPS

Di DNS provider Anda, tambahkan A record:

```
Type: A
Name: @ (atau subdomain)
Value: [IP VPS Anda]
TTL: 3600
```

#### B. Setup di Komodo

1. Di deployment settings, klik "Domains"
2. Tambahkan domain: `yourdomain.com`
3. Enable "SSL/TLS" → pilih "Let's Encrypt"
4. Enable "Force HTTPS"
5. Enable "Auto Renew"

### 6. Setup Queue Worker

#### Via Komodo Panel:

1. Klik deployment Anda
2. Pilih tab "Procedures"
3. Klik "Add Procedure"
4. Pilih "Run Command"
5. Command:

```bash
php artisan queue:work database --sleep=3 --tries=3 --max-time=3600
```

6. Enable "Run on Startup"
7. Enable "Auto Restart"

#### Via SSH (alternatif dengan Supervisor):

```bash
# Copy config supervisor
sudo cp supervisor-laravel-worker.conf /etc/supervisor/conf.d/laravel-worker.conf

# Edit path sesuai lokasi project
sudo nano /etc/supervisor/conf.d/laravel-worker.conf

# Reload supervisor
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start laravel-worker:*
```

### 7. Setup Cron Job

#### Via Komodo Panel:

1. Klik deployment Anda
2. Pilih tab "Procedures"
3. Klik "Add Procedure"
4. Pilih "Cron Job"
5. Schedule: `* * * * *` (every minute)
6. Command:

```bash
cd /path/to/project && php artisan schedule:run
```

#### Via SSH (alternatif):

```bash
crontab -e
# Tambahkan:
* * * * * cd /var/www/html && php artisan schedule:run >> /dev/null 2>&1
```

### 8. Deploy Pertama Kali

1. Klik "Deploy" di Komodo dashboard
2. Tunggu build selesai
3. SSH ke server dan jalankan:

```bash
cd /var/www/html  # atau path project Anda
php artisan migrate --force
php artisan db:seed --force  # jika ada seeder
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### 9. Verifikasi Deployment

Cek hal-hal berikut:

```bash
# Cek aplikasi berjalan
curl https://yourdomain.com

# Cek queue worker
sudo supervisorctl status

# Cek logs
tail -f storage/logs/laravel.log

# Test database connection
php artisan tinker
>>> DB::connection()->getPdo();
```

### 10. Setup Monitoring (Optional)

#### Via Komodo:

1. Klik "Monitoring"
2. Enable monitoring untuk:
    - CPU Usage
    - Memory Usage
    - Disk Space
    - HTTP Response Time

#### Setup Alerts:

1. Klik "Alerts"
2. Tambahkan alert untuk:
    - Server down
    - High CPU usage (>80%)
    - High memory usage (>80%)
    - Disk space low (<10%)

---

## Workflow Update Aplikasi

### Cara 1: Via Komodo Dashboard (Recommended)

1. Push code ke git repository
2. Login ke Komodo
3. Klik deployment Anda
4. Klik "Deploy" atau "Redeploy"
5. Komodo akan otomatis:
    - Pull latest code
    - Run build command
    - Restart services

### Cara 2: Via SSH Manual

```bash
cd /var/www/html
git pull origin main
bash deploy.sh
```

---

## Troubleshooting Komodo

### Build Failed

-   Cek "Build Logs" di Komodo dashboard
-   Pastikan build command benar
-   Cek PHP & Node.js version

### 502 Bad Gateway

-   Cek PHP-FPM running: `sudo systemctl status php8.2-fpm`
-   Restart: `sudo systemctl restart php8.2-fpm`
-   Cek nginx config: `sudo nginx -t`

### Permission Denied

```bash
sudo chown -R www-data:www-data /var/www/html
sudo chmod -R 775 storage bootstrap/cache
```

### Database Connection Failed

-   Cek credentials di environment variables
-   Test connection: `php artisan tinker` → `DB::connection()->getPdo();`
-   Cek MySQL running: `sudo systemctl status mysql`

---

## Tips & Best Practices

1. **Backup Sebelum Deploy**

    ```bash
    # Backup database
    php artisan backup:run  # jika pakai spatie/laravel-backup
    # atau manual:
    mysqldump -u user -p database > backup.sql
    ```

2. **Gunakan Git Tags untuk Release**

    ```bash
    git tag -a v1.0.0 -m "Release version 1.0.0"
    git push origin v1.0.0
    ```

3. **Monitor Logs Secara Berkala**

    - Setup log rotation
    - Review error logs weekly
    - Setup alert untuk critical errors

4. **Update Dependencies Secara Berkala**

    ```bash
    composer update
    npm update
    ```

5. **Test di Staging Dulu**
    - Buat deployment staging di Komodo
    - Test semua fitur
    - Baru deploy ke production

---

## Kontak & Support

Jika ada masalah:

1. Cek dokumentasi Komodo: https://komo.do/docs
2. Cek Laravel logs: `storage/logs/laravel.log`
3. Cek Komodo support/community
