<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Pembayaran;
use App\Models\TagihanBulanan;
use Illuminate\Support\Facades\DB;

echo "=== Backfill Tagihan dari Pembayaran ===\n\n";

// Ambil semua pembayaran yang belum punya tagihan_bulanan
$pembayaranList = Pembayaran::all();

$created = 0;
$skipped = 0;
$errors = 0;

foreach ($pembayaranList as $pembayaran) {
    try {
        // Cek apakah sudah ada tagihan untuk pelanggan dan bulan ini
        $existingTagihan = TagihanBulanan::where('pelanggan_id', $pembayaran->pelanggan_id)
            ->where('bulan', $pembayaran->bulan_bayar)
            ->first();
        
        if ($existingTagihan) {
            $skipped++;
            echo "⏭️  Skip: {$pembayaran->pelanggan->nama_pelanggan} - {$pembayaran->bulan_bayar} (sudah ada)\n";
            continue;
        }
        
        // Tentukan status bayar berdasarkan keterangan
        $keterangan = strtoupper($pembayaran->keterangan ?? '');
        $statusBayar = 'SUDAH_BAYAR';
        
        if ($keterangan === 'NUNGGAK') {
            $statusBayar = 'BELUM_BAYAR';
        } elseif ($keterangan === 'CICILAN') {
            $statusBayar = 'BELUM_BAYAR';
        }
        
        // Buat tagihan baru
        TagihanBulanan::create([
            'pelanggan_id' => $pembayaran->pelanggan_id,
            'bulan' => $pembayaran->bulan_bayar,
            'meteran_sebelum' => $pembayaran->meteran_sebelum ?? 0,
            'meteran_sesudah' => $pembayaran->meteran_sesudah ?? 0,
            'pemakaian_kubik' => $pembayaran->jumlah_kubik ?? 0,
            'ada_abunemen' => $pembayaran->abunemen ?? false,
            'total_tagihan' => $pembayaran->jumlah_bayar,
            'status_bayar' => $statusBayar,
            'tanggal_bayar' => $pembayaran->tanggal_bayar,
        ]);
        
        $created++;
        echo "✅ Created: {$pembayaran->pelanggan->nama_pelanggan} - {$pembayaran->bulan_bayar} - Rp " . number_format($pembayaran->jumlah_bayar, 0, ',', '.') . " ({$statusBayar})\n";
        
    } catch (\Exception $e) {
        $errors++;
        echo "❌ Error: {$pembayaran->pelanggan->nama_pelanggan} - {$pembayaran->bulan_bayar} - {$e->getMessage()}\n";
    }
}

echo "\n=== Summary ===\n";
echo "✅ Created: {$created}\n";
echo "⏭️  Skipped: {$skipped}\n";
echo "❌ Errors: {$errors}\n";
echo "\nSelesai!\n";
