# 🚀 CARA DEPLOY PERUBAHAN KE SERVER HOSTING

## 📍 Perubahan yang Sudah Di-commit

Commit terakhir:
- `4b45e04` - struk (fix struk pembayaran)
- `f44765d` - petaa (fix peta anomali Jakarta)

### File yang sudah diperbaiki:
1. ✅ `resources/views/pdf/struk-pembayaran.blade.php` - Alamat Ciwuni, fix abonemen
2. ✅ `app/Http/Controllers/PembayaranController.php` - Fix logika pemakaian air
3. ✅ `app/Http/Controllers/HomeController.php` - Fix koordinat default peta

---

## 🔑 Cara Deploy ke Server Hosting

### **Option 1: Via SSH Terminal** (Recommended)

#### Step 1: Login ke Server via SSH
```bash
# Ganti dengan server Anda
ssh user@your-domain.com

# Atau jika pakai port custom
ssh -p 2222 user@your-domain.com
```

#### Step 2: Masuk ke Folder Project
```bash
cd /path/to/your/project
# Contoh: cd /home/username/public_html/pamsimas
# Atau: cd /var/www/pamsimas
```

#### Step 3: Pull Perubahan Terbaru dari Git
```bash
# Pull dari branch main
git pull origin main

# Atau jika ada konflik, reset dulu
git fetch origin
git reset --hard origin/main
```

#### Step 4: Clear Cache Laravel di Server
```bash
# Clear semua cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Rebuild cache untuk production
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

#### Step 5: Set Permissions (jika perlu)
```bash
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

#### Step 6: Reload PHP-FPM (jika pakai Nginx)
```bash
sudo systemctl reload php8.2-fpm
# Atau
sudo systemctl reload php-fpm
```

#### Step 7: Test di Browser
```
1. Buka website Anda
2. Hard refresh: Ctrl + Shift + R (atau Cmd + Shift + R di Mac)
3. Test generate struk baru
4. Verify alamat: Ciwuni, Kesugihan, Cilacap
5. Verify pemakaian air terisi
6. Verify biaya abonemen Rp 3.000 muncul
```

---

### **Option 2: Via cPanel File Manager** (Jika tidak ada SSH)

#### Step 1: Backup Dulu (Safety First!)
```
1. Login cPanel
2. File Manager
3. Kompres folder project
4. Download backup
```

#### Step 2: Upload File yang Sudah Diubah

**Upload file-file ini dari local ke server:**

1. **struk-pembayaran.blade.php**
   - Local: `C:\laragon\www\PAMSIMAS\resources\views\pdf\struk-pembayaran.blade.php`
   - Server: `/home/user/public_html/resources/views/pdf/struk-pembayaran.blade.php`

2. **PembayaranController.php**
   - Local: `C:\laragon\www\PAMSIMAS\app\Http\Controllers\PembayaranController.php`
   - Server: `/home/user/public_html/app/Http/Controllers/PembayaranController.php`

3. **HomeController.php**
   - Local: `C:\laragon\www\PAMSIMAS\app\Http\Controllers\HomeController.php`
   - Server: `/home/user/public_html/app/Http/Controllers/HomeController.php`

#### Step 3: Clear Cache via cPanel Terminal
```
1. cPanel → Terminal
2. cd public_html (atau folder project Anda)
3. Jalankan:
   php artisan cache:clear
   php artisan config:clear
   php artisan view:clear
```

#### Step 4: Set Permissions
```bash
chmod -R 755 storage
chmod -R 755 bootstrap/cache
```

#### Step 5: Test di Browser
```
Hard refresh browser: Ctrl + Shift + R
```

---

### **Option 3: Via FTP (FileZilla/WinSCP)**

#### Step 1: Connect ke Server
```
- Host: ftp.yourdomain.com (atau IP server)
- Username: your-ftp-username
- Password: your-ftp-password
- Port: 21 (atau 22 untuk SFTP)
```

#### Step 2: Upload File yang Diubah
```
1. Navigate ke folder project di server
2. Upload 3 file yang sudah diperbaiki (lihat list di Option 2 Step 2)
3. Overwrite file lama
```

#### Step 3: Clear Cache
```
Via cPanel Terminal atau SSH (lihat Option 1 Step 4)
```

---

## 🔍 Verifikasi Deployment Berhasil

### Test 1: Cek Alamat di Struk
```
1. Login sebagai pengelola
2. Pilih pelanggan (contoh: DW017)
3. Generate struk pembayaran
4. Download PDF
5. Verify: "Desa Ciwuni, Kec. Kesugihan, Kabupaten Cilacap"
```

### Test 2: Cek Pemakaian Air
```
1. Buat pembayaran baru atau generate ulang struk
2. Verify: Pemakaian Air tidak 0,00 m³
3. Verify: Biaya Abonemen Rp 3.000 muncul
```

### Test 3: Cek Peta
```
1. Buka halaman Peta
2. Verify: Tidak ada marker di Jakarta
3. Verify: Marker kantor/sumber air di Damar Wulan (jika sudah input)
```

---

## ⚠️ Troubleshooting

### Q: "git pull" error: "Your local changes would be overwritten"
```bash
# Backup perubahan lokal di server dulu
git stash

# Pull
git pull origin main

# Apply kembali perubahan lokal (jika perlu)
git stash pop
```

### Q: "Permission denied" saat git pull
```bash
# Cek ownership folder
ls -la

# Fix ownership
sudo chown -R $USER:$USER .

# Atau pakai sudo
sudo git pull origin main
```

### Q: Struk masih lama setelah deploy
```bash
# Clear cache browser
Ctrl + Shift + R

# Clear cache Laravel di server
php artisan cache:clear
php artisan view:clear

# Hapus struk lama di server
rm -rf public/storage/struk/*.pdf
```

### Q: Tidak ada akses SSH
```
Gunakan Option 2 (cPanel) atau Option 3 (FTP)
Upload manual file yang diubah
Clear cache via cPanel Terminal
```

### Q: "php artisan" not found
```bash
# Gunakan full path PHP
/usr/bin/php artisan cache:clear

# Atau cari PHP path
which php
```

---

## 📋 Quick Checklist Deployment

Sebelum deploy:
- [x] Commit sudah ada di git local (`git log` untuk verify)
- [ ] Push ke repository (`git push origin main`)

Di server hosting:
- [ ] Login SSH atau cPanel
- [ ] `cd` ke folder project
- [ ] `git pull origin main`
- [ ] `php artisan cache:clear`
- [ ] `php artisan config:clear`
- [ ] `php artisan view:clear`
- [ ] `chmod -R 755 storage bootstrap/cache`
- [ ] Test di browser (hard refresh)

Verifikasi:
- [ ] Alamat struk: Ciwuni, Kesugihan, Cilacap
- [ ] Pemakaian air terisi (bukan 0,00)
- [ ] Biaya abonemen Rp 3.000 muncul
- [ ] Peta tidak ada marker Jakarta

---

## 🎯 Command Summary (Copy-Paste Siap Pakai)

### Di Server (via SSH):
```bash
cd /path/to/your/project
git pull origin main
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan config:cache
php artisan route:cache
chmod -R 755 storage bootstrap/cache
```

### Di Browser:
```
1. Buka website
2. Tekan: Ctrl + Shift + R (hard refresh)
3. Test generate struk baru
4. Verify perubahan
```

---

## 💡 Tips

### Auto-Deploy (Opsional)
Jika sering update, setup webhook GitHub → Server auto-pull saat ada push.

### Git Deployment Script
Buat file `deploy.sh` di server:
```bash
#!/bin/bash
cd /path/to/project
git pull origin main
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan config:cache
php artisan route:cache
chmod -R 755 storage bootstrap/cache
echo "✅ Deployment selesai!"
```

Jalankan: `bash deploy.sh`

---

## 📞 Butuh Bantuan?

Jika ada error saat deploy, kirim:
1. Error message lengkap
2. Hosting provider (shared/VPS/cloud)
3. Akses SSH ada atau tidak

---

**Last Updated**: 7 Februari 2026  
**Ready to Deploy**: ✅
