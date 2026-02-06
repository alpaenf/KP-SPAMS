<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== PERBAIKI DATA FEBRUARI DWH0001 ===\n\n";

// Update tagihan Februari dengan meteran_sebelum yang benar dari Januari
$updated = DB::table('tagihan_bulanan')
    ->where('pelanggan_id', 9)
    ->where('bulan', '2026-02')
    ->update([
        'meteran_sebelum' => 88.00,
        'meteran_sesudah' => null,
        'pemakaian_kubik' => 0,
        'total_tagihan' => 0,
        'status_bayar' => 'BELUM_BAYAR'
    ]);

if ($updated) {
    echo "✓ Data Februari diperbaiki:\n";
    echo "  meteran_sebelum: 23.00 → 88.00 (dari meteran_sesudah Januari)\n";
    echo "  meteran_sesudah: 57.00 → NULL (reset untuk input ulang)\n";
    echo "  status: SUDAH_BAYAR → BELUM_BAYAR\n\n";
    
    // Verifikasi
    $tagihan = DB::table('tagihan_bulanan')
        ->where('pelanggan_id', 9)
        ->where('bulan', '2026-02')
        ->first();
    
    echo "Data Februari setelah perbaikan:\n";
    echo "  Meteran Sebelum: {$tagihan->meteran_sebelum}\n";
    echo "  Meteran Sesudah: " . ($tagihan->meteran_sesudah ?? 'NULL') . "\n";
    echo "  Status: {$tagihan->status_bayar}\n";
} else {
    echo "❌ Gagal update\n";
}
