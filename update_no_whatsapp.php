<?php
// Script untuk update nomor WhatsApp pelanggan yang sudah berubah

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Pelanggan;

echo "=== UPDATE NOMOR WHATSAPP PELANGGAN ===\n\n";

// Contoh: Update pelanggan DW017 (Eko) dengan nomor baru
$idPelanggan = 'DW017'; // Bisa diganti sesuai kebutuhan

$pelanggan = Pelanggan::where('id_pelanggan', $idPelanggan)->first();

if (!$pelanggan) {
    echo "❌ Pelanggan dengan ID {$idPelanggan} tidak ditemukan.\n";
    exit;
}

echo "Data Pelanggan:\n";
echo "ID: {$pelanggan->id_pelanggan}\n";
echo "Nama: {$pelanggan->nama_pelanggan}\n";
echo "No WA Lama: {$pelanggan->no_whatsapp}\n\n";

// Input nomor baru
echo "Masukkan nomor WhatsApp BARU (contoh: 085228357400 atau kosongkan untuk skip): ";
$handle = fopen("php://stdin", "r");
$noWaBaru = trim(fgets($handle));

if (empty($noWaBaru)) {
    echo "\n❌ Dibatalkan. Tidak ada perubahan.\n";
    exit;
}

// Validasi format (angka saja)
if (!preg_match('/^[0-9]+$/', $noWaBaru)) {
    echo "\n❌ Format nomor tidak valid. Hanya boleh angka.\n";
    exit;
}

// Normalisasi nomor (tambah 62 jika diawali 0)
if (substr($noWaBaru, 0, 1) === '0') {
    $noWaBaru = '62' . substr($noWaBaru, 1);
    echo "Info: Nomor dinormalisasi menjadi: {$noWaBaru}\n";
}

// Konfirmasi
echo "\nAnda akan mengubah:\n";
echo "Dari: {$pelanggan->no_whatsapp}\n";
echo "Ke  : {$noWaBaru}\n";
echo "\nLanjutkan? (yes/no): ";
$confirm = trim(fgets($handle));

if (strtolower($confirm) !== 'yes' && strtolower($confirm) !== 'y') {
    echo "\n❌ Dibatalkan.\n";
    exit;
}

// Update
$pelanggan->no_whatsapp = $noWaBaru;
$pelanggan->save();

echo "\n✅ Berhasil! Nomor WhatsApp {$pelanggan->nama_pelanggan} telah diupdate.\n";
echo "No WA Baru: {$pelanggan->no_whatsapp}\n\n";

echo "=== UPDATE BATCH UNTUK BEBERAPA PELANGGAN ===\n";
echo "Jika ingin update banyak pelanggan sekaligus, gunakan phpMyAdmin atau SQL:\n\n";
echo "UPDATE pelanggan SET no_whatsapp = '62xxxxx' WHERE id_pelanggan = 'DWxxx';\n";
echo "\nSelesai!\n";
