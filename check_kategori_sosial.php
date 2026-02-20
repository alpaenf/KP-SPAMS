<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Pelanggan;
use App\Models\Pembayaran;

echo "=== Checking Kategori Sosial ===\n\n";

// 1. Check total pelanggan dengan kategori sosial (EXACT match)
$pelangganSosialExact = Pelanggan::where('kategori', 'sosial')->get();
echo "Total pelanggan dengan kategori='sosial' (exact): " . $pelangganSosialExact->count() . "\n";

// 2. Check dengan case-insensitive
$pelangganSosialCaseInsensitive = Pelanggan::whereRaw("LOWER(TRIM(kategori)) = 'sosial'")->get();
echo "Total pelanggan dengan kategori='sosial' (case-insensitive + trim): " . $pelangganSosialCaseInsensitive->count() . "\n";

if ($pelangganSosialCaseInsensitive->count() > 0) {
    echo "\nContoh 5 pelanggan sosial:\n";
    foreach ($pelangganSosialCaseInsensitive->take(5) as $p) {
        echo "- {$p->id_pelanggan}: {$p->nama_pelanggan} (kategori: '{$p->kategori}' | length: " . strlen($p->kategori ?? '') . ")\n";
    }
}

// 3. Check semua nilai unik di kolom kategori
echo "\n\n=== Nilai unik di kolom kategori ===\n";
$kategoriList = Pelanggan::select('kategori')
    ->distinct()
    ->pluck('kategori');

foreach ($kategoriList as $kat) {
    $count = Pelanggan::where('kategori', $kat)->count();
    $normalized = strtolower(trim($kat ?? ''));
    echo "Raw: '{$kat}' (length: " . strlen($kat ?? '') . ") | Normalized: '{$normalized}' | Count: {$count} pelanggan\n";
}

// 4. Check pembayaran bulan ini dari pelanggan sosial
$bulanIni = date('Y-m');
echo "\n\n=== Pembayaran bulan {$bulanIni} ===\n";

$pembayaranBulanIni = Pembayaran::where('bulan_bayar', $bulanIni)
    ->with('pelanggan')
    ->get();

echo "Total pembayaran bulan ini: " . $pembayaranBulanIni->count() . "\n";

// Old way (case-sensitive)
$pembayaranSosialOld = $pembayaranBulanIni->filter(function($p) {
    return $p->pelanggan && ($p->pelanggan->kategori ?? 'umum') === 'sosial';
});
echo "Pembayaran dari kategori sosial (old way - case-sensitive): " . $pembayaranSosialOld->count() . "\n";

// New way (case-insensitive + trim)
$pembayaranSosialNew = $pembayaranBulanIni->filter(function($p) {
    $kategori = strtolower(trim($p->pelanggan->kategori ?? 'umum'));
    return $kategori === 'sosial';
});
echo "Pembayaran dari kategori sosial (new way - case-insensitive + trim): " . $pembayaranSosialNew->count() . "\n";

if ($pembayaranSosialNew->count() > 0) {
    echo "\nContoh pembayaran sosial:\n";
    foreach ($pembayaranSosialNew->take(3) as $p) {
        echo "- {$p->pelanggan->id_pelanggan}: {$p->pelanggan->nama_pelanggan} (kategori: '{$p->pelanggan->kategori}') - Rp " . number_format($p->jumlah_bayar) . "\n";
    }
}

// Check umum juga
$pembayaranUmum = $pembayaranBulanIni->filter(function($p) {
    $kategori = strtolower(trim($p->pelanggan->kategori ?? 'umum'));
    return $kategori === 'umum';
});
echo "\nPembayaran dari kategori umum: " . $pembayaranUmum->count() . "\n";

// 5. Check unique pelanggan_id
$uniqueSosial = $pembayaranSosialNew->unique('pelanggan_id')->count();
$uniqueUmum = $pembayaranUmum->unique('pelanggan_id')->count();

echo "\n=== Unique pelanggan yang bayar (bangunan) ===\n";
echo "Unique pelanggan sosial: {$uniqueSosial} bangunan\n";
echo "Unique pelanggan umum: {$uniqueUmum} bangunan\n";
echo "Total unique: " . ($uniqueSosial + $uniqueUmum) . " bangunan\n";

echo "\n=== DONE ===\n";
