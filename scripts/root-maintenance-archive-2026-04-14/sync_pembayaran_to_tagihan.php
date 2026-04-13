<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Pelanggan;
use App\Models\Pembayaran;
use App\Models\TagihanBulanan;

$bulanIni = now()->format('Y-m');

echo "=== Cek Pembayaran vs Tagihan Bulanan ({$bulanIni}) ===\n\n";

// Ambil semua pembayaran bulan ini
$pembayaranList = Pembayaran::where('bulan_bayar', $bulanIni)
    ->with('pelanggan')
    ->get();

echo "Total pembayaran bulan ini: {$pembayaranList->count()}\n\n";

$belumAdaTagihan = [];

foreach ($pembayaranList as $pembayaran) {
    // Cek apakah ada tagihan_bulanan untuk pembayaran ini
    $tagihan = TagihanBulanan::where('pelanggan_id', $pembayaran->pelanggan_id)
        ->where('bulan', $bulanIni)
        ->first();
    
    if (!$tagihan) {
        $belumAdaTagihan[] = [
            'pelanggan_id' => $pembayaran->pelanggan->id,
            'id_pelanggan' => $pembayaran->pelanggan->id_pelanggan,
            'nama' => $pembayaran->pelanggan->nama_pelanggan,
            'jumlah_bayar' => $pembayaran->jumlah_bayar,
            'tanggal_bayar' => $pembayaran->tanggal_bayar->format('Y-m-d'),
        ];
        
        echo "✗ {$pembayaran->pelanggan->id_pelanggan} - {$pembayaran->pelanggan->nama_pelanggan}\n";
        echo "  Sudah bayar Rp " . number_format($pembayaran->jumlah_bayar, 0, ',', '.') . " tapi belum ada tagihan_bulanan\n\n";
    }
}

if (count($belumAdaTagihan) > 0) {
    echo "\n=== Temukan " . count($belumAdaTagihan) . " pembayaran tanpa tagihan_bulanan ===\n";
    echo "Membuat tagihan_bulanan otomatis dengan status SUDAH_BAYAR...\n\n";
    
    foreach ($belumAdaTagihan as $data) {
        // Buat tagihan_bulanan dengan data default
        $tagihan = TagihanBulanan::create([
            'pelanggan_id' => $data['pelanggan_id'],
            'bulan' => $bulanIni,
            'meteran_sebelum' => 0,
            'meteran_sesudah' => 0,
            'pemakaian_kubik' => 0,
            'tarif_per_kubik' => 2000,
            'ada_abunemen' => true,
            'biaya_abunemen' => 3000,
            'total_tagihan' => $data['jumlah_bayar'],
            'status_bayar' => 'SUDAH_BAYAR',
        ]);
        
        echo "✓ Buat tagihan untuk {$data['id_pelanggan']} - {$data['nama']}\n";
    }
    
    echo "\n=== Selesai ===\n";
    echo "Total tagihan dibuat: " . count($belumAdaTagihan) . "\n";
} else {
    echo "\n✓ Semua pembayaran sudah memiliki tagihan_bulanan\n";
}
