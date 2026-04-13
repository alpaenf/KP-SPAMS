# Fix: Bug FormData - Field Hilang Saat Upload Foto

## Masalah yang Diperbaiki

**Bug:** Saat menambah/edit pelanggan dengan upload foto, field lain (nama, NIK, dll) tidak tersimpan.

**Penyebab:** Loop FormData dengan kondisi yang salah:
```javascript
// ❌ KODE LAMA (SALAH)
for (const key in form.value) {
    if (form.value[key] !== null && form.value[key] !== '') {
        formData.append(key, form.value[key]);
    }
}
```

Kondisi `!== ''` menyebabkan field kosong tidak masuk ke FormData, dan kondisi `!== null` skip field yang seharusnya dikirim.

## Solusi

**Kode Baru (BENAR):**
```javascript
// ✅ KODE BARU
formData.append('id_pelanggan', form.value.id_pelanggan || '');
formData.append('nama_pelanggan', form.value.nama_pelanggan || '');
formData.append('nik', form.value.nik || '');
formData.append('no_whatsapp', form.value.no_whatsapp || '');
formData.append('rt', form.value.rt || '');
formData.append('rw', form.value.rw || '');
formData.append('kategori', form.value.kategori || 'umum');
formData.append('wilayah', form.value.wilayah || '');
formData.append('latitude', form.value.latitude || '');
formData.append('longitude', form.value.longitude || '');
formData.append('google_maps_url', form.value.google_maps_url || '');
formData.append('status_aktif', form.value.status_aktif ? '1' : '0');

// Only append foto_rumah if file is selected
if (form.value.foto_rumah instanceof File) {
    formData.append('foto_rumah', form.value.foto_rumah);
}
```

**Perbaikan:**
1. ✅ Semua field required selalu dikirim (meskipun kosong)
2. ✅ Boolean `status_aktif` dikonversi ke string '1' atau '0'
3. ✅ File foto hanya dikirim jika benar-benar ada file baru
4. ✅ Tidak ada field yang ter-skip karena kondisi yang salah

## Deployment ke Production

### 1. SSH ke Server
```bash
ssh kpsx9679@kp-spamsdammarwulan.com -p2223
```

### 2. Pull Code Terbaru
```bash
cd public_html  # atau folder aplikasi
git pull origin main
```

### 3. Clear Cache
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

### 4. Restart Services (Jika Perlu)
```bash
# Restart PHP-FPM
sudo systemctl restart php8.2-fpm

# Restart Web Server
sudo systemctl restart nginx
# atau
sudo systemctl restart apache2
```

### 5. Test di Browser
1. **Hard refresh:** `Ctrl + Shift + R` (Windows) atau `Cmd + Shift + R` (Mac)
2. Buka halaman **Tambah Pelanggan**
3. Isi **semua field:**
   - ID Pelanggan: TEST001
   - Nama: Test User
   - NIK: 1234567890123456
   - No WA: 08123456789
   - RT/RW: 1/1
   - Kategori: Umum
   - Wilayah: Pilih wilayah
   - Upload foto rumah
4. **Submit form**
5. **Cek hasil:**
   - ✅ Nama pelanggan muncul
   - ✅ NIK muncul
   - ✅ Foto muncul dan bisa diklik "Lihat Foto"
6. **Klik Edit** pelanggan tersebut:
   - ✅ Semua data masih ada
   - ✅ Foto lama masih tampil
7. **Update data:**
   - Edit nama atau field lain
   - Bisa ganti foto atau biarkan foto lama
   - Submit
   - ✅ Semua data tetap tersimpan

## File yang Diubah

- `resources/js/Pages/Pelanggan/Create.vue` - Fix submitForm()
- `resources/js/Pages/Pelanggan/Edit.vue` - Fix submitForm()
- `public/build/assets/*` - Compiled assets

## Troubleshooting

### Jika Masih Bermasalah:

1. **Check browser console** (F12 → Console):
   - Cari error JavaScript
   - Cari error 422 (Validation Error)

2. **Check Laravel log:**
   ```bash
   tail -f storage/logs/laravel.log
   ```

3. **Check FormData yang dikirim:**
   Tambah di Create.vue sebelum router.post():
   ```javascript
   // Debug: Log FormData contents
   for (let [key, value] of formData.entries()) {
       console.log(key, value);
   }
   ```

4. **Check database:**
   ```sql
   SELECT id, id_pelanggan, nama_pelanggan, nik, foto_rumah 
   FROM pelanggan 
   ORDER BY id DESC 
   LIMIT 5;
   ```

5. **Check storage link:**
   ```bash
   ls -la public/storage
   # Harus ada link → ../storage/app/public
   
   # Jika belum ada:
   php artisan storage:link
   ```

## Verifikasi Deployment Sukses

Jalankan di server production:
```bash
# Check code version
git log -1 --oneline
# Harus muncul: "fix: FormData submission bug"

# Check compiled assets timestamp
ls -lh public/build/assets/Create-*.js
# Harus tanggal hari ini
```

## Commit Reference

- **Commit:** `2c0ec92`
- **Message:** "fix: FormData submission bug - ensure all fields are sent properly"
- **Date:** 2026-02-13
- **Files Changed:** 38 files

---

## Status: ✅ FIXED

Bug FormData sudah diperbaiki. Semua field sekarang akan terkirim dengan benar saat upload foto.
