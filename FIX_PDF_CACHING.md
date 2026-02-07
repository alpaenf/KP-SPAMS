# Fix PDF Caching Issue

## Tanggal: <?= date('d F Y') ?>

## Problem
Link struk PDF di hosting selalu sama dan isinya tidak pernah berubah:
- URL: `https://kp-spamsdammarwulan.com/storage/struk/struk_DW017_202602.pdf`
- Browser dan CDN menyimpan cache file lama
- Meskipun data sudah diupdate, PDF yang ditampilkan tetap versi lama

## Root Cause
Nama file PDF yang static menyebabkan browser/CDN caching:
```php
// SEBELUM (static filename)
$fileName = 'struk_' . $pembayaran->pelanggan->id_pelanggan . '_' . str_replace('-', '', $pembayaran->bulan_bayar) . '.pdf';
// Hasil: struk_DW017_202602.pdf (selalu sama)
```

Filename yang sama akan di-cache oleh:
- Browser cache
- CDN cache  
- Proxy cache

## Solution Implemented

### 1. Tambah Timestamp di Nama File
File: `app/Http/Controllers/PembayaranController.php`

```php
// SESUDAH (unique filename dengan timestamp)
$timestamp = time();
$fileName = 'struk_' . $pembayaran->pelanggan->id_pelanggan . '_' . str_replace('-', '', $pembayaran->bulan_bayar) . '_' . $timestamp . '.pdf';
// Hasil: struk_DW017_202602_1707307890.pdf (selalu beda)
```

### 2. Hapus File Lama Otomatis
Sebelum generate PDF baru, hapus file lama untuk hemat space:

```php
// Delete old struk files for this pelanggan and month
$oldPattern = public_path('storage/struk/struk_' . $pembayaran->pelanggan->id_pelanggan . '_' . str_replace('-', '', $pembayaran->bulan_bayar) . '_*.pdf');
foreach (glob($oldPattern) as $oldFile) {
    if (file_exists($oldFile)) {
        @unlink($oldFile);
    }
}
```

### 3. Always Regenerate PDF
Method `getReceiptLink()` sekarang selalu regenerate PDF untuk ensure data fresh:

```php
public function getReceiptLink($id)
{
    // ... validation ...
    
    // Always regenerate PDF to ensure fresh data
    return $this->sendReceipt($id);
}
```

## Benefits
✅ Setiap generate struk selalu dapat file baru dengan timestamp unik  
✅ Browser/CDN tidak bisa cache karena URL selalu berbeda  
✅ Data struk selalu fresh sesuai database terbaru  
✅ Space storage tetap efficient (file lama otomatis dihapus)  

## Deployment Steps

### 1. Commit & Push ke Production
```bash
git add .
git commit -m "Fix: Add timestamp to PDF filename to prevent caching issue"
git push origin main
```

### 2. Deploy di Server Hosting
Login ke server hosting dan jalankan:
```bash
cd /path/to/website
bash deploy.sh
# atau
git pull origin main
```

### 3. Clear Cache di Server
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
```

### 4. Clear Old PDF Files (Optional)
Jika mau hapus semua file PDF lama:
```bash
rm -f public/storage/struk/*.pdf
```

### 5. Test Generate Struk
- Buka admin panel
- Pilih data pembayaran
- Klik "Kirim Struk"
- Cek link baru: `struk_DW017_202602_1707308123.pdf`
- Verify data sudah benar

## Verification Checklist
- [ ] Deploy ke production berhasil
- [ ] Cache sudah di-clear di server
- [ ] Generate struk baru menghasilkan filename dengan timestamp
- [ ] Link WhatsApp berisi URL yang unik
- [ ] Isi struk menampilkan data terbaru:
  - [x] Alamat: Desa Ciwuni, Kec. Kesugihan, Kabupaten Cilacap
  - [x] No WA: 085228357400 (akan diupdate via script)
  - [x] Pemakaian air: nilai sebenarnya (bukan 0,00 m³)
  - [x] Biaya abonemen: Rp 3.000 selalu muncul

## Notes
- Timestamp menggunakan `time()` Unix timestamp (seconds since 1970)
- Format: `struk_{ID_PELANGGAN}_{BULAN}_{TIMESTAMP}.pdf`
- File lama otomatis dihapus saat generate baru untuk pelanggan+bulan yang sama
- CDN/Browser cache tidak akan affected karena setiap URL unik

## Related Issues Fixed
- ✅ Alamat salah → Fixed di `struk-pembayaran.blade.php`
- ✅ Pemakaian air 0,00 m³ → Fixed logic di `PembayaranController.php`
- ✅ Biaya abonemen tidak muncul → Fixed display di blade template
- ✅ PDF caching issue → Fixed dengan timestamp filename

## References
- [FIX_STRUK_PEMBAYARAN.md](FIX_STRUK_PEMBAYARAN.md)
- [update_no_whatsapp.php](update_no_whatsapp.php)
