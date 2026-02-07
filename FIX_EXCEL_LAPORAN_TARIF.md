# Fix Excel Export Laporan - Tarif & Abonemen

## Tanggal: 7 Februari 2026

## Problem
Excel export dari halaman Laporan Bulanan menampilkan data yang salah:
1. **Kolom "Tarif/Kubik"** menampilkan **Rp 0** (harusnya Rp 2.000 atau sesuai tarif di tagihan)
2. **Kolom "Abunemen"** menampilkan **Rp 1** (harusnya Rp 3.000 atau sesuai abonemen di tagihan)

Screenshot user menunjukkan:
```
Bulan Bayar    | Jumlah Kubik | Tarif/Kubik | Abunemen | Total Bayar
Februari 2026  | 14,00        | Rp 0        | Rp 1     | Rp 31.000
Januari 2026   | 12,00        | Rp 0        | Rp 1     | Rp 27.000
```

## Root Cause

### 1. Field Tidak Ada di Tabel Pembayaran
File `LaporanExport.php` mencoba akses field yang tidak exist:
```php
// SEBELUM (SALAH)
'Rp ' . number_format($pembayaran->tarif_per_kubik ?? 0, 0, ',', '.')  // Field tidak ada!
'Rp ' . number_format($pembayaran->abunemen ?? 0, 0, ',', '.')         // Field adalah boolean!
```

Tabel `pembayarans` tidak memiliki kolom `tarif_per_kubik`, dan kolom `abunemen` adalah **boolean** (true/false), bukan integer rupiah.

### 2. Data Tarif Ada di Tabel TagihanBulanan
Data tarif sebenarnya tersimpan di tabel `tagihan_bulanan`:
- `tarif_per_kubik` (integer, contoh: 2000)
- `biaya_abunemen` (integer, contoh: 3000)

Tapi export Excel tidak query/join ke tabel ini.

## Solution Implemented

### 1. Pre-load TagihanBulanan di Constructor (Performance Optimization)
File: `app/Exports/LaporanExport.php`

```php
protected $tagihans;

public function __construct($data, $summary, $detail, $filters)
{
    $this->data = $data;
    $this->summary = $summary;
    $this->detail = $detail;
    $this->filters = $filters;
    
    // Pre-load all tagihan for better performance (avoid N+1 query)
    $pelangganIds = $data->pluck('pelanggan_id')->unique();
    $bulans = $data->pluck('bulan_bayar')->unique();
    
    $this->tagihans = TagihanBulanan::whereIn('pelanggan_id', $pelangganIds)
        ->whereIn('bulan', $bulans)
        ->get()
        ->groupBy(function($tagihan) {
            return $tagihan->pelanggan_id . '_' . $tagihan->bulan;
        });
}
```

**Benefit**: Hanya 1-2 query untuk load semua tagihan yang diperlukan, bukan N query di loop.

### 2. Ambil Tarif dari Tagihan dengan Default Fallback
Di method `collection()`:

```php
// Data Pembayaran Rows
$no = 1;
foreach ($this->data as $pembayaran) {
    $pelanggan = $pembayaran->pelanggan;
    
    // Get tarif dan abonemen dari tagihan atau default
    $tarifPerKubik = 2000; // Default
    $biayaAbunemen = 3000; // Default
    
    // Ambil tagihan dari pre-loaded data
    $key = $pembayaran->pelanggan_id . '_' . $pembayaran->bulan_bayar;
    $tagihans = $this->tagihans->get($key);
    
    if ($tagihans && $tagihans->isNotEmpty()) {
        $tagihan = $tagihans->first();
        if ($tagihan->tarif_per_kubik > 0) {
            $tarifPerKubik = $tagihan->tarif_per_kubik;
        }
        if ($tagihan->biaya_abunemen > 0) {
            $biayaAbunemen = $tagihan->biaya_abunemen;
        }
    }
    
    $rows->push([
        $no++,
        $pembayaran->tanggal_bayar->format('d/m/Y'),
        $pelanggan->id_pelanggan,
        $pelanggan->nama_pelanggan,
        'RT ' . $pelanggan->rt . ' / RW ' . $pelanggan->rw,
        $pelanggan->wilayah ?? '-',
        \Carbon\Carbon::parse($pembayaran->bulan_bayar)->locale('id')->isoFormat('MMMM Y'),
        number_format($pembayaran->jumlah_kubik ?? 0, 2, ',', '.'),
        'Rp ' . number_format($tarifPerKubik, 0, ',', '.'),      // ✅ Sekarang benar
        'Rp ' . number_format($biayaAbunemen, 0, ',', '.'),      // ✅ Sekarang benar
        'Rp ' . number_format($pembayaran->jumlah_bayar, 0, ',', '.')
    ]);
}
```

### 3. Import TagihanBulanan Model
File: `app/Exports/LaporanExport.php`

```php
use App\Models\TagihanBulanan;
```

## Expected Result (Setelah Fix)

Excel export akan menampilkan:
```
Bulan Bayar    | Jumlah Kubik | Tarif/Kubik | Abunemen   | Total Bayar
Februari 2026  | 14,00        | Rp 2.000    | Rp 3.000   | Rp 31.000
Januari 2026   | 12,00        | Rp 2.000    | Rp 3.000   | Rp 27.000
```

## Technical Notes

### Default Values
- **Tarif per kubik**: Rp 2.000 (jika tidak ada di tagihan_bulanan)
- **Biaya abonemen**: Rp 3.000 (wajib, consistent dengan struk pembayaran)

### Database Structure
**Tabel: pembayarans**
- `abunemen` → **boolean** (bukan nilai rupiah)
- Tidak ada kolom `tarif_per_kubik`

**Tabel: tagihan_bulanan**
- `tarif_per_kubik` → **integer** (nilai tarif per m³)
- `biaya_abunemen` → **integer** (nilai abonemen)
- `pelanggan_id` → foreign key
- `bulan` → format YYYY-MM (contoh: "2026-02")

### Query Optimization
Menggunakan **eager loading** dengan groupBy untuk avoid N+1 query problem:
- Load semua tagihan yang diperlukan di constructor (1-2 queries)
- Group by `pelanggan_id + bulan` untuk fast O(1) lookup
- No query inside loop → performance optimal

## Deployment Steps

### 1. Commit & Push
```bash
git add app/Exports/LaporanExport.php
git commit -m "Fix: Display correct tarif & abonemen in Excel export"
git push origin main
```

### 2. Deploy ke Server
```bash
cd /path/to/website
bash deploy.sh
# atau
git pull origin main
```

### 3. Test Export Excel
1. Login ke admin panel
2. Buka menu **Laporan Bulanan**
3. Pilih filter (tahun, bulan, wilayah)
4. Klik tombol **Export Excel**
5. Buka file Excel yang didownload
6. Verify kolom:
   - ✅ **Tarif/Kubik**: menampilkan Rp 2.000 atau sesuai tarif
   - ✅ **Abunemen**: menampilkan Rp 3.000 atau sesuai tagihan

## Verification Checklist
- [x] File LaporanExport.php sudah diperbaiki
- [x] Import TagihanBulanan model ditambahkan
- [x] Pre-load tagihan di constructor (optimization)
- [x] Loop menggunakan pre-loaded data (no N+1 query)
- [x] Default values untuk tarif (2000) dan abonemen (3000)
- [x] No syntax errors
- [ ] Deployed ke production
- [ ] Test export Excel dengan data real
- [ ] Verify tarif & abonemen display correctly

## Related Issues Fixed
- ✅ Tarif/Kubik menampilkan Rp 0 → Fixed, sekarang ambil dari tagihan
- ✅ Abonemen menampilkan Rp 1 → Fixed, sekarang Rp 3.000
- ✅ N+1 query problem → Optimized dengan pre-loading

## Related Files
- [FIX_STRUK_PEMBAYARAN.md](FIX_STRUK_PEMBAYARAN.md) - Fix struk PDF issues
- [FIX_PDF_CACHING.md](FIX_PDF_CACHING.md) - Fix PDF caching dengan timestamp
- [PembayaranController.php](app/Http/Controllers/PembayaranController.php) - Reference untuk logic tarif
