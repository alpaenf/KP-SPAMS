<?php
// Check pelanggan dengan kolom yang benar
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Pelanggan;
use Illuminate\Support\Facades\DB;

echo "=== CHECK PELANGGAN (CORRECTED) ===\n\n";

// 1. Total pelanggan
$total = Pelanggan::count();
echo "1. TOTAL PELANGGAN: {$total}\n\n";

// 2. Pelanggan aktif (status_aktif = 1 atau true)
echo "2. PELANGGAN AKTIF:\n";
$aktif = Pelanggan::where('status_aktif', 1)->count();
$nonAktif = Pelanggan::where('status_aktif', 0)->orWhereNull('status_aktif')->count();
echo "   Aktif: {$aktif}\n";
echo "   Non-aktif: {$nonAktif}\n";

// 3. Breakdown aktif per wilayah
echo "\n3. PELANGGAN AKTIF PER WILAYAH:\n";
$aktifByWilayah = Pelanggan::where('status_aktif', 1)
    ->select('wilayah', DB::raw('count(*) as total'))
    ->groupBy('wilayah')
    ->orderBy('total', 'desc')
    ->get();

foreach ($aktifByWilayah as $a) {
    $wilayah = $a->wilayah ?? '<NULL>';
    echo "   '{$wilayah}' → {$a->total} aktif\n";
}

// 4. Sample pelanggan per wilayah
echo "\n4. SAMPLE PELANGGAN PER WILAYAH:\n";
$wilayahList = ['dawuhan', 'kubangsari_kulon', 'kubangsari_wetan', 'sokarame', 'tiparjaya'];

foreach ($wilayahList as $w) {
    $count = Pelanggan::where('wilayah', $w)->count();
    echo "   {$w}: {$count} pelanggan\n";
    
    if ($count > 0) {
        $samples = Pelanggan::where('wilayah', $w)
            ->take(3)
            ->get(['id_pelanggan', 'nama_pelanggan', 'status_aktif']);
        
        foreach ($samples as $s) {
            $status = $s->status_aktif ? 'Aktif' : 'Non-aktif';
            echo "      - {$s->id_pelanggan} | {$s->nama_pelanggan} | {$status}\n";
        }
    }
    echo "\n";
}

// 5. Pelanggan dengan koordinat (ada di peta)
echo "5. PELANGGAN DENGAN KOORDINAT:\n";
$withCoords = Pelanggan::whereNotNull('latitude')
    ->whereNotNull('longitude')
    ->where('latitude', '!=', 0)
    ->where('longitude', '!=', 0)
    ->count();
echo "   Total dengan koordinat: {$withCoords}\n";

$coordsByWilayah = Pelanggan::whereNotNull('latitude')
    ->whereNotNull('longitude')
    ->where('latitude', '!=', 0)
    ->where('longitude', '!=', 0)
    ->select('wilayah', DB::raw('count(*) as total'))
    ->groupBy('wilayah')
    ->get();

foreach ($coordsByWilayah as $c) {
    $wilayah = $c->wilayah ?? '<NULL>';
    echo "      '{$wilayah}' → {$c->total} dengan koordinat\n";
}

echo "\n✅ Check selesai!\n";
echo "\nNOTE: Ini data di database LOKAL (development).\n";
echo "Database PRODUCTION mungkin punya data berbeda.\n";
