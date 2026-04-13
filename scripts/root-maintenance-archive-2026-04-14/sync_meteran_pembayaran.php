<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Pembayaran;
use App\Models\TagihanBulanan;

echo "=== Sync Meteran Data from Tagihan to Pembayaran ===\n\n";

// Ambil semua pembayaran yang jumlah_kubik nya 0 atau meteran kosong
$pembayaranList = Pembayaran::where(function ($q) {
    $q->whereNull('jumlah_kubik')
      ->orWhere('jumlah_kubik', 0)
      ->orWhereNull('meteran_sebelum')
      ->orWhereNull('meteran_sesudah');
})->get();

echo "Found {$pembayaranList->count()} pembayaran with missing meteran data\n\n";

$synced = 0;
$skipped = 0;

foreach ($pembayaranList as $pembayaran) {
    // Cari tagihan yang sesuai
    $tagihan = TagihanBulanan::where('pelanggan_id', $pembayaran->pelanggan_id)
        ->where('bulan', $pembayaran->bulan_bayar)
        ->first();
    
    if ($tagihan && $tagihan->pemakaian_kubik > 0) {
        $oldPemakaian = $pembayaran->jumlah_kubik;
        
        // Update data meteran dari tagihan
        $pembayaran->meteran_sebelum = $tagihan->meteran_sebelum;
        $pembayaran->meteran_sesudah = $tagihan->meteran_sesudah;
        $pembayaran->jumlah_kubik = $tagihan->pemakaian_kubik;
        $pembayaran->abunemen = $tagihan->ada_abunemen;
        $pembayaran->save();
        
        $synced++;
        echo "✅ Synced: {$pembayaran->pelanggan->nama_pelanggan} - {$pembayaran->bulan_bayar}\n";
        echo "   Meteran: {$tagihan->meteran_sebelum} → {$tagihan->meteran_sesudah}\n";
        echo "   Pemakaian: {$oldPemakaian} m³ → {$tagihan->pemakaian_kubik} m³\n";
        echo "   Abunemen: " . ($tagihan->ada_abunemen ? 'Ya' : 'Tidak') . "\n\n";
    } else {
        $skipped++;
        echo "⏭️  Skip: {$pembayaran->pelanggan->nama_pelanggan} - {$pembayaran->bulan_bayar} (no tagihan data or pemakaian = 0)\n";
    }
}

echo "\n=== Summary ===\n";
echo "✅ Synced: {$synced}\n";
echo "⏭️  Skipped: {$skipped}\n";
echo "\nSelesai!\n";
