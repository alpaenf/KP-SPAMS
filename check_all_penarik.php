<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Pelanggan;

echo "=== DEBUG SEMUA USER PENARIK ===\n\n";

// Check all penarik users
$allPenarik = User::where('role', 'penarik')->get(['id', 'name', 'email', 'role', 'wilayah']);

echo "Total User Penarik: " . $allPenarik->count() . "\n\n";

foreach ($allPenarik as $user) {
    echo "ID: {$user->id}\n";
    echo "Name: {$user->name}\n";
    echo "Email: {$user->email}\n";
    echo "Wilayah: '" . ($user->wilayah ?? 'NULL') . "'\n";
    if ($user->wilayah) {
        echo "Wilayah Length: " . strlen($user->wilayah) . "\n";
        echo "Wilayah Bytes: " . bin2hex($user->wilayah) . "\n";
    }
    echo "---\n\n";
}

// Check pelanggan with various wilayah values  
echo "\n=== DISTINCT WILAYAH DI PELANGGAN ===\n\n";
$wilayahList = Pelanggan::selectRaw('wilayah, COUNT(*) as total')
    ->groupBy('wilayah')
    ->get();

foreach ($wilayahList as $item) {
    echo "Wilayah: '" . ($item->wilayah ?? 'NULL') . "' - Total: {$item->total}\n";
    if ($item->wilayah) {
        echo "   Bytes: " . bin2hex($item->wilayah) . "\n";
    }
}

echo "\n=== SELESAI ===\n";
