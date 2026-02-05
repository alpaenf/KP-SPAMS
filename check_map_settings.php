<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Data Map Settings di Database ===\n\n";

$mapSettings = \App\Models\MapSetting::all();

if ($mapSettings->count() === 0) {
    echo "Tidak ada data map settings di database.\n";
} else {
    foreach ($mapSettings as $setting) {
        echo "ID: {$setting->id}\n";
        echo "Tipe: {$setting->location_type}\n";
        echo "Nama: {$setting->name}\n";
        echo "Koordinat: {$setting->latitude}, {$setting->longitude}\n";
        echo "Status Aktif: " . ($setting->is_active ? 'Ya' : 'Tidak') . "\n";
        echo "Deskripsi: {$setting->description}\n";
        echo "---\n\n";
    }
    
    echo "Total: " . $mapSettings->count() . " lokasi\n\n";
    
    // Cek yang aktif saja
    $active = \App\Models\MapSetting::where('is_active', true)->get();
    echo "Total Aktif: " . $active->count() . " lokasi\n";
    
    // Cek by type
    $kpSpams = \App\Models\MapSetting::where('is_active', true)->where('location_type', 'kp_spams')->count();
    $sumberAir = \App\Models\MapSetting::where('is_active', true)->where('location_type', 'sumber_air')->count();
    
    echo "- KP-SPAMS: $kpSpams lokasi\n";
    echo "- Sumber Air: $sumberAir lokasi\n";
}
