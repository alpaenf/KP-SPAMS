<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Pembayaran;
use App\Models\TagihanBulanan;

echo "=== Fix Total Tagihan from Pembayaran ===\n\n";

// Ambil semua tagihan yang total_tagihan nya 0 atau null
$tagihanList = TagihanBulanan::where(function ($q) {
    $q->whereNull('total_tagihan')
      ->orWhere('total_tagihan', 0);
})->get();

echo "Found {$tagihanList->count()} tagihan with 0 or null total_tagihan\n\n";

$fixed = 0;
$skipped = 0;

foreach ($tagihanList as $tagihan) {
    // Cari pembayaran yang sesuai
    $pembayaran = Pembayaran::where('pelanggan_id', $tagihan->pelanggan_id)
        ->where('bulan_bayar', $tagihan->bulan)
        ->first();
    
    if ($pembayaran && $pembayaran->jumlah_bayar > 0) {
        $oldTotal = $tagihan->total_tagihan;
        $tagihan->total_tagihan = $pembayaran->jumlah_bayar;
        $tagihan->save();
        
        $fixed++;
        echo "✅ Fixed: {$tagihan->pelanggan->nama_pelanggan} - {$tagihan->bulan} - ";
        echo "Rp " . number_format($oldTotal, 0, ',', '.') . " → Rp " . number_format($pembayaran->jumlah_bayar, 0, ',', '.') . "\n";
    } else {
        $skipped++;
        echo "⏭️  Skip: {$tagihan->pelanggan->nama_pelanggan} - {$tagihan->bulan} (no payment data or jumlah_bayar = 0)\n";
    }
}

echo "\n=== Summary ===\n";
echo "✅ Fixed: {$fixed}\n";
echo "⏭️  Skipped: {$skipped}\n";
echo "\nSelesai!\n";
