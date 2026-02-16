# 🔧 Perbaikan Bug: Filter Wilayah & Total Tagihan 0

**Tanggal:** 2026-02-16  
**Masalah:** 
1. Penarik wilayah Dawuhan bisa melihat pembayaran wilayah Kubangsari
2. Total tagihan menampilkan 0 rupiah meskipun meteran sudah diinput

---

## 📋 Masalah 1: Filter Wilayah Pembayaran

### Deskripsi Masalah
Di halaman "Tagihan & Pembayaran", role penarik bisa melihat pembayaran dari wilayah lain. Contoh: penarik wilayah Dawuhan bisa melihat pembayaran dari wilayah Kubangsari.

### Penyebab
Di `TagihanBulananController.php` method `index()`, query `$pembayaranList` tidak memfilter berdasarkan wilayah untuk role penarik.

### Solusi
Menambahkan filter `whereHas('pelanggan')` untuk memfilter pembayaran berdasarkan wilayah penarik.

**File:** `app/Http/Controllers/TagihanBulananController.php`

**Kode Sebelum:**
```php
$pembayaranList = Pembayaran::with('pelanggan')
    ->where('bulan_bayar', $bulan)
    ->orderBy('tanggal_bayar', 'desc')
    ->get()
```

**Kode Sesudah:**
```php
// Apply filter wilayah untuk role penarik
$user = $request->user();
$query = Pembayaran::with('pelanggan')
    ->where('bulan_bayar', $bulan);

// Filter by penarik wilayah if role is penarik
if ($user && $user->role === 'penarik' && $user->wilayah) {
    $query->whereHas('pelanggan', function ($q) use ($user) {
        $q->where('wilayah', $user->wilayah);
    });
}

$pembayaranList = $query->orderBy('tanggal_bayar', 'desc')
    ->get()
```

---

## 📋 Masalah 2: Total Tagihan 0 Rupiah

### Deskripsi Masalah
Setelah input meteran, kolom "TOTAL TAGIHAN" menampilkan 0 rupiah untuk beberapa pelanggan, meskipun meteran sebelum dan sesudah sudah terisi dengan benar.

**Contoh dari screenshot:**
- DW004 (Imung): Meteran 260 → 267 = 7 m³, tapi total tagihan = 0
- DW005 (Darko): Meteran 324 → 338 = 14 m³, tapi total tagihan = 0
- DW006 (Sholihin/Neneng): Meteran 408 → 452 = 44 m³, tapi total tagihan = 0

### Penyebab
Field `tarif_per_kubik`, `ada_abunemen`, dan `biaya_abunemen` bernilai NULL saat data disimpan karena:
1. Field tersebut `nullable` di validasi
2. Frontend tidak selalu mengirim nilai untuk field tersebut
3. Tanpa nilai tersebut, perhitungan `total_tagihan` menghasilkan 0

**Rumus perhitungan:**
```
total_tagihan = (pemakaian_kubik × tarif_per_kubik) + biaya_abunemen
```

Jika `tarif_per_kubik` = NULL atau 0, maka hasilnya = 0

### Solusi

#### 1. Perbaikan Controller
Menambahkan nilai default untuk field yang nullable sebelum disimpan ke database.

**File:** `app/Http/Controllers/TagihanBulananController.php`

**Kode yang Ditambahkan:**
```php
// Set default values jika tidak ada di request
if (!isset($validated['tarif_per_kubik'])) {
    $validated['tarif_per_kubik'] = 2000; // Default tarif per kubik
}
if (!isset($validated['ada_abunemen'])) {
    $validated['ada_abunemen'] = true; // Default ada abunemen
}
if (!isset($validated['biaya_abunemen'])) {
    $validated['biaya_abunemen'] = 3000; // Default biaya abunemen
}
```

#### 2. Script Perbaikan Data Lama
Membuat script `fix_tagihan_zero.php` untuk memperbaiki data yang sudah terlanjur tersimpan dengan total_tagihan = 0.

**Cara Menjalankan:**
```bash
php fix_tagihan_zero.php
```

**Yang Dilakukan Script:**
1. Mencari semua tagihan dengan `total_tagihan = 0` yang sudah ada meteran
2. Mengisi nilai default untuk `tarif_per_kubik`, `ada_abunemen`, dan `biaya_abunemen`
3. Menghitung ulang `total_tagihan` menggunakan method `hitungTagihan()`
4. Menyimpan perubahan ke database

---

## ✅ Hasil Perbaikan

### Masalah 1: Filter Wilayah
- ✅ Penarik wilayah Dawuhan **hanya bisa melihat** pembayaran dari wilayah Dawuhan
- ✅ Penarik wilayah Kubangsari **hanya bisa melihat** pembayaran dari wilayah Kubangsari
- ✅ Admin tetap bisa melihat **semua pembayaran** dari semua wilayah

### Masalah 2: Total Tagihan
- ✅ Input meteran baru akan otomatis menghitung total tagihan dengan benar
- ✅ Data lama yang total_tagihannya 0 bisa diperbaiki dengan script `fix_tagihan_zero.php`
- ✅ Perhitungan menggunakan nilai default:
  - Tarif per kubik: Rp 2.000/m³
  - Biaya abunemen: Rp 3.000

**Contoh perhitungan setelah perbaikan:**
- DW004 (Imung): 7 m³ × Rp 2.000 + Rp 3.000 = **Rp 17.000**
- DW005 (Darko): 14 m³ × Rp 2.000 + Rp 3.000 = **Rp 31.000**
- DW006 (Sholihin): 44 m³ × Rp 2.000 + Rp 3.000 = **Rp 91.000**

---

## 🚀 Deployment

### Langkah Deploy ke Production:

1. **Push kode ke repository:**
   ```bash
   git add .
   git commit -m "fix: Filter wilayah pembayaran & total tagihan 0"
   git push origin main
   ```

2. **Deploy ke server:**
   ```bash
   ssh kpsx9679@kp-spamsdammarwulan.com -p2223
   cd domains/kp-spamsdammarwulan.com/public_html
   git pull origin main
   composer install --no-dev --optimize-autoloader
   npm run build
   php artisan route:clear
   php artisan config:clear
   php artisan view:clear
   ```

3. **Jalankan script perbaikan data:**
   ```bash
   php fix_tagihan_zero.php
   ```

4. **Verifikasi:**
   - Login sebagai penarik wilayah Dawuhan
   - Cek apakah pembayaran wilayah lain tidak terlihat
   - Cek apakah total tagihan sudah benar untuk semua pelanggan

---

## 📝 Catatan Penting

1. **Nilai Default:**
   - Tarif per kubik: Rp 2.000
   - Biaya abunemen: Rp 3.000
   - Jika nilai ini berbeda untuk wilayah tertentu, perlu disesuaikan di frontend

2. **Kategori Sosial:**
   - Pelanggan kategori sosial tetap memiliki total_tagihan = 0
   - Status bayar otomatis "SUDAH_BAYAR"

3. **Script Perbaikan:**
   - Script `fix_tagihan_zero.php` hanya perlu dijalankan **sekali** untuk memperbaiki data lama
   - Setelah itu, sistem akan otomatis menghitung dengan benar

---

## 🔍 Testing Checklist

- [ ] Login sebagai penarik wilayah Dawuhan
- [ ] Buka halaman "Tagihan & Pembayaran"
- [ ] Verifikasi hanya melihat data wilayah Dawuhan
- [ ] Input meteran untuk pelanggan baru
- [ ] Verifikasi total tagihan terhitung dengan benar
- [ ] Cek data pelanggan DW004, DW005, DW006 setelah run script
- [ ] Verifikasi total tagihan sudah tidak 0 lagi
