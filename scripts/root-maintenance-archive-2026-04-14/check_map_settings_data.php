<?php
// Script untuk cek dan bersihkan data map_settings yang anomali

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\MapSetting;

echo "=== CEK DATA MAP SETTINGS ===\n\n";

$allSettings = MapSetting::all();

echo "Total data: " . $allSettings->count() . "\n\n";

foreach ($allSettings as $setting) {
    echo "ID: {$setting->id}\n";
    echo "Type: {$setting->location_type}\n";
    echo "Name: {$setting->name}\n";
    echo "Latitude: {$setting->latitude}\n";
    echo "Longitude: {$setting->longitude}\n";
    echo "Active: " . ($setting->is_active ? 'Yes' : 'No') . "\n";
    echo "Created: {$setting->created_at}\n";
    echo "---\n\n";
}

// Cek apakah ada koordinat yang anomali (Jakarta area sekitar -6.x lat, 106.x long)
echo "\n=== CEK KOORDINAT ANOMALI (JAKARTA AREA) ===\n";
$anomaliJakarta = MapSetting::whereBetween('latitude', [-6.5, -6.0])
    ->whereBetween('longitude', [106.0, 107.0])
    ->get();

if ($anomaliJakarta->isEmpty()) {
    echo "Tidak ada data anomali di area Jakarta\n";
} else {
    echo "Ditemukan {$anomaliJakarta->count()} data anomali:\n";
    foreach ($anomaliJakarta as $anomali) {
        echo "- ID {$anomali->id}: {$anomali->name} ({$anomali->latitude}, {$anomali->longitude})\n";
    }
}

// Cek koordinat yang benar (Damar Wulan area sekitar -7.x lat, 109.x long)
echo "\n=== DATA KOORDINAT YANG BENAR (DAMAR WULAN AREA) ===\n";
$damarWulan = MapSetting::whereBetween('latitude', [-8.0, -7.0])
    ->whereBetween('longitude', [108.0, 110.0])
    ->get();

if ($damarWulan->isEmpty()) {
    echo "Tidak ada data di area Damar Wulan\n";
} else {
    echo "Ditemukan {$damarWulan->count()} data:\n";
    foreach ($damarWulan as $data) {
        echo "- ID {$data->id}: {$data->name} ({$data->latitude}, {$data->longitude}) - " . ($data->is_active ? 'Aktif' : 'Tidak Aktif') . "\n";
    }
}
