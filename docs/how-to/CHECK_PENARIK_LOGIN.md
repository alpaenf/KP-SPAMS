# 🔍 Checklist: Kenapa Penarik Gabisa Login?

## 1. Cek Migration di Server
```bash
ssh ke server
php artisan migrate:status
```

**Expected:** Migration `modify_role_and_add_wilayah_to_users_table` harus `Ran`

---

## 2. Cek Seeder Sudah Jalan?
```bash
# Di server
php artisan tinker --execute="echo User::where('role', 'penarik')->count();"
```

**Expected:** Output `5` (5 akun penarik)

**Kalau 0, berarti seeder belum jalan:**
```bash
php artisan db:seed --class=PenarikSeeder
```

---

## 3. Cek Struktur Tabel Users
```bash
php artisan tinker --execute="print_r(DB::select('DESCRIBE users'));"
```

**Expected:** Ada kolom:
- `role` enum('admin','penarik')
- `wilayah` enum('dawuhan','kubangsari_kulon','kubangsari_wetan','sokarame','tiparjaya')

---

## 4. Cek Data Penarik
```bash
php artisan tinker --execute="print_r(User::where('role', 'penarik')->get(['name','email','role','wilayah'])->toArray());"
```

**Expected:** 5 user dengan:
- dawuhan@pamsimas.com
- kubkul@pamsimas.com
- kubwet@pamsimas.com
- sokarame@pamsimas.com
- tiparjaya@pamsimas.com

---

## 5. Test Login Manual
Buka browser: `https://your-domain.com/login`

Login dengan:
- Email: `sokarame@pamsimas.com`
- Password: `password`

**Error yang mungkin muncul:**

### a) "These credentials do not match our records"
**Penyebab:** Seeder belum jalan atau password salah

**Fix:**
```bash
php artisan db:seed --class=PenarikSeeder
```

### b) "Unauthorized" atau redirect ke login lagi
**Penyebab:** Middleware atau auth guard issue

**Fix:** Cek `app/Models/User.php` method `canAccessFilament()`

### c) Blank page / 500 error
**Penyebab:** Assets belum di-build atau cache issue

**Fix:**
```bash
npm run build
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

---

## 6. Cek Log Error
```bash
tail -f storage/logs/laravel.log
```

Atau di Windows:
```powershell
Get-Content storage/logs/laravel.log -Tail 50
```

---

## ✅ Checklist Summary

- [ ] Migration sudah run di server
- [ ] Seeder sudah run (5 akun penarik ada)
- [ ] Kolom role & wilayah ada di tabel users
- [ ] Data penarik ter-insert dengan benar
- [ ] Password hash benar (Hash::make('password'))
- [ ] Login form accessible
- [ ] No error di logs

---

## 🚀 Quick Fix Command (Jalankan di Server)

```bash
# All-in-one fix
php artisan migrate
php artisan db:seed --class=PenarikSeeder
php artisan config:clear
php artisan route:clear
php artisan view:clear
npm run build

# Verify
php artisan tinker --execute="echo User::where('role', 'penarik')->count();"
```

Kalau output `5`, berarti berhasil!
