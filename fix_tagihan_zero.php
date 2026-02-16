<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\TagihanBulanan;

echo "🔧 Memperbaiki tagihan dengan total_tagihan = 0...\n\n";

// Ambil semua tagihan yang total_tagihan = 0 tapi sudah ada meteran
$tagihanBermasalah = TagihanBulanan::where('total_tagihan', 0)
    ->whereNotNull('meteran_sebelum')
    ->whereNotNull('meteran_sesudah')
    ->where('meteran_sesudah', '>', 'meteran_sebelum')
    ->with('pelanggan')
    ->get();

echo "📊 Ditemukan {$tagihanBermasalah->count()} tagihan yang perlu diperbaiki\n\n";

$fixed = 0;

foreach ($tagihanBermasalah as $tagihan) {
    $oldTotal = $tagihan->total_tagihan;
    
    // Set default values jika NULL
    if ($tagihan->tarif_per_kubik === null || $tagihan->tarif_per_kubik == 0) {
        $tagihan->tarif_per_kubik = 1500;
    }
    if ($tagihan->ada_abunemen === null) {
        $tagihan->ada_abunemen = true;
    }
    if ($tagihan->biaya_abunemen === null || $tagihan->biaya_abunemen == 0) {
        $tagihan->biaya_abunemen = 3000;
    }
    
    // Skip jika kategori sosial
    if ($tagihan->pelanggan->kategori === 'sosial') {
        echo "⏭️  Skip: {$tagihan->pelanggan->nama_pelanggan} - {$tagihan->bulan} (kategori sosial)\n";
        continue;
    }
    
    // Hitung ulang tagihan
    $tagihan->hitungTagihan();
    $tagihan->save();
    
    $newTotal = $tagihan->total_tagihan;
    
    echo "✅ {$tagihan->pelanggan->id_pelanggan} - {$tagihan->pelanggan->nama_pelanggan} ({$tagihan->bulan})\n";
    echo "   Meteran: {$tagihan->meteran_sebelum} → {$tagihan->meteran_sesudah} = {$tagihan->pemakaian_kubik} m³\n";
    echo "   Tarif: Rp " . number_format($tagihan->tarif_per_kubik, 0, ',', '.') . "/m³\n";
    echo "   Abunemen: " . ($tagihan->ada_abunemen ? "Ya (Rp " . number_format($tagihan->biaya_abunemen, 0, ',', '.') . ")" : "Tidak") . "\n";
    echo "   Total: Rp " . number_format($oldTotal, 0, ',', '.') . " → Rp " . number_format($newTotal, 0, ',', '.') . "\n\n";
    
    $fixed++;
}

echo "\n✨ Selesai! {$fixed} tagihan berhasil diperbaiki.\n";
