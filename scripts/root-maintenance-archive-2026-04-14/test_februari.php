<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\TagihanBulananController;

echo "=== TEST API FEBRUARI 2026 ===\n\n";

$controller = new TagihanBulananController();

// Test untuk DWH0001 bulan Februari 2026
echo "Testing: DWH0001 - Februari 2026\n";
$pelangganId = 9;
$bulan = '2026-02';

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
    echo "  Status: {$tagihan['status_bayar']}\n\n";
    
    echo "💡 MASALAHNYA:\n";
    echo "   Tagihan Februari sudah ada dengan meteran_sebelum = {$tagihan['meteran_sebelum']}\n";
    echo "   Seharusnya meteran_sebelum Februari = 88 (dari meteran_sesudah Januari)\n";
    echo "   Tapi saat ini = {$tagihan['meteran_sebelum']}\n\n";
    echo "   Karena tagihan Februari sudah ada, sistem akan pakai data yang ada.\n";
    echo "   Auto-fill hanya bekerja untuk bulan yang BELUM ada datanya.\n";
} else {
    echo "❌ Data tidak ditemukan\n";
}
