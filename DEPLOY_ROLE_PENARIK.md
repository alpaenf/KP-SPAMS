# 🚀 Deployment Checklist: Role Penarik

## 📋 Langkah Deployment ke Production

### 1. Push Code ke Repository
```bash
git add .
git commit -m "feat: Add role penarik dengan wilayah scoping dan UI manajemen"
git push origin main
```

---

### 2. Di Server Production

#### a) Pull Latest Code
```bash
cd /path/to/your/app
git pull origin main
```

#### b) Install Dependencies (jika ada perubahan)
```bash
composer install --no-dev
npm install
```

#### c) Build Production Assets
```bash
npm run build
```

#### d) Run Migration
```bash
php artisan migrate

# Verify migration berhasil
php artisan migrate:status
```

**Expected:** `modify_role_and_add_wilayah_to_users_table` status `Ran`

#### e) Run Seeder untuk Buat Akun Penarik
```bash
php artisan db:seed --class=PenarikSeeder
```

**Output expected:**
```
✅ 5 akun penarik berhasil dibuat!
📧 Email: dawuhan@pamsimas.com, kubkul@pamsimas.com, dll
🔑 Password: password
📍 PIN: 1234
```

#### f) Verify Data Penarik
```bash
php artisan tinker --execute="echo User::where('role', 'penarik')->count();"
```

**Expected output:** `5`

```bash
php artisan tinker --execute="print_r(User::where('role', 'penarik')->get(['name','email','wilayah'])->toArray());"
```

**Expected:** 5 user dengan email dan wilayah yang benar

#### g) Clear All Cache
```bash
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
```

#### h) Set Permissions (jika perlu)
```bash
chgrp -R www-data storage bootstrap/cache
chmod -R ug+rwx storage bootstrap/cache
```

---

## ✅ Testing di Production

### 1. Test Login Penarik
```
URL: https://your-domain.com/login
Email: sokarame@pamsimas.com
Password: password
```

**Expected:**
- ✅ Login berhasil
- ✅ Redirect ke dashboard
- ✅ Data pelanggan hanya tampil wilayah Sokarame

### 2. Test QR Scanner Security
- Scan QR pelanggan dari wilayah sendiri → **Harus berhasil**
- Scan QR pelanggan dari wilayah lain → **Harus error 403**

### 3. Test UI Manajemen Penarik
```
1. Login sebagai admin
2. Buka: https://your-domain.com/admin/pengelola
3. Input PIN (default: 123456)
4. Klik "Tambah Akun"
5. Pilih role "Penarik"
6. Field wilayah harus muncul
7. Isi form dan submit
8. Verify user baru muncul di tabel
```

---

## 🔍 Troubleshooting

### ❌ Problem: Penarik gabisa login

**Diagnosa:**
```bash
# Cek apakah akun penarik ada
php artisan tinker --execute="User::where('email', 'sokarame@pamsimas.com')->first();"
```

**Fix:**
- Kalau `null` → Seeder belum jalan, run: `php artisan db:seed --class=PenarikSeeder`
- Kalau ada tapi password salah → Reset password di database

---

### ❌ Problem: Field wilayah tidak muncul di form

**Penyebab:** Assets belum di-build

**Fix:**
```bash
npm run build
php artisan view:clear
```

---

### ❌ Problem: Error 500 saat buka manajemen pengelola

**Cek log:**
```bash
tail -f storage/logs/laravel.log
```

**Common fixes:**
- Clear cache: `php artisan config:clear`
- Permissions: `chmod -R 775 storage`

---

### ❌ Problem: QR Scanner tidak filter by wilayah

**Diagnosa:**
```bash
# Cek kolom wilayah di pelanggan
php artisan tinker --execute="echo Pelanggan::first()->wilayah;"
```

**Fix:**
- Kalau `null` → Data pelanggan belum punya wilayah
- Update data pelanggan dengan wilayah yang benar

---

## 📊 Monitoring & Audit

### Cek Log Unauthorized Access
```bash
# Lihat log penarik yang coba akses wilayah lain
tail -f storage/logs/laravel.log | grep "Penarik mencoba"

# Atau di Windows
Get-Content storage/logs/laravel.log -Tail 50 | Select-String "Penarik mencoba"
```

### Cek Jumlah User per Role
```bash
php artisan tinker --execute="
echo 'Admin: ' . User::where('role', 'admin')->count() . PHP_EOL;
echo 'Pengelola: ' . User::where('role', 'pengelola')->count() . PHP_EOL;
echo 'Penarik: ' . User::where('role', 'penarik')->count() . PHP_EOL;
"
```

---

## 📚 Referensi Dokumentasi

- [QR_SECURITY_ROLE_PENARIK.md](QR_SECURITY_ROLE_PENARIK.md) - Keamanan QR code
- [IMPLEMENTASI_ROLE_PENARIK.md](IMPLEMENTASI_ROLE_PENARIK.md) - Technical docs
- [CHECK_PENARIK_LOGIN.md](CHECK_PENARIK_LOGIN.md) - Debug login issues

---

## 🎯 Quick Deploy Command (All-in-One)

```bash
#!/bin/bash

# Pull latest code
git pull origin main

# Install dependencies
composer install --no-dev --optimize-autoloader
npm install
npm run build

# Run migrations
php artisan migrate --force

# Seed penarik accounts
php artisan db:seed --class=PenarikSeeder

# Clear all cache
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
php artisan optimize

# Set permissions
chgrp -R www-data storage bootstrap/cache
chmod -R ug+rwx storage bootstrap/cache

echo "✅ Deployment selesai!"
echo "📝 Silakan test login dengan akun penarik"
```

**Save sebagai:** `deploy-penarik.sh`

**Run dengan:**
```bash
chmod +x deploy-penarik.sh
./deploy-penarik.sh
```

---

## ⚠️ Catatan Penting

1. **Backup database** sebelum run migration di production
2. **Test di staging** dulu kalau ada
3. **Informasikan downtime** ke user jika perlu
4. **Monitor error logs** setelah deployment
5. **Simpan kredensial penarik** dengan aman

---

**Document created:** 8 Feb 2026  
**Status:** Ready for Production Deployment  
**Version:** 1.0
