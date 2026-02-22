<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\TagihanBulanan;

echo "=== CEK INKONSISTENSI TARIF ===\n\n";

// Cek berapa tagihan dengan tarif 2500
$count2500 = TagihanBulanan::where('tarif_per_kubik', 2500)->count();
echo "📊 Tagihan dengan tarif Rp 2.500: {$count2500}\n";

// Cek berapa tagihan dengan tarif 2000
$count2000 = TagihanBulanan::where('tarif_per_kubik', 2000)->count();
echo "📊 Tagihan dengan tarif Rp 2.000: {$count2000}\n\n";

if ($count2500 > 0) {
    echo "🔍 Sample tagihan dengan tarif Rp 2.500:\n";
    $samples = TagihanBulanan::where('tarif_per_kubik', 2500)
        ->with('pelanggan')
        ->take(10)
        ->get();
    
    foreach ($samples as $t) {
        echo "  - {$t->pelanggan->id_pelanggan} ({$t->pelanggan->nama_pelanggan})\n";
        echo "    Bulan: {$t->bulan}\n";
        echo "    Tarif: Rp " . number_format($t->tarif_per_kubik, 0, ',', '.') . " / m³\n";
        echo "    Total: Rp " . number_format($t->total_tagihan, 0, ',', '.') . "\n\n";
    }
}

echo "\n=== SELESAI ===\n";
