<?php

/**
 * Script untuk update tarif_per_kubik dari 2500 ke 2000
 * Jalankan dengan: php artisan tinker < update_tarif_2000.php
 */

use App\Models\TagihanBulanan;

echo "=== Update Tarif Per Kubik dari 2500 ke 2000 ===\n\n";

// Cek berapa banyak tagihan yang tarif_per_kubik nya 2500
$count = TagihanBulanan::where('tarif_per_kubik', 2500)->count();

if ($count === 0) {
    echo "✅ Tidak ada tagihan dengan tarif 2500. Semua sudah benar!\n";
    exit(0);
}

echo "📊 Ditemukan {$count} tagihan dengan tarif Rp 2.500\n";
echo "🔄 Mengupdate ke Rp 2.000...\n\n";

// Update semua tagihan yang tarif_per_kubik nya 2500
$updated = TagihanBulanan::where('tarif_per_kubik', 2500)
    ->update([
        'tarif_per_kubik' => 2000
    ]);

echo "✅ Berhasil update {$updated} tagihan!\n";
echo "✅ Semua tarif sekarang sudah Rp 2.000 per m³\n";
