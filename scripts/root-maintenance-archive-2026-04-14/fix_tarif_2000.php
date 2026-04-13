<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\TagihanBulanan;

echo "=== Update Tarif Per Kubik dari 2500 ke 2000 ===\n\n";

// Ambil semua tagihan yang tarif_per_kubik nya 2500
$tagihanList = TagihanBulanan::where('tarif_per_kubik', 2500)->get();

echo "Found {$tagihanList->count()} tagihan with tarif Rp 2.500\n\n";

$updated = 0;

foreach ($tagihanList as $tagihan) {
    $oldTarif = $tagihan->tarif_per_kubik;
    $oldTotal = $tagihan->total_tagihan;
    
    // Update tarif
    $tagihan->tarif_per_kubik = 2000;
    
    // Recalculate total tagihan
    $biayaPemakaian = $tagihan->pemakaian_kubik * 2000;
    $biayaAbunemen = $tagihan->ada_abunemen ? ($tagihan->biaya_abunemen ?? 3000) : 0;
    $tagihan->total_tagihan = $biayaPemakaian + $biayaAbunemen;
    
    $tagihan->save();
    
    $updated++;
    echo "✅ Updated: {$tagihan->pelanggan->nama_pelanggan} - {$tagihan->bulan}\n";
    echo "   Tarif: Rp " . number_format($oldTarif, 0, ',', '.') . " → Rp 2.000\n";
    echo "   Total: Rp " . number_format($oldTotal, 0, ',', '.') . " → Rp " . number_format($tagihan->total_tagihan, 0, ',', '.') . "\n\n";
}

echo "=== Summary ===\n";
echo "✅ Updated: {$updated} tagihan\n";
echo "\nSelesai!\n";
