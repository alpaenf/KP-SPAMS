<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Pelanggan;
use App\Models\Pembayaran;
use App\Models\TagihanBulanan;

$bulanIni = now()->format('Y-m');
$pelanggan = Pelanggan::where('id_pelanggan', 'DWH0001')->first();

if (!$pelanggan) {
    echo "Pelanggan DWH0001 tidak ditemukan\n";
    exit;
}

echo "=== Data Pelanggan DWH0001 ===\n";
echo "ID Internal: {$pelanggan->id}\n";
echo "Nama: {$pelanggan->nama_pelanggan}\n";
echo "Kategori: {$pelanggan->kategori}\n\n";

echo "=== Pembayaran Bulan {$bulanIni} ===\n";
$pembayaran = Pembayaran::where('pelanggan_id', $pelanggan->id)
    ->where('bulan_bayar', $bulanIni)
    ->first();

if ($pembayaran) {
    echo "✓ Ada pembayaran:\n";
    echo "  - Tanggal: {$pembayaran->tanggal_bayar}\n";
    echo "  - Jumlah: Rp " . number_format($pembayaran->jumlah_bayar, 0, ',', '.') . "\n";
} else {
    echo "✗ Belum ada pembayaran\n";
}

echo "\n=== Tagihan Bulanan {$bulanIni} ===\n";
$tagihan = TagihanBulanan::where('pelanggan_id', $pelanggan->id)
    ->where('bulan', $bulanIni)
    ->first();

if ($tagihan) {
    echo "✓ Ada tagihan:\n";
    echo "  - Status: {$tagihan->status_bayar}\n";
    echo "  - Total Tagihan: Rp " . number_format($tagihan->total_tagihan, 0, ',', '.') . "\n";
    echo "  - Meteran Sebelum: {$tagihan->meteran_sebelum}\n";
    echo "  - Meteran Sesudah: {$tagihan->meteran_sesudah}\n";
    echo "  - Pemakaian: {$tagihan->pemakaian_kubik} m³\n";
} else {
    echo "✗ Belum ada tagihan bulanan yang di-generate\n";
}
