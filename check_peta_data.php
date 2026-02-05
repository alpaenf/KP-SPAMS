<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Simulasi Data yang Dikirim ke Peta.vue ===\n\n";

// Ambil lokasi dari database
$mapSettings = \App\Models\MapSetting::where('is_active', true)->get();

// Ambil kantor KP-SPAMS dari database
$kantorData = $mapSettings->where('location_type', 'kp_spams')->first();
$kantor = $kantorData ? [
    'name' => $kantorData->name,
    'lat' => (float) $kantorData->latitude,
    'lng' => (float) $kantorData->longitude,
    'description' => $kantorData->description,
] : [
    // Default jika belum ada di database
    'name' => 'Kantor PAMSIMAS',
    'lat' => -6.200000,
    'lng' => 106.816666,
];

echo "KANTOR:\n";
echo json_encode($kantor, JSON_PRETTY_PRINT) . "\n\n";

// Ambil sumber air dari database
$sumberAirData = $mapSettings->where('location_type', 'sumber_air');
$sumberAir = $sumberAirData->isNotEmpty() 
    ? $sumberAirData->map(function ($item) {
        return [
            'name' => $item->name,
            'lat' => (float) $item->latitude,
            'lng' => (float) $item->longitude,
            'description' => $item->description,
        ];
    })->values()->toArray() // Tambah values() untuk reset keys
    : [
        // Default jika belum ada di database
        [
            'name' => 'Sumber Air Utama',
            'lat' => -6.201000,
            'lng' => 106.817000,
        ],
    ];

echo "SUMBER AIR:\n";
echo json_encode($sumberAir, JSON_PRETTY_PRINT) . "\n\n";

echo "Total Sumber Air: " . count($sumberAir) . " lokasi\n";
