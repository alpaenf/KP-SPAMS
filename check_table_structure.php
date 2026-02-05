<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Struktur Tabel tagihan_bulanan ===\n\n";

$columns = DB::select("SHOW COLUMNS FROM tagihan_bulanan");

foreach ($columns as $column) {
    echo "Kolom: {$column->Field}\n";
    echo "  Type: {$column->Type}\n";
    echo "  Null: {$column->Null}\n";
    echo "  Default: " . ($column->Default ?? 'NULL') . "\n\n";
}

echo "\n=== Cek Kolom Konfirmasi ===\n";
$kolomKonfirmasi = ['bukti_transfer', 'konfirmasi_at', 'status_konfirmasi', 'catatan_admin'];

foreach ($kolomKonfirmasi as $kolom) {
    $exists = collect($columns)->firstWhere('Field', $kolom);
    if ($exists) {
        echo "✓ $kolom - SUDAH ADA\n";
    } else {
        echo "✗ $kolom - BELUM ADA\n";
    }
}
