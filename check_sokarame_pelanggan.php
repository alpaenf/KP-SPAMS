<?php
// Check pelanggan Sokarame
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Pelanggan;

echo "=== Check Pelanggan Sokarame ===\n\n";

$pelangganSokarame = Pelanggan::where('wilayah', 'sokarame')
    ->orWhere('wilayah', 'Sokarame')
    ->get(['id', 'id_pelanggan', 'nama_pelanggan', 'wilayah', 'status_aktif']);

echo "Jumlah pelanggan Sokarame: " . $pelangganSokarame->count() . "\n\n";

if ($pelangganSokarame->count() > 0) {
    echo "Data pelanggan:\n";
    foreach ($pelangganSokarame->take(5) as $p) {
        echo "- ID: {$p->id_pelanggan} | {$p->nama_pelanggan} | Wilayah: {$p->wilayah} | Status: " . ($p->status_aktif ? 'Aktif' : 'Nonaktif') . "\n";
    }
} else {
    echo "❌ TIDAK ADA pelanggan dengan wilayah Sokarame!\n";
    echo "\nCek wilayah yang ada:\n";
    $allWilayah = Pelanggan::select('wilayah')
        ->distinct()
        ->pluck('wilayah');
    foreach ($allWilayah as $w) {
        $count = Pelanggan::where('wilayah', $w)->count();
        echo "- {$w}: {$count} pelanggan\n";
    }
}
