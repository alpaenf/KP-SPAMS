<?php
// Check all pelanggan termasuk yang wilayah NULL atau format aneh
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Pelanggan;
use Illuminate\Support\Facades\DB;

echo "=== CHECK ALL PELANGGAN ===\n\n";

// 1. Total pelanggan
$total = Pelanggan::count();
echo "1. TOTAL PELANGGAN: {$total}\n\n";

// 2. Group by wilayah (termasuk NULL)
echo "2. PELANGGAN PER WILAYAH:\n";
$grouped = Pelanggan::select('wilayah', DB::raw('count(*) as total'))
    ->groupBy('wilayah')
    ->orderBy('total', 'desc')
    ->get();

$totalMapped = 0;
foreach ($grouped as $g) {
    $wilayah = $g->wilayah ?? '<NULL>';
    echo "   '{$wilayah}' → {$g->total} pelanggan\n";
    if ($g->wilayah) {
        $totalMapped += $g->total;
    }
}

echo "\n   Total mapped: {$totalMapped}\n";
echo "   Total unmapped (NULL): " . ($total - $totalMapped) . "\n";

// 3. Sample pelanggan dengan wilayah NULL
echo "\n3. SAMPLE PELANGGAN WILAYAH NULL:\n";
$nulls = Pelanggan::whereNull('wilayah')
    ->orWhere('wilayah', '')
    ->take(10)
    ->get(['id_pelanggan', 'nama_pelanggan', 'alamat', 'wilayah', 'status_langganan']);

if ($nulls->count() > 0) {
    foreach ($nulls as $p) {
        echo "   - {$p->id_pelanggan} | {$p->nama_pelanggan} | {$p->alamat} | Status: {$p->status_langganan}\n";
    }
} else {
    echo "   ✅ Tidak ada pelanggan dengan wilayah NULL\n";
}

// 4. Check pelanggan yang status_langganan = Aktif
echo "\n4. PELANGGAN AKTIF:\n";
$aktif = Pelanggan::where('status_langganan', 'Aktif')->count();
$aktifByWilayah = Pelanggan::where('status_langganan', 'Aktif')
    ->select('wilayah', DB::raw('count(*) as total'))
    ->groupBy('wilayah')
    ->orderBy('total', 'desc')
    ->get();

echo "   Total aktif: {$aktif}\n";
echo "   Breakdown:\n";
foreach ($aktifByWilayah as $a) {
    $wilayah = $a->wilayah ?? '<NULL>';
    echo "      '{$wilayah}' → {$a->total} aktif\n";
}

// 5. Check distinct values semua kolom wilayah
echo "\n5. SEMUA DISTINCT WILAYAH (case sensitive):\n";
$distinct = DB::table('pelanggan')
    ->select('wilayah')
    ->distinct()
    ->orderBy('wilayah')
    ->get();

foreach ($distinct as $d) {
    $val = $d->wilayah ?? '<NULL>';
    $count = Pelanggan::where('wilayah', $d->wilayah)->count();
    echo "   '{$val}' → {$count}\n";
}
