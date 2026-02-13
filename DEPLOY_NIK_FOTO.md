# Deploy NIK dan Foto Rumah

## Di Server Production:

```bash
# 1. Pull latest changes
cd ~/pamsimas
git pull origin main

# 2. Run migration
php artisan migrate
# Pilih "Yes" saat ditanya

# 3. Pastikan storage link ada (untuk upload foto)
php artisan storage:link

# 4. Build frontend assets
npm run build

# 5. Deploy ke public_html
bash deploy.sh

# 6. Set permission untuk folder storage (jika diperlukan)
chmod -R 775 storage/app/public/foto_rumah
chmod -R 775 bootstrap/cache
```

## Verifikasi:

1. Login ke aplikasi
2. Buka menu **Data Pelanggan**
3. Klik **Tambah** atau **Edit**
4. Coba isi NIK dan upload foto rumah
5. Cek apakah kolom NIK dan Foto Rumah muncul di tabel
6. Klik tombol **Lihat Foto** untuk test viewer

## Troubleshooting:

**Jika upload foto error "Failed to store":**
```bash
# Pastikan folder ada dan writable
mkdir -p storage/app/public/foto_rumah
chmod -R 775 storage/app/public/foto_rumah
chown -R kpsx9679:kpsx9679 storage/app/public/foto_rumah
```

**Jika foto tidak muncul (404):**
```bash
# Re-create storage link
rm public/storage
php artisan storage:link
```

## Fitur yang Ditambahkan:
- ✅ Kolom NIK (16 digit)
- ✅ Upload foto rumah (JPG/PNG max 2MB)
- ✅ Preview foto sebelum upload
- ✅ Modal viewer foto di list pelanggan
- ✅ Download foto
- ✅ Auto-delete foto lama saat update
