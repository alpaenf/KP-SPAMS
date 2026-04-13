<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Pelanggan;

echo "=== DEBUG WILAYAH DAWUHAN ===\n\n";

// Check user penarik dawuhan
echo "1. CEK USER PENARIK DAWUHAN:\n";
$penarikDawuhan = User::where('role', 'penarik')
    ->where('wilayah', 'like', '%dawuhan%')
    ->get(['id', 'name', 'email', 'role', 'wilayah']);

foreach ($penarikDawuhan as $user) {
    echo "   ID: {$user->id}\n";
    echo "   Name: {$user->name}\n";
    echo "   Email: {$user->email}\n";
    echo "   Wilayah: '" . $user->wilayah . "'\n";
    echo "   Wilayah Length: " . strlen($user->wilayah) . "\n";
    echo "   Wilayah Bytes: " . bin2hex($user->wilayah) . "\n";
    echo "   isPenarik(): " . ($user->isPenarik() ? 'true' : 'false') . "\n";
    echo "   hasWilayah(): " . ($user->hasWilayah() ? 'true' : 'false') . "\n";
    echo "   getWilayah(): '" . $user->getWilayah() . "'\n";
    echo "\n";
}

// Check pelanggan dawuhan (sample)
echo "2. CEK PELANGGAN DAWUHAN (Sample 3):\n";
$pelangganDawuhan = Pelanggan::where('wilayah', 'like', '%dawuhan%')
    ->limit(3)
    ->get(['id', 'id_pelanggan', 'nama_pelanggan', 'wilayah']);

foreach ($pelangganDawuhan as $pelanggan) {
    echo "   ID: {$pelanggan->id}\n";
    echo "   ID Pelanggan: {$pelanggan->id_pelanggan}\n";
    echo "   Nama: {$pelanggan->nama_pelanggan}\n";
    echo "   Wilayah: '" . $pelanggan->wilayah . "'\n";
    echo "   Wilayah Length: " . strlen($pelanggan->wilayah) . "\n";
    echo "   Wilayah Bytes: " . bin2hex($pelanggan->wilayah) . "\n";
    echo "\n";
}

// Check comparison
echo "3. CEK PERBANDINGAN:\n";
if ($penarikDawuhan->count() > 0 && $pelangganDawuhan->count() > 0) {
    $user = $penarikDawuhan->first();
    $pelanggan = $pelangganDawuhan->first();
    
    echo "   User Wilayah: '" . $user->getWilayah() . "'\n";
    echo "   Pelanggan Wilayah: '" . $pelanggan->wilayah . "'\n";
    echo "   Comparison (===): " . ($pelanggan->wilayah === $user->getWilayah() ? 'TRUE' : 'FALSE') . "\n";
    echo "   Comparison (==): " . ($pelanggan->wilayah == $user->getWilayah() ? 'TRUE' : 'FALSE') . "\n";
    echo "   Comparison (!==): " . ($pelanggan->wilayah !== $user->getWilayah() ? 'TRUE (BLOCKED!)' : 'FALSE (ALLOWED)') . "\n";
}

// Check all variants of dawuhan
echo "\n4. CEK SEMUA VARIANT 'DAWUHAN':\n";
$allWilayahUsers = User::whereNotNull('wilayah')
    ->distinct()
    ->pluck('wilayah');
    
echo "   User wilayah values:\n";
foreach ($allWilayahUsers as $w) {
    echo "      - '" . $w . "' (bytes: " . bin2hex($w) . ")\n";
}

$allWilayahPelanggan = Pelanggan::whereNotNull('wilayah')
    ->distinct()
    ->pluck('wilayah');
    
echo "\n   Pelanggan wilayah values:\n";
foreach ($allWilayahPelanggan as $w) {
    echo "      - '" . $w . "' (bytes: " . bin2hex($w) . ")\n";
}

echo "\n=== SELESAI ===\n";
