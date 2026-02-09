<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Pembayaran;
use App\Models\TagihanBulanan;

echo "=== Checking HOIRUL Data ===\n\n";

// Cek pembayaran HOIRUL Januari 2026
$pembayaran = Pembayaran::whereHas('pelanggan', function ($q) {
    $q->where('nama_pelanggan', 'like', '%HOIRUL%');
})->where('bulan_bayar', '2026-01')->first();

if ($pembayaran) {
    echo "📊 Data Pembayaran:\n";
    echo "ID: {$pembayaran->id}\n";
    echo "Pelanggan: {$pembayaran->pelanggan->nama_pelanggan}\n";
    echo "Bulan: {$pembayaran->bulan_bayar}\n";
    echo "Meteran Sebelum: {$pembayaran->meteran_sebelum}\n";
    echo "Meteran Sesudah: {$pembayaran->meteran_sesudah}\n";
    echo "Jumlah Kubik: {$pembayaran->jumlah_kubik}\n";
    echo "Jumlah Bayar: Rp " . number_format($pembayaran->jumlah_bayar, 0, ',', '.') . "\n";
    echo "Abunemen: " . ($pembayaran->abunemen ? 'Ya' : 'Tidak') . "\n";
    echo "\n";
} else {
    echo "❌ Data pembayaran tidak ditemukan\n\n";
}

// Cek tagihan HOIRUL Januari 2026
$tagihan = TagihanBulanan::whereHas('pelanggan', function ($q) {
    $q->where('nama_pelanggan', 'like', '%HOIRUL%');
})->where('bulan', '2026-01')->first();

if ($tagihan) {
    echo "📋 Data Tagihan Bulanan:\n";
    echo "ID: {$tagihan->id}\n";
    echo "Pelanggan: {$tagihan->pelanggan->nama_pelanggan}\n";
    echo "Bulan: {$tagihan->bulan}\n";
    echo "Meteran Sebelum: {$tagihan->meteran_sebelum}\n";
    echo "Meteran Sesudah: {$tagihan->meteran_sesudah}\n";
    echo "Pemakaian Kubik: {$tagihan->pemakaian_kubik}\n";
    echo "Total Tagihan: Rp " . number_format($tagihan->total_tagihan, 0, ',', '.') . "\n";
    echo "Status Bayar: {$tagihan->status_bayar}\n";
    echo "\n";
} else {
    echo "❌ Data tagihan tidak ditemukan\n\n";
}
