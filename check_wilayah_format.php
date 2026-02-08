<?php
// Check pelanggan dan user wilayah format
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Pelanggan;
use App\Models\User;

echo "=== Check Format Wilayah ===\n\n";

// 1. Cek format wilayah di tabel users
echo "1. FORMAT WILAYAH DI TABEL USERS:\n";
$users = User::whereNotNull('wilayah')->get(['name', 'email', 'wilayah']);
foreach ($users as $u) {
    echo "   {$u->name} ({$u->email}) → wilayah: '{$u->wilayah}'\n";
}

echo "\n2. FORMAT WILAYAH DI TABEL PELANGGAN:\n";
$wilayahList = Pelanggan::select('wilayah')
    ->distinct()
    ->orderBy('wilayah')
    ->pluck('wilayah');
    
foreach ($wilayahList as $w) {
    $count = Pelanggan::where('wilayah', $w)->count();
    echo "   '{$w}' → {$count} pelanggan\n";
}

echo "\n3. CHECK PELANGGAN KUBANGSARI KULON (semua variasi):\n";
$variations = [
    'kubangsari_kulon',
    'Kubangsari Kulon',
    'Kubangsari_Kulon',
    'KUBANGSARI_KULON'
];

foreach ($variations as $v) {
    $count = Pelanggan::where('wilayah', $v)->count();
    if ($count > 0) {
        echo "   ✅ '{$v}' → {$count} pelanggan\n";
        $samples = Pelanggan::where('wilayah', $v)->take(3)->get(['id_pelanggan', 'nama_pelanggan', 'wilayah']);
        foreach ($samples as $s) {
            echo "      - {$s->id_pelanggan} | {$s->nama_pelanggan}\n";
        }
    } else {
        echo "   ❌ '{$v}' → tidak ada\n";
    }
}
