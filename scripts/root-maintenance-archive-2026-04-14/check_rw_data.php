<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Cek Data RW di Database Pelanggan ===\n\n";

// Cek semua RW yang ada (termasuk NULL dan empty)
$allRW = \App\Models\Pelanggan::select('rw', \DB::raw('COUNT(*) as total'))
    ->groupBy('rw')
    ->orderBy('rw')
    ->get();

echo "Semua RW di database:\n";
foreach ($allRW as $item) {
    $rwValue = $item->rw ?: '[NULL/Empty]';
    echo "  RW: $rwValue - Total: {$item->total} pelanggan\n";
}
echo "\n";

// Cek yang tidak NULL dan tidak empty
$validRW = \App\Models\Pelanggan::select('rw')
    ->whereNotNull('rw')
    ->where('rw', '!=', '')
    ->distinct()
    ->orderBy('rw')
    ->pluck('rw')
    ->toArray();

echo "RW yang valid (not null dan not empty):\n";
if (empty($validRW)) {
    echo "  [KOSONG]\n";
} else {
    foreach ($validRW as $rw) {
        echo "  - RW $rw\n";
    }
}
echo "\nTotal RW valid: " . count($validRW) . "\n\n";

// Cek juga kolom wilayah
$wilayahData = \App\Models\Pelanggan::select('wilayah', \DB::raw('COUNT(*) as total'))
    ->groupBy('wilayah')
    ->orderBy('wilayah')
    ->get();

echo "Data kolom 'wilayah':\n";
foreach ($wilayahData as $item) {
    $wilayahValue = $item->wilayah ?: '[NULL/Empty]';
    echo "  Wilayah: $wilayahValue - Total: {$item->total} pelanggan\n";
}
