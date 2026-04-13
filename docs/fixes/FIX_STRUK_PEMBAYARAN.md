# 🔧 FIX STRUK PEMBAYARAN WHATSAPP

## 🐛 Masalah yang Ditemukan

Berdasarkan struk yang dihasilkan pada 6 Februari 2026 untuk pelanggan DW017 (Eko), ditemukan beberapa kesalahan:

### 1. ❌ **Alamat Desa/Kecamatan/Kabupaten Salah**
- **Salah**: "Desa Lempeni, Kec. Swakarya, Grobogan"
- **Benar**: "Desa Ciwuni, Kec. Kesugihan, Kabupaten Cilacap"

### 2. ❌ **Pemakaian Air Tidak Terdeteksi**
- Menampilkan: "Pemakaian Air: 0,00 m³"
- Padahal ada tagihan Rp 31.000 (berarti ada pemakaian)

### 3. ❌ **Biaya Abonemen Tidak Muncul**
- Seharusnya ada: "Biaya Abonemen: Rp 3.000" (wajib setiap bulan)
- Di struk hanya muncul jika field `abunemen` = true

### 4. ⚠️ **Nomor WhatsApp Lama**
- No WA yang muncul: 085228357400 (dari data pelanggan di database)
- Jika nomor sudah berubah, perlu diupdate di database

---

## ✅ Perbaikan yang Sudah Diterapkan

### 1. **Fix Alamat** ✅
**File**: `resources/views/pdf/struk-pembayaran.blade.php`

**Sebelum**:
```html
<p>Desa Lempeni, Kec. Swakarya, Grobogan</p>
```

**Sesudah**:
```html
<p>Desa Ciwuni, Kec. Kesugihan, Kabupaten Cilacap</p>
```

---

### 2. **Fix Logika Pemakaian Air** ✅
**File**: `app/Http/Controllers/PembayaranController.php`

**Perbaikan**:
- ✅ Prioritas ambil pemakaian dari `tagihan_bulanan.pemakaian_kubik`
- ✅ Jika tidak ada, hitung dari `meteran_sesudah - meteran_sebelum`
- ✅ Jika tetap 0, tetap tampilkan 0,00 m³ (bukan hide)

**Sebelum**:
```php
if ($jumlahKubik <= 0 && $tagihan->pemakaian_kubik > 0) {
    $jumlahKubik = $tagihan->pemakaian_kubik;
}
```

**Sesudah**:
```php
// Prioritas dari tagihan
if (!is_null($tagihan->pemakaian_kubik) && $tagihan->pemakaian_kubik > 0) {
    $jumlahKubik = $tagihan->pemakaian_kubik;
} elseif ($jumlahKubik <= 0 && $meteranSebelum && $meteranSesudah) {
    // Hitung dari meteran
    $jumlahKubik = $meteranSesudah - $meteranSebelum;
}
```

---

### 3. **Fix Biaya Abonemen** ✅
**File**: `resources/views/pdf/struk-pembayaran.blade.php`

**Perbaikan**:
- ✅ Biaya abonemen **SELALU** ditampilkan (default Rp 3.000)
- ✅ Tidak perlu kondisi `@if($pembayaran['abunemen'])`
- ✅ Bisa customize dari database jika `biaya_abunemen` tersedia

**Sebelum** (conditional):
```php
@if($pembayaran['abunemen'])
<div class="total-row">
    <div class="total-label">Abunemen</div>
    <div class="total-value">Rp 3.000</div>
</div>
@endif
```

**Sesudah** (always show):
```php
<div class="total-row">
    <div class="total-label">Biaya Abonemen</div>
    <div class="total-value">Rp {{ number_format($pembayaran['biaya_abunemen'] ?? 3000, 0, ',', '.') }}</div>
</div>
```

---

### 4. **Script Update No WhatsApp** 🆕
**File**: `update_no_whatsapp.php`

Script interaktif untuk update nomor WhatsApp pelanggan yang sudah berubah.

---

## 🚀 Cara Menggunakan

### **A. Update Nomor WhatsApp Pelanggan**

Jika nomor WhatsApp pelanggan sudah berubah:

#### Option 1: Via Script PHP (Recommended)
```bash
# Jalankan script
php update_no_whatsapp.php

# Ikuti instruksi:
# 1. Masukkan ID pelanggan (contoh: DW017)
# 2. Masukkan nomor baru (contoh: 085228357400)
# 3. Konfirmasi
```

#### Option 2: Via Database Manual
```sql
-- Buka phpMyAdmin atau MySQL
UPDATE pelanggan 
SET no_whatsapp = '6285228357400'  -- Nomor baru (gunakan format 62xxx)
WHERE id_pelanggan = 'DW017';       -- ID pelanggan
```

#### Option 3: Via UI Management
```
1. Login sebagai pengelola
2. Menu "Cek Pelanggan"
3. Cari pelanggan DW017
4. Klik "Edit"
5. Update No. WhatsApp
6. Simpan
```

---

### **B. Test Generate Struk Baru**

Setelah perbaikan, test generate struk:

1. **Pastikan ada tagihan bulan Februari 2026**:
   ```sql
   SELECT * FROM tagihan_bulanan 
   WHERE pelanggan_id = (SELECT id FROM pelanggan WHERE id_pelanggan = 'DW017')
     AND bulan = '2026-02';
   ```

2. **Pastikan data lengkap**:
   - `meteran_sebelum`: ada nilai
   - `meteran_sesudah`: ada nilai
   - `pemakaian_kubik`: harus > 0 (meteran_sesudah - meteran_sebelum)
   - `biaya_abunemen`: 3000 (default)
   - `tarif_per_kubik`: 2000 (default)

3. **Generate Ulang Struk**:
   ```
   1. Buka halaman pembayaran pelanggan
   2. Pilih bulan Februari 2026
   3. Klik "Kirim Struk ke WhatsApp"
   4. Struk baru akan ter-generate dengan data yang benar
   ```

---

### **C. Fix Data Tagihan yang Salah**

Jika pemakaian air masih 0,00 m³, cek dan perbaiki data tagihan:

```sql
-- Cek data tagihan
SELECT 
    id,
    pelanggan_id,
    bulan,
    meteran_sebelum,
    meteran_sesudah,
    pemakaian_kubik,
    total_tagihan
FROM tagihan_bulanan
WHERE bulan = '2026-02'
  AND pelanggan_id = (SELECT id FROM pelanggan WHERE id_pelanggan = 'DW017');

-- Jika pemakaian_kubik = 0, update:
UPDATE tagihan_bulanan
SET pemakaian_kubik = meteran_sesudah - meteran_sebelum,
    total_tagihan = ((meteran_sesudah - meteran_sebelum) * 2000) + 3000
WHERE id = YOUR_TAGIHAN_ID
  AND pemakaian_kubik = 0
  AND meteran_sebelum IS NOT NULL
  AND meteran_sesudah IS NOT NULL;
```

---

## 📋 Checklist Verifikasi Struk

Setelah fix, pastikan struk menampilkan:

- [ ] **Header**: "KP-SPAMS DAMAR WULAN"
- [ ] **Alamat**: "Desa Ciwuni, Kec. Kesugihan, Kabupaten Cilacap" ✅
- [ ] **ID Pelanggan**: DW017
- [ ] **Nama**: Eko
- [ ] **Alamat**: RT / RW
- [ ] **No. WhatsApp**: Nomor yang sudah diupdate ✅
- [ ] **No. Transaksi**: #000025
- [ ] **Tanggal Bayar**: 6 Februari 2026
- [ ] **Bulan Tagihan**: Februari 2026
- [ ] **Pemakaian Air**: X.XX m³ (bukan 0,00) ✅
- [ ] **Tarif per m³**: Rp 2.000
- [ ] **Subtotal**: Rp X.XXX (pemakaian × tarif)
- [ ] **Biaya Abonemen**: Rp 3.000 ✅
- [ ] **Tunggakan**: Rp X (jika ada)
- [ ] **TOTAL BAYAR**: Rp 31.000 (atau sesuai perhitungan)
- [ ] **Status**: ✓ LUNAS
- [ ] **Footer**: "Terima kasih atas pembayaran Anda!"

---

## 🔍 Troubleshooting

### Q: Pemakaian air masih 0,00 m³ setelah fix
**A**: Cek data tagihan di database:
```sql
SELECT meteran_sebelum, meteran_sesudah, pemakaian_kubik 
FROM tagihan_bulanan 
WHERE pelanggan_id = X AND bulan = '2026-02';
```
- Jika `pemakaian_kubik` = 0, update manual atau re-generate tagihan
- Jika `meteran_sebelum` atau `meteran_sesudah` NULL, input meteran dulu

---

### Q: Biaya abonemen tidak muncul
**A**: Setelah fix ini, abonemen **PASTI** muncul dengan nilai:
1. `biaya_abunemen` dari tagihan (jika ada)
2. Atau default Rp 3.000

Jika tetap tidak muncul, clear cache:
```bash
php artisan cache:clear
php artisan view:clear
```

---

### Q: No WhatsApp masih yang lama di struk
**A**: No WA diambil dari tabel `pelanggan.no_whatsapp`. Update dengan:
```bash
php update_no_whatsapp.php
```
Atau manual via UI/Database.

Setelah update, generate ulang struk.

---

### Q: Alamat masih Grobogan di struk lama
**A**: Struk lama (PDF yang sudah tersimpan) tidak akan berubah. Hapus file lama:
```bash
# Hapus struk lama
rm public/storage/struk/struk_DW017_202602.pdf
```
Lalu generate ulang.

---

## 📊 Contoh Struk yang Benar

```
========================================
    KP-SPAMS "DAMAR WULAN"
    Air Bersih untuk Kehidupan Sehat
Desa Ciwuni, Kec. Kesugihan, Kabupaten Cilacap
========================================

    *** STRUK PEMBAYARAN ***

┌──────────────────────────────────────┐
│ ID Pelanggan  : DW017                │
│ Nama          : Eko                  │
│ Alamat        : RT 5 / RW 2          │
│ No. WhatsApp  : 6285228357400        │
└──────────────────────────────────────┘

----------------------------------------
No. Transaksi  : #000025
Tanggal Bayar  : 6 Februari 2026
Bulan Tagihan  : Februari 2026
----------------------------------------

┌──────────────────────────────────────┐
│ Meteran Sebelum     100 m³           │
│ Meteran Sesudah     114 m³           │
│ ════════════════════════════         │
│ Pemakaian Air       14,00 m³         │
│ Tarif per m³        Rp 2.000         │
│ Subtotal            Rp 28.000        │
│ ────────────────────────────         │
│ Biaya Abonemen      Rp 3.000         │
│ Tunggakan           Rp 0             │
│ ════════════════════════════         │
│ TOTAL BAYAR         Rp 31.000        │
└──────────────────────────────────────┘

        ✓ LUNAS

Terima kasih atas pembayaran Anda!
Struk ini adalah bukti pembayaran yang sah
Simpan struk ini dengan baik

Dicetak pada: 06/02/2026 17:31 WIB
========================================
```

---

## ✅ Status Perbaikan

| Issue | Status | File |
|-------|--------|------|
| Alamat salah (Grobogan) | ✅ Fixed | struk-pembayaran.blade.php |
| Pemakaian air 0,00 m³ | ✅ Fixed | PembayaranController.php |
| Biaya abonemen tidak muncul | ✅ Fixed | struk-pembayaran.blade.php |
| No WA lama | ⚠️ Manual | update_no_whatsapp.php |

### Next Actions:
1. ✅ Clear cache Laravel
2. ⏳ Update No WA pelanggan yang berubah
3. ⏳ Fix data tagihan jika pemakaian_kubik = 0
4. ⏳ Generate ulang struk untuk test
5. ⏳ Verify struk baru sesuai checklist

---

**Last Updated**: 7 Februari 2026  
**Status**: Ready to Use ✅
