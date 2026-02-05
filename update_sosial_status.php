<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

// Update semua tagihan kategori sosial
$updated = DB::table('tagihan_bulanan')
    ->join('pelanggan', 'tagihan_bulanan.pelanggan_id', '=', 'pelanggan.id')
    ->where('pelanggan.kategori', 'sosial')
    ->where('tagihan_bulanan.status_bayar', 'BELUM_BAYAR')
    ->update([
        'tagihan_bulanan.status_bayar' => 'SUDAH_BAYAR',
        'tagihan_bulanan.total_tagihan' => 0
    ]);

echo "✅ Berhasil update $updated tagihan kategori sosial menjadi SUDAH_BAYAR\n";

// Tampilkan jumlah total tagihan sosial
$total = DB::table('tagihan_bulanan')
    ->join('pelanggan', 'tagihan_bulanan.pelanggan_id', '=', 'pelanggan.id')
    ->where('pelanggan.kategori', 'sosial')
    ->count();

echo "📊 Total tagihan kategori sosial: $total\n";
