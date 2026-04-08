<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$data = \App\Models\TagihanBulanan::where('status_bayar', 'BELUM_BAYAR')
    ->take(10)
    ->get(['id', 'pelanggan_id', 'bulan', 'status_bayar']);

echo json_encode($data, JSON_PRETTY_PRINT);
