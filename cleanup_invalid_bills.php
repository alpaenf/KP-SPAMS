<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Pelanggan;
use App\Models\TagihanBulanan;

echo "Mencari tagihan yang tidak valid untuk pelanggan baru...\n\n";

$pelangganList = Pelanggan::all();
$totalDeleted = 0;

foreach ($pelangganList as $pelanggan) {
    $bulanDaftar = $pelanggan->created_at->format('Y-m');
    
    // Cari tagihan yang dibuat SEBELUM atau SAMA DENGAN bulan pendaftaran
    $invalidBills = TagihanBulanan::where('pelanggan_id', $pelanggan->id)
        ->where('bulan', '<=', $bulanDaftar)
        ->get();
    
    if ($invalidBills->count() > 0) {
        echo "Pelanggan: {$pelanggan->nama_pelanggan} (ID: {$pelanggan->id_pelanggan})\n";
        echo "Terdaftar: {$pelanggan->created_at->format('Y-m-d')}\n";
        echo "Bulan Daftar: {$bulanDaftar}\n";
        echo "Tagihan invalid yang akan dihapus:\n";
        
        foreach ($invalidBills as $bill) {
            echo "  - Bulan {$bill->bulan} (Total: Rp {$bill->total_tagihan})\n";
            $bill->delete();
            $totalDeleted++;
        }
        
        echo "\n";
    }
}

echo "\nSelesai! Total {$totalDeleted} tagihan invalid telah dihapus.\n";
