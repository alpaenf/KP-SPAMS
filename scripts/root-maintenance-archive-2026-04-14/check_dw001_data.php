<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== CEK DATA DW001/DWH0001 ===\n\n";

// Cek pelanggan
$pelanggan = DB::table('pelanggan')
    ->where('id_pelanggan', 'LIKE', 'DW%0001')
    ->orWhere('id_pelanggan', 'LIKE', 'DWH%0001')
    ->first();

if (!$pelanggan) {
    echo "❌ Pelanggan tidak ditemukan\n";
    exit;
}

echo "✓ Pelanggan ditemukan:\n";
echo "  ID: {$pelanggan->id}\n";
echo "  ID Pelanggan: {$pelanggan->id_pelanggan}\n";
echo "  Nama: {$pelanggan->nama_pelanggan}\n\n";

// Cek pembayaran Januari 2026
echo "=== PEMBAYARAN JANUARI 2026 ===\n";
$pembayaranJan = DB::table('pembayarans')
    ->where('pelanggan_id', $pelanggan->id)
    ->where('bulan_bayar', '2026-01')
    ->first();

if ($pembayaranJan) {
    echo "✓ Ada pembayaran Januari:\n";
    echo "  Tanggal Bayar: {$pembayaranJan->tanggal_bayar}\n";
    echo "  Meteran Sebelum: {$pembayaranJan->meteran_sebelum}\n";
    echo "  Meteran Sesudah: {$pembayaranJan->meteran_sesudah}\n";
    echo "  Jumlah Bayar: " . number_format($pembayaranJan->jumlah_bayar, 0, ',', '.') . "\n\n";
} else {
    echo "❌ Tidak ada pembayaran Januari\n\n";
}

// Cek tagihan Januari 2026
echo "=== TAGIHAN JANUARI 2026 ===\n";
$tagihanJan = DB::table('tagihan_bulanan')
    ->where('pelanggan_id', $pelanggan->id)
    ->where('bulan', '2026-01')
    ->first();

if ($tagihanJan) {
    echo "✓ Ada tagihan Januari:\n";
    echo "  Meteran Sebelum: {$tagihanJan->meteran_sebelum}\n";
    echo "  Meteran Sesudah: {$tagihanJan->meteran_sesudah}\n";
    echo "  Pemakaian: {$tagihanJan->pemakaian_kubik} m³\n";
    echo "  Total Tagihan: " . number_format($tagihanJan->total_tagihan, 0, ',', '.') . "\n";
    echo "  Status: {$tagihanJan->status_bayar}\n\n";
} else {
    echo "❌ Tidak ada tagihan Januari\n\n";
}

// Cek tagihan Februari 2026
echo "=== TAGIHAN FEBRUARI 2026 ===\n";
$tagihanFeb = DB::table('tagihan_bulanan')
    ->where('pelanggan_id', $pelanggan->id)
    ->where('bulan', '2026-02')
    ->first();

if ($tagihanFeb) {
    echo "✓ Ada tagihan Februari:\n";
    echo "  Meteran Sebelum: {$tagihanFeb->meteran_sebelum}\n";
    echo "  Meteran Sesudah: " . ($tagihanFeb->meteran_sesudah ?? 'NULL') . "\n";
    echo "  Status: {$tagihanFeb->status_bayar}\n\n";
} else {
    echo "❌ Tidak ada tagihan Februari\n\n";
}
