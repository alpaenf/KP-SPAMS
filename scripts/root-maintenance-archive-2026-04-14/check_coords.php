<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$pelanggan = \App\Models\Pelanggan::whereNotNull('latitude')
    ->whereNotNull('longitude')
    ->get(['id', 'id_pelanggan', 'nama_pelanggan', 'latitude', 'longitude']);

echo "Total pelanggan dengan koordinat: " . $pelanggan->count() . "\n\n";

foreach ($pelanggan as $p) {
    echo sprintf(
        "ID: %s | Nama: %s | Lat: %s | Lng: %s\n",
        $p->id_pelanggan,
        $p->nama_pelanggan,
        $p->latitude,
        $p->longitude
    );
}

echo "\n\nYang lolos filter (!=0):\n";
$filtered = $pelanggan->filter(function($p) {
    return $p->latitude != 0 && $p->longitude != 0;
});

echo "Total yang lolos: " . $filtered->count() . "\n";
foreach ($filtered as $p) {
    echo sprintf(
        "ID: %s | Nama: %s | Lat: %s | Lng: %s\n",
        $p->id_pelanggan,
        $p->nama_pelanggan,
        $p->latitude,
        $p->longitude
    );
}
