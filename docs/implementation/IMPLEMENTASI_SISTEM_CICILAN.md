# 🔄 IMPLEMENTASI SISTEM CICILAN PEMBAYARAN

## 📋 RINGKASAN
Sistem cicilan memungkinkan pelanggan membayar tagihan secara bertahap (nyicil). Sistem akan tracking berapa yang sudah dibayar dan otomatis update status jika sudah lunas.

---

## 🗂️ FILE YANG SUDAH DIUPDATE

### ✅ 1. Migration (SUDAH DIBUAT)
**File:** `database/migrations/2026_02_17_232700_add_jumlah_terbayar_to_tagihan_bulanan.php`
- Menambahkan kolom `jumlah_terbayar` untuk tracking total yang sudah dibayar

### ✅ 2. Model TagihanBulanan (SUDAH DIUPDATE)
**File:** `app/Models/TagihanBulanan.php`
- Tambah `jumlah_terbayar` ke fillable dan casts
- Method `getSisaTagihanAttribute()` - Hitung sisa tagihan
- Method `isLunas()` - Cek apakah sudah lunas
- Method `tambahCicilan($jumlah)` - Tambah cicilan dan auto-update status

---

## 🔧 FILE YANG PERLU DIUPDATE MANUAL

### 3. PembayaranController.php
**File:** `app/Http/Controllers/PembayaranController.php`
**Baris:** 170-235 (method `store`)

**GANTI KODE INI:**
```php
// Update atau create tagihan_bulanan
$tagihan = \App\Models\TagihanBulanan::where('pelanggan_id', $pelangganId)
    ->where('bulan', $validated['bulan_bayar'])
    ->first();

if ($tagihan) {
    // ... kode lama ...
    $keterangan = strtoupper($validated['keterangan'] ?? '');
    
    if ($keterangan === 'TUNGGAKAN') {
        $tagihan->status_bayar = 'BELUM_BAYAR';
    } elseif ($keterangan === 'CICILAN') {
        // Logika lama yang tidak track cicilan
        $totalTagihanRef = $tagihan->total_tagihan > 0 ? $tagihan->total_tagihan : $validated['jumlah_bayar'];
        if ($validated['jumlah_bayar'] >= $totalTagihanRef) {
            $tagihan->status_bayar = 'SUDAH_BAYAR';
        } else {
            $tagihan->status_bayar = 'BELUM_BAYAR';
        }
    } else {
        $tagihan->status_bayar = 'SUDAH_BAYAR';
    }
    
    $tagihan->save();
}
```

**DENGAN KODE BARU:**
```php
// Update atau create tagihan_bulanan
$tagihan = \App\Models\TagihanBulanan::where('pelanggan_id', $pelangganId)
    ->where('bulan', $validated['bulan_bayar'])
    ->first();

$keterangan = strtoupper($validated['keterangan'] ?? 'LUNAS');

if ($tagihan) {
    // Update data meteran dan tagihan agar sinkron dengan pembayaran
    $tagihan->meteran_sebelum = $validated['meteran_sebelum'] ?? $tagihan->meteran_sebelum;
    $tagihan->meteran_sesudah = $validated['meteran_sesudah'] ?? $tagihan->meteran_sesudah;
    $tagihan->pemakaian_kubik = $validated['jumlah_kubik'] ?? $tagihan->pemakaian_kubik;
    
    // Jika pembayaran menyertakan abunemen, update juga
    if (isset($validated['abunemen'])) {
        $tagihan->ada_abunemen = $validated['abunemen'];
    }
    
    // LOGIKA CICILAN BARU
    if ($keterangan === 'TUNGGAKAN') {
        // TUNGGAKAN: Tidak bayar sama sekali, status tetap BELUM_BAYAR
        $tagihan->status_bayar = 'BELUM_BAYAR';
        // Tidak update jumlah_terbayar
        
    } elseif ($keterangan === 'CICILAN') {
        // CICILAN: Tambahkan ke jumlah yang sudah terbayar
        $tagihan->tambahCicilan($validated['jumlah_bayar']);
        // Status auto-update di method tambahCicilan()
        
    } else {
        // LUNAS: Bayar penuh langsung
        $tagihan->jumlah_terbayar = $validated['jumlah_bayar'];
        $tagihan->status_bayar = 'SUDAH_BAYAR';
    }
    
    $tagihan->save();
} else {
    // Jika belum ada tagihan, buat baru
    $statusBayar = 'SUDAH_BAYAR';
    $jumlahTerbayar = $validated['jumlah_bayar'];
    
    if ($keterangan === 'TUNGGAKAN') {
        $statusBayar = 'BELUM_BAYAR';
        $jumlahTerbayar = 0; // Belum bayar sama sekali
    } elseif ($keterangan === 'CICILAN') {
        $statusBayar = 'BELUM_BAYAR'; // Cicilan dianggap belum lunas
        $jumlahTerbayar = $validated['jumlah_bayar']; // Catat cicilan pertama
    }
    
    \App\Models\TagihanBulanan::create([
        'pelanggan_id' => $pelangganId,
        'bulan' => $validated['bulan_bayar'],
        'meteran_sebelum' => $validated['meteran_sebelum'] ?? 0,
        'meteran_sesudah' => $validated['meteran_sesudah'] ?? 0,
        'pemakaian_kubik' => $validated['jumlah_kubik'] ?? 0,
        'ada_abunemen' => $validated['abunemen'] ?? false,
        'total_tagihan' => $validated['jumlah_bayar'],
        'jumlah_terbayar' => $jumlahTerbayar,
        'status_bayar' => $statusBayar,
        'tanggal_bayar' => $validated['tanggal_bayar'],
    ]);
}
```

---

### 4. TagihanBulananController.php
**File:** `app/Http/Controllers/TagihanBulananController.php`

**TAMBAHKAN** di method `index()` saat return data pelanggan (sekitar baris 45-71):
```php
// Cek tunggakan bulan sebelumnya
$tunggakan = $this->getTunggakanSebelumnya($p->id, $bulan);

return [
    'id' => $p->id,
    'id_pelanggan' => $p->id_pelanggan,
    'nama_pelanggan' => $p->nama_pelanggan,
    // ... data lainnya ...
    'tagihan' => $tagihan ? [
        'id' => $tagihan->id,
        'bulan' => $tagihan->bulan,
        'meteran_sebelum' => $tagihan->meteran_sebelum,
        'meteran_sesudah' => $tagihan->meteran_sesudah,
        'pemakaian_kubik' => $tagihan->pemakaian_kubik,
        'total_tagihan' => $tagihan->total_tagihan,
        'jumlah_terbayar' => $tagihan->jumlah_terbayar,  // ← TAMBAHKAN INI
        'sisa_tagihan' => $tagihan->sisa_tagihan,        // ← TAMBAHKAN INI
        'status_bayar' => $tagihan->status_bayar,
        // ... data lainnya ...
    ] : null,
    'tunggakan' => $tunggakan,
];
```

---

### 5. Frontend Vue Components

#### A. TagihanBulanan/Index.vue
**File:** `resources/js/Pages/TagihanBulanan/Index.vue`

**TAMBAHKAN** di modal pembayaran (sekitar baris 728-752):
```vue
<!-- Setelah section Tunggakan, TAMBAHKAN ini: -->

<!-- Info Cicilan (jika ada) -->
<div v-if="selectedPelanggan?.tagihan?.jumlah_terbayar > 0 && selectedPelanggan?.tagihan?.status_bayar === 'BELUM_BAYAR'" 
     class="bg-blue-50 border border-blue-200 rounded-lg p-4">
    <h5 class="font-bold text-blue-900 mb-2 flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        Informasi Cicilan
    </h5>
    <div class="space-y-2 text-sm">
        <div class="flex justify-between">
            <span class="text-blue-700">Total Tagihan:</span>
            <span class="font-bold text-blue-900">{{ formatRupiah(selectedPelanggan.tagihan.total_tagihan) }}</span>
        </div>
        <div class="flex justify-between">
            <span class="text-blue-700">Sudah Dibayar:</span>
            <span class="font-bold text-green-600">{{ formatRupiah(selectedPelanggan.tagihan.jumlah_terbayar) }}</span>
        </div>
        <div class="flex justify-between border-t border-blue-200 pt-2">
            <span class="text-blue-700 font-bold">Sisa Tagihan:</span>
            <span class="font-bold text-red-600">{{ formatRupiah(selectedPelanggan.tagihan.sisa_tagihan) }}</span>
        </div>
    </div>
</div>
```

#### B. Home.vue & CekPelanggan.vue
**File:** `resources/js/Pages/Home.vue` dan `resources/js/Pages/CekPelanggan.vue`

**TAMBAHKAN** di bagian tampilan tagihan:
```vue
<!-- Tampilkan info cicilan jika ada -->
<div v-if="pelanggan.tagihan_bulan_ini && pelanggan.jumlah_terbayar > 0 && pelanggan.status_bayar !== 'Lunas'" 
     class="mt-4 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
    <h5 class="font-bold text-yellow-900 mb-2">💰 Informasi Cicilan</h5>
    <div class="space-y-1 text-sm">
        <div class="flex justify-between">
            <span>Sudah Dibayar:</span>
            <span class="font-bold text-green-600">{{ formatRupiah(pelanggan.jumlah_terbayar) }}</span>
        </div>
        <div class="flex justify-between">
            <span>Sisa Tagihan:</span>
            <span class="font-bold text-red-600">{{ formatRupiah(pelanggan.sisa_tagihan) }}</span>
        </div>
    </div>
</div>
```

---

## 🚀 CARA DEPLOY

### 1. Jalankan Migration
```bash
php artisan migrate
```

### 2. Update Data Lama (Opsional)
Jika ada data lama yang perlu di-update:
```bash
php artisan tinker
```
```php
// Set jumlah_terbayar = total_tagihan untuk yang sudah lunas
\App\Models\TagihanBulanan::where('status_bayar', 'SUDAH_BAYAR')
    ->update(['jumlah_terbayar' => \DB::raw('total_tagihan')]);
```

### 3. Build Frontend
```bash
npm run build
```

### 4. Test
- Test cicilan: Bayar sebagian tagihan
- Test lunas otomatis: Cicilan sampai total = tagihan
- Test tunggakan: Pilih keterangan "TUNGGAKAN"

---

## 📊 CONTOH FLOW CICILAN

**Tagihan Januari: Rp 20.000**

| Tanggal | Cicilan | Jumlah Terbayar | Sisa | Status |
|---------|---------|-----------------|------|--------|
| 5 Jan   | Rp 10.000 | Rp 10.000 | Rp 10.000 | BELUM_BAYAR |
| 15 Jan  | Rp 5.000  | Rp 15.000 | Rp 5.000  | BELUM_BAYAR |
| 25 Jan  | Rp 5.000  | Rp 20.000 | Rp 0      | **SUDAH_BAYAR** ✅ |

---

## ✅ CHECKLIST IMPLEMENTASI

- [x] Migration dibuat
- [x] Model TagihanBulanan updated
- [ ] PembayaranController.php - Update method store()
- [ ] TagihanBulananController.php - Tambah jumlah_terbayar & sisa_tagihan di response
- [ ] TagihanBulanan/Index.vue - Tampilkan info cicilan di modal
- [ ] Home.vue - Tampilkan info cicilan di landing page
- [ ] CekPelanggan.vue - Tampilkan info cicilan
- [ ] Test semua flow

---

## 🐛 TROUBLESHOOTING

**Q: Cicilan tidak bertambah?**
A: Pastikan method `tambahCicilan()` dipanggil di controller

**Q: Status tidak berubah jadi SUDAH_BAYAR?**
A: Cek method `isLunas()` di model, pastikan `jumlah_terbayar >= total_tagihan`

**Q: Data lama error?**
A: Jalankan update query di tinker untuk set jumlah_terbayar
