# Environment Information

## 🖥️ Development Environment

**Environment Type**: FlyEnv  
**Custom Host**: `data_anbksulteng.test`  
**OS**: Windows 10 (Build 26200)  
**Shell**: PowerShell 7

---

## 🌐 Access URLs

### Public Dashboard

-   **Landing Page**: http://data_anbksulteng.test/
-   **Dashboard**: http://data_anbksulteng.test/dashboard

### Admin Panel

-   **Admin Login**: http://data_anbksulteng.test/admin
-   **Admin Dashboard**: http://data_anbksulteng.test/admin

### Legacy Routes (Backward Compatibility)

-   **Old Home**: http://data_anbksulteng.test/old
-   **Asesmen Recap**: http://data_anbksulteng.test/asesmen/{tahun}
-   **Asesmen Detail**: http://data_anbksulteng.test/asesmen/{tahun}/wilayah/{wilayah_id}

---

## 🔧 FlyEnv Configuration

### What is FlyEnv?

FlyEnv adalah local development environment yang digunakan untuk menjalankan aplikasi Laravel dengan custom host domain.

### Host Configuration

```
Host: data_anbksulteng.test
Type: Custom domain (local)
```

### How to Access

1. Pastikan FlyEnv sudah running
2. Aplikasi otomatis tersedia di http://data_anbksulteng.test/
3. Tidak perlu `php artisan serve` karena menggunakan FlyEnv

---

## 📝 Important Notes

### Untuk Testing

-   ✅ Gunakan `http://data_anbksulteng.test/` bukan `http://127.0.0.1:8000/`
-   ✅ FlyEnv harus dalam kondisi running
-   ✅ Host `data_anbksulteng.test` sudah ter-configure di FlyEnv

### Untuk Development

-   Assets (Vite) tetap bisa dijalankan dengan `npm run dev`
-   Hot reload akan bekerja normal
-   Database connection menggunakan konfigurasi di `.env`

### Untuk Production

-   FlyEnv hanya untuk development
-   Untuk production, gunakan server dan domain sebenarnya
-   Update `.env` sesuai environment production

---

## 🚀 Quick Reference

### Start Development

```bash
# FlyEnv sudah running secara default
# Jika perlu rebuild assets:
npm run dev
```

### Access Application

```bash
# Buka browser ke:
http://data_anbksulteng.test/
```

### Stop Development

```bash
# Untuk stop FlyEnv, ikuti dokumentasi FlyEnv
# Biasanya: stop FlyEnv service
```

---

## 🔍 Troubleshooting

### Issue: Cannot access data_anbksulteng.test

**Possible Causes**:

1. FlyEnv tidak running
2. Host belum ter-configure
3. Browser cache

**Solution**:

1. Pastikan FlyEnv running
2. Check FlyEnv configuration untuk host `data_anbksulteng.test`
3. Clear browser cache atau coba private/incognito mode

### Issue: Page loads but shows 404

**Solution**:

```bash
# Clear Laravel cache
php artisan route:clear
php artisan config:clear
php artisan view:clear
```

### Issue: Assets not loading

**Solution**:

```bash
# Rebuild assets
npm run build
# or for development
npm run dev
```

---

## 📊 Environment Variables

Key environment variables untuk aplikasi:

```env
APP_NAME="ANBK Data Management"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://data_anbksulteng.test

# Database sesuai konfigurasi FlyEnv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=anbk_db
DB_USERNAME=root
DB_PASSWORD=
```

---

## 🎯 Development Workflow

1. **Start Development**

    - FlyEnv running
    - Visit http://data_anbksulteng.test/

2. **Make Changes**

    - Edit PHP files (auto-reload dengan FlyEnv)
    - Edit views (auto-reload)
    - Edit CSS/JS → need `npm run dev`

3. **Testing**

    - Test di http://data_anbksulteng.test/
    - Check browser console for errors
    - Check Laravel logs: `storage/logs/laravel.log`

4. **Commit Changes**
    - Test semua features
    - Commit to Git
    - Push to repository

---

## 💡 Tips

1. **Bookmark URLs**

    ```
    Public: http://data_anbksulteng.test/
    Admin: http://data_anbksulteng.test/admin
    Dashboard: http://data_anbksulteng.test/dashboard
    ```

2. **Quick Access**

    - Buat shortcut di browser bookmark bar
    - Gunakan browser extension untuk quick switch

3. **Multiple Tabs**
    - Tab 1: Public Dashboard
    - Tab 2: Admin Panel
    - Tab 3: Database Tool (phpMyAdmin/Adminer)
    - Tab 4: Terminal/VSCode

---

**Last Updated**: 2025-11-21  
**Environment**: FlyEnv  
**Host**: data_anbksulteng.test
