# ⚠️ Fix: Production Database Missing 'wilayah' Column

## 🔍 Problem
Error saat tambah user penarik di production:
```
SQLSTATE[42S22]: Column not found: 1054 Unknown column 'wilayah' in 'INSERT INTO'
```

**Root Cause:** Migration belum dijalankan di server production.

---

## ✅ Solution: Run Migration di Production

### SSH ke Server Production
```bash
ssh user@kp-spamsdammarwulan.com
# atau gunakan panel hosting
```

### Navigate ke Directory Aplikasi
```bash
cd /home/kpsx9679/public_html
# atau path aplikasi Laravel Anda
```

---

## 🚀 Run Commands Step-by-Step

### 1. Cek Migration Status
```bash
php artisan migrate:status
```

**Cari migration ini:**
- `modify_role_and_add_wilayah_to_users_table`

**Status harus:** `Pending` (belum dijalankan)

---

### 2. Backup Database DULU!
```bash
# Via command line
mysqldump -u kpsx9679_prod_user -p kpsx9679_prod > backup_before_migration.sql

# Atau via cPanel / phpMyAdmin:
# Export database → Download .sql file
```

⚠️ **PENTING:** Jangan skip backup!

---

### 3. Run Migration
```bash
php artisan migrate --force
```

**Expected output:**
```
INFO  Running migrations.

2026_02_08_054857_modify_role_and_add_wilayah_to_users_table ... DONE
```

---

### 4. Verify Migration Berhasil
```bash
php artisan migrate:status
```

**Expected:** `modify_role_and_add_wilayah_to_users_table` status **Ran** ✅

---

### 5. Cek Struktur Tabel Users
```bash
php artisan tinker --execute="print_r(DB::select('DESCRIBE users'));"
```

**Harus ada kolom:**
- `role` → enum('admin','penarik')
- `wilayah` → enum('dawuhan','kubangsari_kulon','kubangsari_wetan','sokarame','tiparjaya')

---

### 6. Run Seeder (Buat Akun Penarik)
```bash
php artisan db:seed --class=PenarikSeeder --force
```

**Expected output:**
```
✅ 5 akun penarik berhasil dibuat!
📧 Email: dawuhan@pamsimas.com, kubkul@pamsimas.com, dll
🔑 Password: password
📍 PIN: 1234
```

---

### 7. Verify Akun Penarik Sudah Ada
```bash
php artisan tinker --execute="echo User::where('role', 'penarik')->count();"
```

**Expected:** `5`

```bash
php artisan tinker --execute="print_r(User::where('role', 'penarik')->get(['name','email','wilayah'])->toArray());"
```

**Expected:** 5 users dengan nama, email, dan wilayah yang benar.

---

### 8. Clear All Cache
```bash
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
```

---

## ✅ Test After Fix

### 1. Test Login Penarik
```
URL: https://kp-spamsdammarwulan.com/login
Email: sokarame@pamsimas.com
Password: password
```

**Expected:** Login berhasil ✅

---

### 2. Test Tambah User Penarik Baru
```
1. Login sebagai admin
2. Buka: https://kp-spamsdammarwulan.com/admin/pengelola
3. Input PIN
4. Klik "Tambah Akun"
5. Isi form:
   - Nama: King
   - Email: sokarame.penarik@pamsimas.com
   - Role: Penarik
   - Wilayah: Sokarame (dropdown harus muncul)
6. Submit
```

**Expected:** User berhasil dibuat tanpa error ✅

---

## 🐛 Troubleshooting

### Error: "Nothing to migrate"
**Penyebab:** Migration sudah pernah dijalankan tapi rolback/gagal.

**Fix:**
```bash
# Cek migrations table
php artisan tinker --execute="print_r(DB::table('migrations')->latest()->take(5)->get()->toArray());"

# Hapus entry migration yang bermasalah
php artisan tinker --execute="DB::table('migrations')->where('migration', 'LIKE', '%add_role_and_wilayah%')->delete();"

# Run ulang
php artisan migrate --force
```

---

### Error: "SQLSTATE[01000]: Warning: 1265 Data truncated"
**Penyebab:** Ada user dengan role 'pengelola' di database.

**Fix:** Migration kita sudah handle ini, harusnya tidak error. Tapi kalau masih error:
```bash
# Update manual role pengelola → admin
php artisan tinker --execute="DB::table('users')->where('role', 'pengelola')->update(['role' => 'admin']);"

# Lalu run migration
php artisan migrate --force
```

---

### Error: Permission denied
**Fix:**
```bash
# Set permissions
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

---

## 📝 Verification Checklist

- [ ] Backup database sudah dibuat
- [ ] Migration berhasil run
- [ ] Kolom `role` dan `wilayah` sudah ada di tabel users
- [ ] Seeder berhasil, 5 akun penarik sudah dibuat
- [ ] Test login penarik berhasil
- [ ] Test tambah user baru di UI berhasil
- [ ] Cache sudah di-clear

---

## 🚨 Quick Command (Copy-Paste)

```bash
# 1. Masuk ke directory
cd /home/kpsx9679/public_html

# 2. Backup database (GANTI username & password!)
mysqldump -u DB_USERNAME -p kpsx9679_prod > backup_$(date +%Y%m%d_%H%M%S).sql

# 3. Run migration
php artisan migrate --force

# 4. Run seeder
php artisan db:seed --class=PenarikSeeder --force

# 5. Clear cache
php artisan config:clear && php artisan route:clear && php artisan view:clear && php artisan cache:clear

# 6. Verify
php artisan tinker --execute="echo 'Penarik: ' . User::where('role', 'penarik')->count();"

echo "✅ Done! Silakan test login dan tambah user."
```

---

## 📞 Support

Kalau masih error setelah langkah ini:
1. Cek log: `tail -f storage/logs/laravel.log`
2. Cek PHP version: `php -v` (harus 8.1+)
3. Cek database connection di `.env`

---

**Document created:** 8 Feb 2026  
**Issue:** Missing 'wilayah' column in production  
**Status:** Awaiting migration execution
