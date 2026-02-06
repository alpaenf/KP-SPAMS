# Deploy Auto-Fill Meteran ke Server

## Langkah Deploy ke Server Hosting

### 1. SSH ke Server
```bash
ssh user@kp-spamsdammarwulan.com
# atau
ssh user@your-server-ip
```

### 2. Masuk ke Folder Project
```bash
cd domains/kp-spamsdammarwulan.com/pamsimas
# atau
cd ~/pamsimas
```

### 3. Pull Perubahan dari Git
```bash
git pull origin main
```

### 4. Perbaiki Data Februari DWH0001 (ID Pelanggan 9)
```bash
php artisan tinker --execute="
DB::table('tagihan_bulanan')
  ->where('pelanggan_id', 9)
  ->where('bulan', '2026-02')
  ->update([
    'meteran_sebelum' => 88.00,
    'meteran_sesudah' => null,
    'pemakaian_kubik' => 0,
    'total_tagihan' => 0,
    'status_bayar' => 'BELUM_BAYAR'
  ]);
echo 'Data Februari DWH0001 diperbaiki';
"
```

### 5. Clear Cache Laravel
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
php artisan optimize:clear
```

### 6. Copy Build Assets ke Public HTML
```bash
# Asumsi public_html ada di level yang sama dengan folder pamsimas
cp -r public/build ../public_html/
```

### 7. Test API Endpoint
```bash
# Test apakah API sudah bisa ambil data Januari
curl "https://kp-spamsdammarwulan.com/api/tagihan-bulanan/9/2026-01"

# Test apakah API sudah bisa ambil data Februari
curl "https://kp-spamsdammarwulan.com/api/tagihan-bulanan/9/2026-02"
```

## Verifikasi di Browser

1. Buka https://kp-spamsdammarwulan.com/cek-pelanggan
2. Tekan **Ctrl+Shift+R** (hard refresh) atau **Ctrl+F5**
3. Klik tombol **"Aksi Pembayaran"** untuk pelanggan DWH0001 (Hoerul)
4. Meteran Sebelumnya harus terisi **88** (bukan 0)

## File yang Diupdate

1. ✅ `app/Http/Controllers/TagihanBulananController.php` - API endpoint untuk ambil data pembayaran lama
2. ✅ `resources/js/Pages/CekPelanggan.vue` - Logic auto-fill meteran dari bulan lalu
3. ✅ `resources/js/Pages/TagihanBulanan/Index.vue` - Auto-fill untuk form input & pembayaran
4. ✅ `resources/js/Pages/QRScanner/InputMeteran.vue` - Auto-fill untuk QR Scanner
5. ✅ `public/build/*` - JavaScript yang sudah di-compile

## Fitur Auto-Fill Meteran

Sekarang sistem akan otomatis mengisi **meteran_sebelum** dari **meteran_sesudah bulan lalu**:

- Januari meteran_sesudah = 88
- Februari meteran_sebelum = 88 (otomatis dari Januari)

Berlaku untuk:
- ✅ Halaman Cek Data Pelanggan - Form Pembayaran
- ✅ Halaman Tagihan Bulanan - Form Input Meteran
- ✅ Halaman Tagihan Bulanan - Form Pembayaran
- ✅ Halaman QR Scanner - Input Meteran
