# Troubleshooting Guide - Public Dashboard

## 🐛 Common Issues & Solutions

### Issue 1: Vite Manifest Not Found ✅ FIXED

**Error Message**:

```
Illuminate\Foundation\ViteManifestNotFoundException
Vite manifest not found at: D:\BIRMAN-DEV\app\public\build\manifest.json
```

**Root Cause**:

-   Aplikasi menggunakan `@vite()` directive tapi assets belum di-build
-   NPM tidak tersedia atau belum menjalankan build

**Solution** ✅:
Layout sudah diupdate untuk menggunakan **Tailwind CSS via CDN** sehingga tidak memerlukan build process.

**What Was Changed**:

```php
// BEFORE (memerlukan npm run build)
@vite(['resources/css/app.css', 'resources/js/app.js'])

// AFTER (langsung pakai CDN)
<script src="https://cdn.tailwindcss.com"></script>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
```

**Next Steps**:

1. Clear cache: `php artisan view:clear`
2. Refresh browser: http://data_anbksulteng.test/
3. Page should load now! ✅

---

### Issue 2: Page Still Shows Error After Fix

**Solution**:

```bash
# Clear all caches
php artisan view:clear
php artisan config:clear
php artisan route:clear

# Restart FlyEnv if needed
# Then refresh browser (Ctrl+Shift+R for hard refresh)
```

---

### Issue 3: Charts Not Showing

**Symptoms**:

-   Page loads but charts area is blank
-   Console shows ApexCharts errors

**Solution**:

1. Check browser console (F12)
2. Ensure ApexCharts CDN is loading:
    ```html
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    ```
3. Check if data exists in database
4. Verify JavaScript syntax in chart components

---

### Issue 4: Livewire Not Working

**Symptoms**:

-   Filters don't update
-   Search doesn't work
-   Pagination doesn't work

**Solution**:

```bash
php artisan livewire:publish --assets
php artisan view:clear
```

**Check**:

-   Livewire scripts should be at bottom of page
-   Check browser console for Livewire errors
-   Ensure `@livewireStyles` and `@livewireScripts` are present

---

### Issue 5: Styles Look Broken

**Symptoms**:

-   No colors
-   Wrong layout
-   Elements overlapping

**Solution**:
Since we're using Tailwind via CDN, this should work automatically. If not:

1. **Check internet connection** (CDN requires internet)
2. **Hard refresh browser** (Ctrl+Shift+R)
3. **Clear browser cache**
4. **Check console** for CDN loading errors

---

### Issue 6: Cannot Access data_anbksulteng.test

**Symptoms**:

-   Site not found
-   Connection refused
-   DNS error

**Solution**:

1. **Check FlyEnv is running**

    - Ensure FlyEnv service is active
    - Check FlyEnv dashboard/control panel

2. **Check host configuration**

    - Verify `data_anbksulteng.test` is configured in FlyEnv
    - Check hosts file if needed

3. **Try alternative**:
    ```bash
    # If FlyEnv has issues, use Laravel's built-in server
    php artisan serve
    # Then visit: http://127.0.0.1:8000
    ```

---

### Issue 7: Database Connection Error

**Symptoms**:

-   No data showing
-   Stats cards show 0
-   Database error messages

**Solution**:

1. Check `.env` file:

    ```env
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=your_database
    DB_USERNAME=root
    DB_PASSWORD=
    ```

2. Ensure database exists and has data:

    ```bash
    php artisan migrate
    php artisan db:seed  # if you have seeders
    ```

3. Test database connection:
    ```bash
    php artisan tinker
    >>> \App\Models\PelaksanaanAsesmen::count()
    ```

---

### Issue 8: Stats Cards Show Wrong Numbers

**Symptoms**:

-   Stats show 0 or incorrect values
-   Stats don't match expected data

**Solution**:

1. **Check database has data**:

    ```sql
    SELECT COUNT(*) FROM pelaksanaan_asesmen;
    SELECT COUNT(*) FROM sekolah;
    SELECT COUNT(*) FROM wilayah;
    ```

2. **Verify relationships**:

    - Check model relationships are correct
    - Ensure foreign keys exist

3. **Clear query cache**:
    ```bash
    php artisan cache:clear
    ```

---

### Issue 9: Search/Filter Not Working

**Symptoms**:

-   Typing in search box does nothing
-   Selecting filter dropdown does nothing

**Solution**:

1. **Check Livewire is loaded**:

    - View page source
    - Ensure `@livewireScripts` is present
    - Check browser console for Livewire errors

2. **Check wire:model syntax**:

    ```blade
    wire:model.live.debounce.500ms="search"
    wire:model.live="filterTahun"
    ```

3. **Republish Livewire assets**:
    ```bash
    php artisan livewire:publish --assets --force
    ```

---

### Issue 10: Pagination Not Working

**Symptoms**:

-   Page numbers don't work
-   Clicking next/prev does nothing

**Solution**:

1. Ensure `WithPagination` trait is used in DataTable component
2. Check pagination links are rendered:
    ```blade
    {{ $data->links() }}
    ```
3. Clear view cache:
    ```bash
    php artisan view:clear
    ```

---

## 🔧 Quick Fix Commands

### Reset Everything

```bash
php artisan view:clear
php artisan config:clear
php artisan route:clear
php artisan cache:clear
```

### Republish Assets

```bash
php artisan livewire:publish --assets --force
```

### Check Routes

```bash
php artisan route:list --name=public
```

### Check Database

```bash
php artisan tinker
>>> \App\Models\PelaksanaanAsesmen::count()
>>> \App\Models\Sekolah::count()
```

---

## 📞 Still Having Issues?

### Debug Mode

1. Set `APP_DEBUG=true` in `.env`
2. Refresh page
3. Read full error message
4. Check `storage/logs/laravel.log`

### Browser Console

1. Press F12
2. Go to Console tab
3. Look for JavaScript errors
4. Look for failed network requests

### Check Logs

```bash
# View latest errors
tail -50 storage/logs/laravel.log

# Clear log file (if too large)
echo "" > storage/logs/laravel.log
```

---

## ✅ Verification Checklist

After fixing issues, verify:

-   [ ] Page loads without errors
-   [ ] Tailwind CSS styles are applied
-   [ ] Stats cards show correct numbers
-   [ ] Both charts render correctly
-   [ ] Search box works
-   [ ] All three filters work
-   [ ] Pagination works
-   [ ] No errors in browser console
-   [ ] No errors in Laravel logs
-   [ ] Mobile view looks good

---

## 🎯 Performance Tips

### If Page Loads Slowly

1. **Add query indexes** (already done in migrations)
2. **Enable query caching**:

    ```php
    // In StatsOverview.php
    $stats = Cache::remember('dashboard_stats', 300, function() {
        return [
            'total_sekolah' => PelaksanaanAsesmen::distinct('sekolah_id')->count(),
            // ... etc
        ];
    });
    ```

3. **Optimize images** (if you add any)
4. **Use lazy loading** for charts

---

**Last Updated**: 2025-11-21  
**Status**: Vite manifest issue FIXED ✅  
**Current Solution**: Using Tailwind CSS via CDN
