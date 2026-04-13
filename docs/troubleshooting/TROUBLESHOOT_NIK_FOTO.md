# Troubleshooting: NIK dan Foto Rumah Tidak Tersimpan

## Masalah
Data NIK dan foto rumah berhasil diinput dan muncul pesan sukses, tapi saat dicek di tabel atau edit lagi, datanya kosong.

## Penyebab Umum

### 1. Migration Belum Dijalankan di Server Production
**Gejala:**
- Kolom `nik` dan `foto_rumah` belum ada di database
- Error tidak muncul karena Laravel ignore kolom yang tidak ada di fillable

**Solusi:**
```bash
# Di server production via SSH
cd /path/to/your/app
php artisan migrate

# Jika migration tidak terdeteksi
php artisan migrate:status
php artisan cache:clear
php artisan config:clear
php artisan migrate
```

### 2. Storage Link Belum Dibuat
**Gejala:**
- Foto tidak bisa diakses (404 error)
- URL foto: `https://domain.com/storage/foto_rumah/xxx.jpg` tidak tersedia

**Solusi:**
```bash
# Buat symbolic link dari storage ke public
php artisan storage:link
```

**Verifikasi:**
```bash
ls -la public/storage
# Harus muncul link: storage -> ../storage/app/public
```

### 3. Permission Folder Storage
**Gejala:**
- Error saat upload foto
- "Unable to write file"

**Solusi:**
```bash
# Set permission yang benar
chmod -R 775 storage
chmod -R 775 bootstrap/cache
chown -R www-data:www-data storage
chown -R www-data:www-data bootstrap/cache
```

### 4. File .htaccess Blocking Storage
**Gejala:**
- Foto tidak bisa diakses meski file ada

**Cek:**
```bash
# Pastikan file ada
ls storage/app/public/foto_rumah/

# Cek via browser
curl https://domain.com/storage/foto_rumah/xxx.jpg
```

### 5. Cache Browser
**Gejala:**
- Data sudah ada di database tapi frontend masih tampil kosong

**Solusi:**
- Hard refresh browser: `Ctrl + Shift + R`
- Clear cache browser
- Clear Laravel cache:
```bash
php artisan cache:clear
php artisan view:clear
php artisan config:clear
```

## Cara Check Manual

### 1. Check Database
```bash
# Login ke MySQL
mysql -u root -p

# Pilih database
use pamsimas;

# Check struktur tabel
DESCRIBE pelanggan;
# Harus ada kolom: nik (varchar 16), foto_rumah (varchar 255)

# Check data
SELECT id, id_pelanggan, nama_pelanggan, nik, foto_rumah 
FROM pelanggan 
WHERE nik IS NOT NULL OR foto_rumah IS NOT NULL 
LIMIT 5;
```

### 2. Check File Upload
```bash
# Jalankan script check
php check_nik_foto.php

# Atau manual check
ls -la storage/app/public/foto_rumah/
```

### 3. Test Upload
```bash
# Buat file test
echo "test" > storage/app/public/test.txt

# Cek bisa diakses
curl http://localhost/storage/test.txt
# atau
curl https://domain.com/storage/test.txt
```

## Checklist Deployment

Setiap kali deploy perubahan database atau file upload:

```bash
# 1. Pull code terbaru
git pull origin main

# 2. Install dependencies (jika ada perubahan)
composer install --no-dev --optimize-autoloader

# 3. Jalankan migration
php artisan migrate --force

# 4. Buat storage link (jika belum)
php artisan storage:link

# 5. Clear cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# 6. Build assets (jika ada perubahan frontend)
npm install
npm run build

# 7. Set permission
chmod -R 775 storage bootstrap/cache

# 8. Restart services
sudo systemctl restart php8.2-fpm
sudo systemctl restart nginx
# atau
service apache2 restart
```

## Verifikasi Setelah Deploy

### Test di Browser
1. Buka halaman "Data Pelanggan"
2. Klik "Tambah Pelanggan"
3. Input data termasuk NIK dan foto
4. Submit form
5. Cek tabel - NIK dan foto harus muncul
6. Klik "Edit" - data harus tetap ada
7. Klik "Lihat Foto" - foto harus terbuka di modal

### Test via API
```bash
# Test upload
curl -X POST https://domain.com/pelanggan \
  -F "id_pelanggan=TEST001" \
  -F "nama_pelanggan=Test User" \
  -F "nik=1234567890123456" \
  -F "foto_rumah=@/path/to/photo.jpg" \
  -F "kategori=umum" \
  -F "wilayah=Test" \
  -H "Cookie: laravel_session=xxx"
```

## File-file Terkait

### Migration
```
database/migrations/2026_02_13_030249_add_nik_and_foto_rumah_to_pelanggan_table.php
```

### Controller
```
app/Http/Controllers/HomeController.php
- storePelanggan() - line 717
- updatePelanggan() - line 772
- editPelanggan() - line 748
- cekPelanggan() - line 330
```

### Model
```
app/Models/Pelanggan.php
- $fillable - line 14 (nik, foto_rumah)
- getFotoRumahUrlAttribute() - line 144
```

### Frontend
```
resources/js/Pages/Pelanggan/Create.vue
resources/js/Pages/Pelanggan/Edit.vue
resources/js/Pages/CekPelanggan.vue
```

## Kontak dan Bantuan
Jika masih bermasalah setelah mengikuti langkah di atas, check:
1. Laravel log: `storage/logs/laravel.log`
2. PHP error log: `/var/log/php8.2-fpm.log`
3. Nginx/Apache error log
