# 🔧 Fix DNS Error - Setup Virtual Host

## Masalah
Error `DNS_PROBE_FINISHED_NXDOMAIN` terjadi karena:
1. Hosts file memiliki entry `data_anbksulteng` (tanpa `.test`)
2. Anda mencoba akses `data_anbksulteng.test`
3. Virtual host nginx mungkin belum dikonfigurasi dengan benar

---

## Solusi Cepat

### Opsi 1: Akses Tanpa .test (TERCEPAT)

Coba akses menggunakan URL:
```
http://data_anbksulteng/admin
```

Bukan:
```
http://data_anbksulteng.test/admin
```

---

### Opsi 2: Update Hosts File (RECOMMENDED)

#### Step 1: Jalankan PowerShell sebagai Administrator

1. Klik kanan pada PowerShell
2. Pilih "Run as Administrator"

#### Step 2: Jalankan Script Update Hosts

```powershell
cd d:\BIRMAN-DEV\app
.\update_hosts.ps1
```

Script ini akan:
- Backup hosts file
- Update entry dari `data_anbksulteng` menjadi `data_anbksulteng.test`
- Menambahkan entry untuk IPv4 dan IPv6

#### Step 3: Flush DNS Cache

```powershell
ipconfig /flushdns
```

---

### Opsi 3: Cek dan Buat Virtual Host Nginx

#### Step 1: Cari Lokasi Nginx Config

Nginx Anda ada di:
```
C:\Users\antoa\AppData\Local\Programs\PhpWebData\app\nginx-1.29.3\nginx-1.29.3
```

#### Step 2: Buat Virtual Host Config

Buat file konfigurasi nginx untuk project ini. Saya akan buatkan template-nya.

---

## Verifikasi

Setelah melakukan salah satu opsi di atas:

1. **Test DNS Resolution**:
   ```powershell
   ping data_anbksulteng.test
   ```
   
   Harusnya reply dari `127.0.0.1`

2. **Test Web Server**:
   ```powershell
   curl http://data_anbksulteng.test
   ```

3. **Buka di Browser**:
   ```
   http://data_anbksulteng.test/admin
   ```

---

## Troubleshooting

### Jika masih error setelah update hosts:

1. **Restart Nginx**:
   ```powershell
   # Stop nginx
   Get-Process nginx | Stop-Process -Force
   
   # Start nginx (sesuaikan path)
   cd C:\Users\antoa\AppData\Local\Programs\PhpWebData\app\nginx-1.29.3\nginx-1.29.3
   .\nginx.exe
   ```

2. **Cek apakah port 80 digunakan**:
   ```powershell
   netstat -ano | findstr :80
   ```

3. **Restart Browser**:
   - Tutup semua tab browser
   - Buka kembali

---

## Alternatif: Gunakan PHP Built-in Server

Jika nginx terlalu rumit, gunakan PHP built-in server:

```bash
cd d:\BIRMAN-DEV\app
php artisan serve --host=0.0.0.0 --port=8000
```

Lalu akses:
```
http://localhost:8000/admin
```

---

## Rekomendasi

Saya sarankan **Opsi 1** (akses tanpa .test) sebagai solusi tercepat untuk saat ini.

Jika ingin menggunakan `.test`, ikuti **Opsi 2** dengan menjalankan PowerShell sebagai Administrator.
