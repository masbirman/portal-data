# Panduan Setup Komodo - Sesuai Interface Asli

## Informasi Project

**Komodo Panel:** `http://103.150.227.7:9120`  
**VPS IP:** `103.150.227.7`  
**Repository:** `https://github.com/masbirman/portal-data.git`  
**Branch:** `dev`

---

## LANGKAH 1: Enable Server yang Sudah Ada

Saya lihat Anda sudah punya server `data-antka-sulteng` tapi statusnya **DISABLED**.

### A. Enable Server

1. Klik server `data-antka-sulteng` di sidebar
2. Di bagian atas, klik tombol **Edit** (icon pensil)
3. Scroll ke bagian **Enabled**
4. Toggle switch **Enabled** menjadi ON
5. Klik **Save** atau **Update**

### B. Verifikasi Server Status

-   Status harus berubah dari DISABLED menjadi **ENABLED** atau **ONLINE**
-   Cek di tab **Stats** untuk melihat CPU, Memory, Disk usage
-   Pastikan **Address** sudah terisi: `103.150.227.7`

---

## LANGKAH 2: Buat Stack untuk Laravel

Komodo menggunakan **Stack** (Docker Compose) untuk deploy aplikasi.

### A. Klik "Stacks" di Sidebar Kiri

### B. Klik "Create Stack" atau tombol "+"

### C. Isi Form Stack:

**Stack Name:**

```
portal-anbk-production
```

**Server:**

-   Pilih: `data-antka-sulteng`

**Git Repository:**

```
https://github.com/masbirman/portal-data.git
```

**Branch:**

```
dev
```

**Git Account:** (kosongkan jika repository public)

---

## LANGKAH 3: Konfigurasi Stack

### A. Pilih Stack Type

Ada beberapa pilihan:

1. **Compose** - Jika sudah punya docker-compose.yml (kita belum punya)
2. **Custom** - Buat manual

**Pilih: Custom** atau **Compose** (kita akan buat docker-compose.yml)

### B. Buat Docker Compose Configuration

Klik tab **Config** atau **Compose File**, lalu isi:

```yaml
version: "3.8"

services:
    app:
        image: php:8.2-fpm
        container_name: laravel-app
        working_dir: /var/www/html
        volumes:
            - ./:/var/www/html
        environment:
            - APP_NAME=Portal Data AN-TKA Disdik Sulteng
            - APP_ENV=production
            - APP_DEBUG=false
            - APP_URL=http://103.150.227.7
            - DB_CONNECTION=mysql
            - DB_HOST=mysql
            - DB_PORT=3306
            - DB_DATABASE=data_anbksulteng_prod
            - DB_USERNAME=anbk_user
            - DB_PASSWORD=your_secure_password
        depends_on:
            - mysql
        networks:
            - laravel-network

    nginx:
        image: nginx:alpine
        container_name: laravel-nginx
        ports:
            - "80:80"
            - "443:443"
        volumes:
            - ./:/var/www/html
            - ./nginx-site.conf:/etc/nginx/conf.d/default.conf
        depends_on:
            - app
        networks:
            - laravel-network

    mysql:
        image: mysql:8.0
        container_name: laravel-mysql
        environment:
            MYSQL_ROOT_PASSWORD: root_password
            MYSQL_DATABASE: data_anbksulteng_prod
            MYSQL_USER: anbk_user
            MYSQL_PASSWORD: your_secure_password
        volumes:
            - mysql-data:/var/lib/mysql
        ports:
            - "3306:3306"
        networks:
            - laravel-network

networks:
    laravel-network:
        driver: bridge

volumes:
    mysql-data:
        driver: local
```

### C. Klik "Save" atau "Create"

---

## LANGKAH 4: Setup Build & Deploy

### A. Klik Tab "Builds" di Sidebar

### B. Klik "Create Build" atau "New Build"

### C. Isi Build Configuration:

**Build Name:**

```
portal-anbk-build
```

**Stack:**

-   Pilih: `portal-anbk-production`

**Build Command:**

```bash
composer install --no-dev --optimize-autoloader
npm install
npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan filament:optimize
```

**Run On:**

-   ☑️ Git Push (auto build saat push ke GitHub)
-   ☑️ Manual

### D. Klik "Save" atau "Create"

---

## LANGKAH 5: Deploy Stack

### A. Kembali ke Stack `portal-anbk-production`

### B. Klik Tombol "Deploy" atau "Start Stack"

Komodo akan:

1. Clone repository dari GitHub
2. Pull Docker images (PHP, Nginx, MySQL)
3. Build containers
4. Start services

**Tunggu proses selesai** (5-15 menit untuk pertama kali)

### C. Monitor Logs

-   Klik tab **Logs** untuk melihat progress
-   Pastikan tidak ada error

---

## LANGKAH 6: Setup Database & Migration

### A. Akses Container Terminal

1. Di Stack page, klik tab **Docker** atau **Containers**
2. Cari container `laravel-app`
3. Klik icon **Terminal** atau **Console**

### B. Jalankan Commands:

```bash
# Generate APP_KEY
php artisan key:generate --force

# Run migrations
php artisan migrate --force

# Run seeders (jika ada)
php artisan db:seed --force

# Set permissions
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# Clear cache
php artisan optimize:clear
php artisan optimize
```

---

## LANGKAH 7: Setup Queue Worker (Procedures)

### A. Klik "Procedures" di Sidebar

### B. Klik "Create Procedure"

### C. Isi Form:

**Procedure Name:**

```
laravel-queue-worker
```

**Type:**

-   Pilih: **Run Command**

**Stack:**

-   Pilih: `portal-anbk-production`

**Container:**

-   Pilih: `laravel-app`

**Command:**

```bash
php artisan queue:work database --sleep=3 --tries=3 --max-time=3600
```

**Options:**

-   ☑️ Run on Startup
-   ☑️ Auto Restart on Failure

### D. Klik "Save" atau "Create"

### E. Klik "Run" untuk start queue worker

---

## LANGKAH 8: Setup Cron Job (Scheduler)

### A. Masih di "Procedures", Klik "Create Procedure"

### B. Isi Form:

**Procedure Name:**

```
laravel-scheduler
```

**Type:**

-   Pilih: **Cron Job**

**Schedule:**

```
* * * * *
```

(setiap menit)

**Stack:**

-   Pilih: `portal-anbk-production`

**Container:**

-   Pilih: `laravel-app`

**Command:**

```bash
php artisan schedule:run
```

### C. Klik "Save" atau "Create"

---

## LANGKAH 9: Test Aplikasi

### A. Akses via Browser

Buka browser dan akses:

```
http://103.150.227.7
```

Anda harus melihat aplikasi Laravel berjalan.

### B. Test Filament Admin

Akses:

```
http://103.150.227.7/admin
```

Login dengan credentials admin yang sudah di-seed.

---

## LANGKAH 10: Setup Domain & SSL (Optional)

### A. Point Domain ke VPS

Di DNS provider (Cloudflare, Namecheap, dll):

```
Type: A
Name: @ atau subdomain
Value: 103.150.227.7
```

### B. Update Stack Environment

1. Klik Stack `portal-anbk-production`
2. Klik tab **Config**
3. Update environment variable:
    ```
    APP_URL=https://yourdomain.com
    ```
4. Klik **Save**

### C. Setup SSL di Komodo

1. Klik tab **Resources** di sidebar
2. Pilih **SSL/TLS** atau **Certificates**
3. Klik **Create Certificate**
4. Pilih **Let's Encrypt**
5. Isi domain: `yourdomain.com`
6. Klik **Generate**

### D. Update Nginx Config

Edit `nginx-site.conf` untuk support HTTPS, lalu redeploy stack.

---

## Troubleshooting

### Stack Tidak Start

1. Cek **Logs** di tab Logs
2. Pastikan Docker images berhasil di-pull
3. Cek port 80 tidak bentrok dengan service lain

### Database Connection Error

1. Akses terminal container `laravel-app`
2. Test koneksi:
    ```bash
    php artisan tinker
    DB::connection()->getPdo();
    ```
3. Pastikan credentials di environment benar

### Permission Denied

```bash
# Di terminal container
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### Queue Worker Tidak Jalan

1. Klik **Procedures** di sidebar
2. Cari `laravel-queue-worker`
3. Cek status, jika stopped klik **Run**
4. Cek logs di tab **Logs**

---

## Update Aplikasi

### Via Komodo (Recommended)

1. Push code ke GitHub
2. Di Komodo, klik Stack `portal-anbk-production`
3. Klik **Redeploy** atau **Update**
4. Komodo akan:
    - Pull latest code
    - Rebuild containers
    - Restart services

### Via Terminal Manual

1. Akses terminal container `laravel-app`
2. Jalankan:
    ```bash
    git pull origin dev
    composer install --no-dev --optimize-autoloader
    npm install && npm run build
    php artisan migrate --force
    php artisan optimize:clear
    php artisan optimize
    ```

---

## Monitoring

### Via Komodo Dashboard

1. Klik **Dashboard** di sidebar
2. Lihat overview semua services
3. Monitor CPU, Memory, Disk usage

### Via Stack Page

1. Klik Stack `portal-anbk-production`
2. Tab **Stats** - lihat resource usage
3. Tab **Logs** - lihat application logs
4. Tab **Docker** - lihat container status

---

## Tips

1. **Backup Database Berkala**

    - Setup procedure untuk backup otomatis
    - Export database via phpMyAdmin atau command line

2. **Monitor Logs**

    - Cek logs Laravel: `storage/logs/laravel.log`
    - Cek logs Nginx di Komodo Logs tab

3. **Update Dependencies**

    - Update composer & npm packages secara berkala
    - Test di local/staging dulu

4. **Security**
    - Ganti password database default
    - Set `APP_DEBUG=false` di production
    - Enable firewall di VPS

---

## Catatan Penting

-   Komodo menggunakan **Docker** untuk semua deployment
-   Setiap Stack = 1 aplikasi dengan multiple containers
-   Procedures = Background jobs/cron
-   Builds = CI/CD pipeline

Jika ada yang tidak jelas atau stuck, screenshot dan tanyakan!
