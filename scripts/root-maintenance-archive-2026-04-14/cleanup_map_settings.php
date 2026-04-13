<?php
// Script untuk membersihkan data map_settings anomali dan memverifikasi data

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\MapSetting;
use Illuminate\Support\Facades\DB;

echo "=== CLEANUP MAP SETTINGS ANOMALI ===\n\n";

// 1. Check semua data
$allSettings = MapSetting::all();
echo "Total data saat ini: " . $allSettings->count() . "\n\n";

if ($allSettings->isEmpty()) {
    echo "❌ Tidak ada data map_settings di database.\n";
    echo "   Data dari management (screenshot) belum tersimpan.\n\n";
    exit;
}

// 2. Tampilkan semua data
echo "=== SEMUA DATA ===\n";
foreach ($allSettings as $setting) {
    echo "ID: {$setting->id}\n";
    echo "  Type: {$setting->location_type}\n";
    echo "  Name: {$setting->name}\n";
    echo "  Koordinat: ({$setting->latitude}, {$setting->longitude})\n";
    echo "  Active: " . ($setting->is_active ? '✅ Yes' : '❌ No') . "\n";
    echo "  Created: {$setting->created_at}\n";
    echo "\n";
}

// 3. Deteksi data anomali (koordinat Jakarta area)
echo "=== CEK KOORDINAT ANOMALI (JAKARTA) ===\n";
$anomaliJakarta = MapSetting::where(function($query) {
    $query->whereBetween('latitude', [-6.5, -6.0])
          ->whereBetween('longitude', [106.0, 107.0]);
})->get();

if ($anomaliJakarta->isEmpty()) {
    echo "✅ Tidak ada data anomali di area Jakarta\n\n";
} else {
    echo "⚠️  Ditemukan {$anomaliJakarta->count()} data anomali:\n";
    foreach ($anomaliJakarta as $anomali) {
        echo "  - ID {$anomali->id}: {$anomali->name}\n";
        echo "    Koordinat: ({$anomali->latitude}, {$anomali->longitude})\n";
    }
    echo "\n";
    
    // Tanya konfirmasi untuk hapus
    echo "Apakah Anda ingin menghapus data anomali ini? (yes/no): ";
    $handle = fopen("php://stdin", "r");
    $line = trim(fgets($handle));
    
    if (strtolower($line) === 'yes' || strtolower($line) === 'y') {
        foreach ($anomaliJakarta as $anomali) {
            $anomali->delete();
            echo "✅ Dihapus: {$anomali->name} (ID: {$anomali->id})\n";
        }
        echo "\n";
    } else {
        echo "❌ Pembersihan dibatalkan\n\n";
    }
}

// 4. Cek data yang benar (Damar Wulan area)
echo "=== DATA AREA DAMAR WULAN ===\n";
$damarWulan = MapSetting::where(function($query) {
    $query->whereBetween('latitude', [-8.0, -7.0])
          ->whereBetween('longitude', [108.0, 110.0]);
})->get();

if ($damarWulan->isEmpty()) {
    echo "⚠️  Tidak ada data di area Damar Wulan!\n";
    echo "   Seharusnya ada:\n";
    echo "   - Kantor Pusat: (-7.60043000, 109.08140000)\n";
    echo "   - Sumber Air: (-7.58129100, 109.08441200)\n\n";
    
    echo "💡 Tip: Tambahkan data melalui menu 'Pengaturan Lokasi Peta'\n";
} else {
    echo "✅ Ditemukan {$damarWulan->count()} data:\n";
    foreach ($damarWulan as $data) {
        $activeIcon = $data->is_active ? '✅' : '❌';
        echo "  {$activeIcon} ID {$data->id}: {$data->name}\n";
        echo "     Type: {$data->location_type}\n";
        echo "     Koordinat: ({$data->latitude}, {$data->longitude})\n";
        echo "     Active: " . ($data->is_active ? 'Ya' : 'Tidak') . "\n";
        
        if (!$data->is_active) {
            echo "     ⚠️  STATUS TIDAK AKTIF - Data tidak akan muncul di peta!\n";
        }
        echo "\n";
    }
}

// 5. Ringkasan
echo "=== RINGKASAN ===\n";
$totalActive = MapSetting::where('is_active', true)->count();
$totalInactive = MapSetting::where('is_active', false)->count();
echo "Total data aktif: {$totalActive}\n";
echo "Total data tidak aktif: {$totalInactive}\n";
echo "\n";

if ($totalActive === 0) {
    echo "⚠️  WARNING: Tidak ada data aktif!\n";
    echo "   Peta akan menggunakan koordinat default.\n";
    echo "   Aktifkan data melalui management atau set is_active = 1 di database.\n";
}

echo "\n=== SELESAI ===\n";
