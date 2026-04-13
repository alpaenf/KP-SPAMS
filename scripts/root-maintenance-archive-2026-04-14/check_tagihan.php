<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$aktif = \App\Models\Pelanggan::where('status_aktif', true)->count();
$totalTagihan = $aktif * 10000;

echo "Pelanggan aktif: " . $aktif . "\n";
echo "Total tagihan: Rp " . number_format($totalTagihan, 0, ',', '.') . "\n";
echo "Total tagihan (raw): " . $totalTagihan . "\n";
