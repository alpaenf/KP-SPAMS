<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\TagihanBulanan;
use App\Models\Pembayaran;

$bulanIni = now()->format('Y-m');

echo "=== Cek Tagihan BELUM_BAYAR yang Sebenarnya Sudah Ada Pembayaran ({$bulanIni}) ===\n\n";

// Ambil semua tagihan dengan status BELUM_BAYAR
$tagihanList = TagihanBulanan::where('bulan', $bulanIni)
    ->where('status_bayar', 'BELUM_BAYAR')
    ->with('pelanggan')
    ->get();

echo "Total tagihan status BELUM_BAYAR: {$tagihanList->count()}\n\n";

$perluDiupdate = 0;

foreach ($tagihanList as $tagihan) {
    // Cek apakah ada pembayaran
    $pembayaran = Pembayaran::where('pelanggan_id', $tagihan->pelanggan_id)
        ->where('bulan_bayar', $bulanIni)
        ->first();
    
    if ($pembayaran) {
        echo "✗ {$tagihan->pelanggan->id_pelanggan} - {$tagihan->pelanggan->nama_pelanggan}\n";
        echo "  Status: BELUM_BAYAR tapi ada pembayaran Rp " . number_format($pembayaran->jumlah_bayar, 0, ',', '.') . "\n";
        echo "  Updating status ke SUDAH_BAYAR...\n\n";
        
        $tagihan->status_bayar = 'SUDAH_BAYAR';
        $tagihan->save();
        
        $perluDiupdate++;
    }
}

echo "\n=== Selesai ===\n";
echo "Total tagihan diupdate: {$perluDiupdate}\n";

if ($perluDiupdate == 0) {
    echo "✓ Semua data sudah konsisten!\n";
}
