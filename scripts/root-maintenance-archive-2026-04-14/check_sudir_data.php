<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Pembayaran;
use App\Models\TagihanBulanan;

echo "=== Checking Sudir Data ===\n\n";

// Cek pembayaran Sudir Januari 2026
$pembayaran = Pembayaran::whereHas('pelanggan', function ($q) {
    $q->where('nama_pelanggan', 'like', '%Sudir%');
})->where('bulan_bayar', '2026-01')->first();

if ($pembayaran) {
    echo "📊 Data Pembayaran:\n";
    echo "ID: {$pembayaran->id}\n";
    echo "Pelanggan: {$pembayaran->pelanggan->nama_pelanggan} ({$pembayaran->pelanggan->id_pelanggan})\n";
    echo "Bulan: {$pembayaran->bulan_bayar}\n";
    echo "Tanggal Bayar: {$pembayaran->tanggal_bayar}\n";
    echo "Meteran Sebelum: {$pembayaran->meteran_sebelum}\n";
    echo "Meteran Sesudah: {$pembayaran->meteran_sesudah}\n";
    echo "Jumlah Kubik: {$pembayaran->jumlah_kubik}\n";
    echo "Jumlah Bayar: Rp " . number_format($pembayaran->jumlah_bayar, 0, ',', '.') . "\n";
    echo "Abunemen: " . ($pembayaran->abunemen ? 'Ya' : 'Tidak') . "\n";
    echo "Tunggakan: Rp " . number_format($pembayaran->tunggakan ?? 0, 0, ',', '.') . "\n";
    echo "Keterangan: " . ($pembayaran->keterangan ?? '-') . "\n";
    echo "\n";
} else {
    echo "❌ Data pembayaran tidak ditemukan\n\n";
}

// Cek tagihan Sudir Januari 2026
$tagihan = TagihanBulanan::whereHas('pelanggan', function ($q) {
    $q->where('nama_pelanggan', 'like', '%Sudir%');
})->where('bulan', '2026-01')->first();

if ($tagihan) {
    echo "📋 Data Tagihan Bulanan:\n";
    echo "ID: {$tagihan->id}\n";
    echo "Pelanggan: {$tagihan->pelanggan->nama_pelanggan} ({$tagihan->pelanggan->id_pelanggan})\n";
    echo "Bulan: {$tagihan->bulan}\n";
    echo "Meteran Sebelum: {$tagihan->meteran_sebelum}\n";
    echo "Meteran Sesudah: {$tagihan->meteran_sesudah}\n";
    echo "Pemakaian Kubik: {$tagihan->pemakaian_kubik}\n";
    echo "Tarif Per Kubik: Rp " . number_format($tagihan->tarif_per_kubik, 0, ',', '.') . "\n";
    echo "Ada Abunemen: " . ($tagihan->ada_abunemen ? 'Ya' : 'Tidak') . "\n";
    echo "Biaya Abunemen: Rp " . number_format($tagihan->biaya_abunemen ?? 0, 0, ',', '.') . "\n";
    echo "Total Tagihan: Rp " . number_format($tagihan->total_tagihan, 0, ',', '.') . "\n";
    echo "Status Bayar: {$tagihan->status_bayar}\n";
    echo "\n";
} else {
    echo "❌ Data tagihan tidak ditemukan\n\n";
}

echo "💡 Analisis:\n";
if ($pembayaran && $pembayaran->jumlah_kubik == 0 && $pembayaran->jumlah_bayar == 35000) {
    echo "Kemungkinan ini adalah:\n";
    echo "1. Pembayaran abunemen + tunggakan (Rp 3.000 abunemen + Rp 32.000 tunggakan?)\n";
    echo "2. Pembayaran denda atau biaya khusus\n";
    echo "3. Error input data\n";
}
