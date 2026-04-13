<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\TagihanBulanan;

echo "=== Check Tarif Per Kubik ===\n\n";

// Cek tarif di tagihan HOIRUL
$tagihan = TagihanBulanan::whereHas('pelanggan', function ($q) {
    $q->where('nama_pelanggan', 'like', '%HOIRUL%');
})->where('bulan', '2026-01')->first();

if ($tagihan) {
    echo "📋 Data Tagihan HOIRUL Januari 2026:\n";
    echo "Meteran Sebelum: {$tagihan->meteran_sebelum}\n";
    echo "Meteran Sesudah: {$tagihan->meteran_sesudah}\n";
    echo "Pemakaian Kubik: {$tagihan->pemakaian_kubik}\n";
    echo "Tarif Per Kubik: Rp " . number_format($tagihan->tarif_per_kubik, 0, ',', '.') . "\n";
    echo "Biaya Abunemen: " . ($tagihan->biaya_abunemen ?? 0) . "\n";
    echo "Ada Abunemen: " . ($tagihan->ada_abunemen ? 'Ya' : 'Tidak') . "\n";
    echo "Total Tagihan: Rp " . number_format($tagihan->total_tagihan, 0, ',', '.') . "\n\n";
    
    // Hitung manual
    echo "📊 Perhitungan Manual:\n";
    $biayaPemakaian = $tagihan->pemakaian_kubik * $tagihan->tarif_per_kubik;
    $biayaAbunemen = $tagihan->ada_abunemen ? 3000 : 0;
    $totalManual = $biayaPemakaian + $biayaAbunemen;
    
    echo "Biaya Pemakaian: {$tagihan->pemakaian_kubik} m³ × Rp " . number_format($tagihan->tarif_per_kubik, 0, ',', '.') . " = Rp " . number_format($biayaPemakaian, 0, ',', '.') . "\n";
    echo "Biaya Abunemen: Rp " . number_format($biayaAbunemen, 0, ',', '.') . "\n";
    echo "Total Manual: Rp " . number_format($totalManual, 0, ',', '.') . "\n";
} else {
    echo "❌ Data tagihan tidak ditemukan\n";
}
