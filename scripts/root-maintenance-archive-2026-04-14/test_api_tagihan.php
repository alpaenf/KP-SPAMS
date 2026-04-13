<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\TagihanBulananController;
use Illuminate\Http\Request;

echo "=== TEST API GET TAGIHAN BY PELANGGAN & BULAN ===\n\n";

$controller = new TagihanBulananController();

// Test untuk DWH0001 bulan Januari 2026
echo "Testing: DWH0001 - Januari 2026\n";
$pelangganId = 9; // ID dari DWH0001
$bulan = '2026-01';

$response = $controller->getByPelangganBulan($pelangganId, $bulan);
$data = json_decode($response->getContent(), true);

echo "Response:\n";
print_r($data);
echo "\n";

if (isset($data['tagihan'])) {
    $tagihan = $data['tagihan'];
    echo "✓ Data ditemukan:\n";
    echo "  Meteran Sebelum: {$tagihan['meteran_sebelum']}\n";
    echo "  Meteran Sesudah: {$tagihan['meteran_sesudah']}\n";
    echo "  Pemakaian: {$tagihan['pemakaian_kubik']} m³\n";
    echo "  Status: {$tagihan['status_bayar']}\n";
} else {
    echo "❌ Data tidak ditemukan\n";
}
