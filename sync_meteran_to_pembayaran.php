<?php

/**
 * Script untuk sync data meteran dari tagihan_bulanan ke pembayarans
 * Data meteran di pembayarans masih kosong, harus diambil dari tagihan_bulanan
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Pembayaran;
use App\Models\TagihanBulanan;

echo "=== Sync Data Meteran dari Tagihan Bulanan ke Pembayaran ===\n\n";

// Ambil semua pembayaran yang meteran-nya masih NULL
$pembayaranList = Pembayaran::whereNull('meteran_sebelum')
    ->orWhereNull('meteran_sesudah')
    ->get();

echo "Total pembayaran yang perlu diupdate: {$pembayaranList->count()}\n\n";

$updated = 0;
$skipped = 0;

foreach ($pembayaranList as $pembayaran) {
    // Cari tagihan_bulanan untuk pelanggan dan bulan yang sama
    $tagihan = TagihanBulanan::where('pelanggan_id', $pembayaran->pelanggan_id)
        ->where('bulan', $pembayaran->bulan_bayar)
        ->first();
    
    if ($tagihan) {
        // Update data meteran dari tagihan
        $pembayaran->meteran_sebelum = $tagihan->meteran_sebelum;
        $pembayaran->meteran_sesudah = $tagihan->meteran_sesudah;
        $pembayaran->jumlah_kubik = $tagihan->pemakaian_kubik;
        $pembayaran->abunemen = $tagihan->ada_abunemen;
        $pembayaran->save();
        
        $updated++;
        echo "✓ Updated Pembayaran ID {$pembayaran->id} - {$pembayaran->pelanggan->nama_pelanggan} ({$pembayaran->bulan_bayar})\n";
        echo "  Meteran: {$tagihan->meteran_sebelum} → {$tagihan->meteran_sesudah} = {$tagihan->pemakaian_kubik} m³\n\n";
    } else {
        $skipped++;
        echo "✗ Skipped Pembayaran ID {$pembayaran->id} - Tidak ada tagihan bulanan\n\n";
    }
}

echo "\n=== SELESAI ===\n";
echo "Total diupdate: $updated\n";
echo "Total diskip: $skipped\n";
