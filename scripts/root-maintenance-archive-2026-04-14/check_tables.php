<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== DAFTAR TABEL ===\n";
$tables = DB::select("SHOW TABLES");
foreach ($tables as $t) {
    echo "  " . implode("", array_values((array)$t)) . "\n";
}

// Cek tabel yang berkaitan
echo "\n=== CEK TABEL PELANGGAN ===\n";
$hasPelanggan = DB::select("SHOW TABLES LIKE '%pelanggan%'");
foreach ($hasPelanggan as $t) { echo "  " . implode("", array_values((array)$t)) . "\n"; }

echo "\n=== CEK TABEL PEMBAYARAN ===\n";
$hasPembayaran = DB::select("SHOW TABLES LIKE '%pembayaran%'");
foreach ($hasPembayaran as $t) { echo "  " . implode("", array_values((array)$t)) . "\n"; }

echo "\n=== CEK TABEL TAGIHAN ===\n";
$hasTagihan = DB::select("SHOW TABLES LIKE '%tagihan%'");
foreach ($hasTagihan as $t) { echo "  " . implode("", array_values((array)$t)) . "\n"; }
