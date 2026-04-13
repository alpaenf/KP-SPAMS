# 🎯 CARA RUN & TEST FITUR ROLE PENARIK

## 📦 Step 1: Run Migration

```bash
cd C:\laragon\www\PAMSIMAS
php artisan migrate
```

**Output yang diharapkan:**
```
Migration table created successfully.
Migrating: 2026_02_08_053220_add_role_and_wilayah_to_users_table
Migrated:  2026_02_08_053220_add_role_and_wilayah_to_users_table
```

## 🌱 Step 2: Run Seeder

```bash
php artisan db:seed --class=PenarikSeeder
```

**Output yang diharapkan:**
```
✅ 5 akun penarik berhasil dibuat!
📧 Email: dawuhan@pamsimas.com, kubkul@pamsimas.com, dll
🔑 Password: password
📍 PIN: 1234
```

## 👥 Step 3: Daftar Akun Penarik

| Wilayah | Email | Password | PIN |
|---------|-------|----------|-----|
| Dawuhan | dawuhan@pamsimas.com | password | 1234 |
| Kubangsari Kulon | kubkul@pamsimas.com | password | 1234 |
| Kubangsari Wetan | kubwet@pamsimas.com | password | 1234 |
| Sokarame | sokarame@pamsimas.com | password | 1234 |
| Tiparjaya | tiparjaya@pamsimas.com | password | 1234 |

## 🧪 Step 4: Testing

### Test Login Penarik

1. Logout dari akun admin (jika sedang login)
2. Login dengan email: `dawuhan@pamsimas.com`, password: `password`
3. Akan masuk ke dashboard

### Test Filter Wilayah

**Yang HARUS terlihat:**
✅ Hanya pelanggan dengan wilayah = "Dawuhan"
✅ Badge/info menampilkan "Wilayah Anda: Dawuhan"
✅ Stats dashboard hanya untuk Dawuhan
✅ Peta hanya menampilkan marker Dawuhan
✅ Input meteran hanya untuk pelanggan Dawuhan
✅ Laporan hanya menampilkan data Dawuhan

**Yang TIDAK BOLEH terlihat:**
❌ Pelanggan dari wilayah lain (Kubangsari, Sokarame, dll)
❌ Filter wilayah manual (hidden untuk penarik)
❌ Menu tambah pelanggan baru (jika sudah dibatasi)

### Test Login Admin

1. Logout dari akun penarik
2. Login sebagai admin
3. Akan terlihat **SEMUA DATA** dari **SEMUA WILAYAH**
4. Filter wilayah manual masih berfungsi

## 🔍 Cara Verifikasi

### 1. Check Database
```sql
-- Lihat user yang sudah dibuat
SELECT id, name, email, role, wilayah FROM users;

-- Hitung pelanggan per wilayah
SELECT wilayah, COUNT(*) as jumlah FROM pelanggan GROUP BY wilayah;
```

### 2. Check di Browser DevTools
```javascript
// Di console browser setelah login sebagai penarik
console.log($page.props.auth.user);
// Output harus tampil: { role: 'penarik', wilayah: 'Dawuhan', ... }
```

### 3. Test Flow Lengkap

**Sebagai Penarik Dawuhan:**
- ✅ Login → Dashboard → Hanya stats Dawuhan
- ✅ Menu Data Pelanggan → Hanya pelanggan Dawuhan
- ✅ Menu Input Meteran → Hanya pelanggan Dawuhan
- ✅ Menu Laporan → Hanya data Dawuhan
- ✅ Menu Peta → Hanya marker Dawuhan

**Sebagai Admin:**
- ✅ Login → Dashboard → Stats semua wilayah
- ✅ Bisa pilih filter wilayah
- ✅ Lihat semua pelanggan dari semua wilayah
- ✅ Bisa tambah/edit/delete pelanggan

## 🐛 Troubleshooting

### Error: "Column 'role' or 'wilayah' not found"
**Solusi:**
```bash
php artisan migrate:fresh
php artisan db:seed --class=PenarikSeeder
```

### Penarik masih bisa lihat data wilayah lain
**Cek:**
1. Pastikan user punya wilayah: `SELECT wilayah FROM users WHERE email='dawuhan@pamsimas.com'`
2. Pastikan pelanggan punya wilayah: `SELECT id_pelanggan, wilayah FROM pelanggan LIMIT 10`
3. Clear cache: `php artisan optimize:clear`

### Data tidak ter-filter
**Cek controller:**
- Pastikan query menggunakan `Pelanggan::forUser()` bukan `Pelanggan::where()`
- Check di file: HomeController, TagihanBulananController, LaporanController

## 📝 Notes untuk Production

### Sebelum Deploy:

1. **Update existing users** - Set role untuk user yang sudah ada:
```sql
-- Set admin untuk user existing
UPDATE users SET role = 'admin', wilayah = NULL WHERE id = 1;
```

2. **Assign wilayah ke penarik** - Jika ada user yang perlu jadi penarik:
```sql
UPDATE users 
SET role = 'penarik', wilayah = 'Dawuhan'  
WHERE email = 'petugas1@email.com';
```

3. **Pastikan pelanggan punya wilayah**:
```sql
-- Cek pelanggan tanpa wilayah
SELECT COUNT(*) FROM pelanggan WHERE wilayah IS NULL;

-- Update jika ada yang NULL
UPDATE pelanggan SET wilayah = 'Dawuhan' WHERE rt = '001';
```

### Setelah Deploy:

1. Test dengan akun nyata (bukan seeder)
2. Verifikasi penarik tidak bisa akses data wilayah lain
3. Training user cara login dan pakai fitur
4. Monitor log untuk error

## 🎓 Training untuk Penarik

**Yang perlu diajarkan:**
1. Cara login (email & password)
2. Menu apa saja yang bisa diakses
3. Cara input meteran pelanggan
4. Cara lihat riwayat pembayaran
5. Kenapa cuma keliatan pelanggan wilayahnya aja (fitur, bukan bug!)

## ✅ Checklist Go-Live

- [ ] Migration sudah di-run
- [ ] Seeder sudah di-run (atau buat user manual)
- [ ] Test login admin - bisa lihat semua data
- [ ] Test login penarik - cuma lihat wilayahnya
- [ ] Pelanggan sudah punya wilayah semua
- [ ] Training penarik sudah selesai
- [ ] Backup database sebelum deploy
- [ ] Monitor error log 24 jam pertama

## 🆘 Support

Jika ada masalah, check:
1. Laravel log: `storage/logs/laravel.log`
2. Browser console untuk JS error
3. Database query log (enable di `.env`: `DB_LOG_QUERIES=true`)
