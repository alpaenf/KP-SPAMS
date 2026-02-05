<?php

/**
 * Script untuk sinkronisasi status_bayar di tagihan_bulanan
 * berdasarkan data pembayaran yang sudah ada di tabel pembayarans
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\TagihanBulanan;
use App\Models\Pembayaran;

echo "=== Sinkronisasi Status Bayar ===\n\n";

// Ambil semua tagihan yang statusnya BELUM_BAYAR
$tagihanList = TagihanBulanan::where('status_bayar', 'BELUM_BAYAR')->get();

$jumlahDiupdate = 0;

foreach ($tagihanList as $tagihan) {
    // Cek apakah ada pembayaran untuk pelanggan dan bulan ini
    $pembayaran = Pembayaran::where('pelanggan_id', $tagihan->pelanggan_id)
        ->where('bulan_bayar', $tagihan->bulan)
        ->first();
    
    if ($pembayaran) {
        // Update status menjadi SUDAH_BAYAR
        $tagihan->status_bayar = 'SUDAH_BAYAR';
        $tagihan->save();
        
        $jumlahDiupdate++;
        echo "✓ Update status untuk Pelanggan ID {$tagihan->pelanggan->id_pelanggan} - {$tagihan->pelanggan->nama_pelanggan} bulan {$tagihan->bulan}\n";
    }
}

echo "\n=== Selesai ===\n";
echo "Total tagihan diupdate: {$jumlahDiupdate}\n";
