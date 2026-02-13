<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Pelanggan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

echo "=== CHECK NIK DAN FOTO RUMAH ===\n\n";

// 1. Check apakah kolom ada di database
echo "1. Checking database structure...\n";
try {
    $columns = DB::select("DESCRIBE pelanggan");
    $hasNik = false;
    $hasFoto = false;
    
    foreach ($columns as $column) {
        if ($column->Field === 'nik') {
            $hasNik = true;
            echo "   ✓ Kolom 'nik' ditemukan (Type: {$column->Type})\n";
        }
        if ($column->Field === 'foto_rumah') {
            $hasFoto = true;
            echo "   ✓ Kolom 'foto_rumah' ditemukan (Type: {$column->Type})\n";
        }
    }
    
    if (!$hasNik) {
        echo "   ✗ KOLOM 'nik' TIDAK DITEMUKAN! Migration belum dijalankan!\n";
    }
    if (!$hasFoto) {
        echo "   ✗ KOLOM 'foto_rumah' TIDAK DITEMUKAN! Migration belum dijalankan!\n";
    }
    
    if (!$hasNik || !$hasFoto) {
        echo "\n   SOLUSI: Jalankan 'php artisan migrate' untuk menambahkan kolom\n";
        exit(1);
    }
} catch (Exception $e) {
    echo "   ✗ Error: " . $e->getMessage() . "\n";
    exit(1);
}

// 2. Check apakah storage link sudah dibuat
echo "\n2. Checking storage link...\n";
$publicStoragePath = public_path('storage');
if (is_link($publicStoragePath) || is_dir($publicStoragePath)) {
    echo "   ✓ Storage link sudah ada\n";
    
    // Check apakah folder foto_rumah ada
    $fotoRumahPath = storage_path('app/public/foto_rumah');
    if (is_dir($fotoRumahPath)) {
        $files = glob($fotoRumahPath . '/*');
        echo "   ✓ Folder foto_rumah ada (berisi " . count($files) . " file)\n";
        
        if (count($files) > 0) {
            echo "   Files:\n";
            foreach (array_slice($files, 0, 5) as $file) {
                echo "     - " . basename($file) . "\n";
            }
        }
    } else {
        echo "   ⚠ Folder foto_rumah belum ada\n";
        echo "   SOLUSI: Upload foto untuk membuat folder otomatis\n";
    }
} else {
    echo "   ✗ Storage link BELUM dibuat!\n";
    echo "   SOLUSI: Jalankan 'php artisan storage:link'\n";
}

// 3. Check data pelanggan dengan NIK atau foto
echo "\n3. Checking data pelanggan...\n";
$pelangganDenganNik = Pelanggan::whereNotNull('nik')->count();
$pelangganDenganFoto = Pelanggan::whereNotNull('foto_rumah')->count();

echo "   - Pelanggan dengan NIK: {$pelangganDenganNik}\n";
echo "   - Pelanggan dengan foto: {$pelangganDenganFoto}\n";

if ($pelangganDenganNik > 0 || $pelangganDenganFoto > 0) {
    echo "\n4. Sample data (5 pelanggan pertama dengan NIK atau Foto):\n";
    $samples = Pelanggan::where(function($q) {
        $q->whereNotNull('nik')->orWhereNotNull('foto_rumah');
    })->limit(5)->get();
    
    foreach ($samples as $p) {
        echo "\n   ID: {$p->id_pelanggan}\n";
        echo "   Nama: {$p->nama_pelanggan}\n";
        echo "   NIK: " . ($p->nik ?: '(kosong)') . "\n";
        echo "   Foto: " . ($p->foto_rumah ?: '(kosong)') . "\n";
        
        if ($p->foto_rumah) {
            $fullPath = storage_path('app/public/' . $p->foto_rumah);
            if (file_exists($fullPath)) {
                $size = filesize($fullPath);
                echo "   File exists: YES (size: " . round($size/1024, 2) . " KB)\n";
                echo "   URL: " . $p->foto_rumah_url . "\n";
            } else {
                echo "   File exists: NO - File tidak ditemukan!\n";
            }
        }
    }
} else {
    echo "\n   ℹ Belum ada data pelanggan dengan NIK atau foto\n";
    echo "   Ini normal jika baru saja migration. Silakan test input data.\n";
}

echo "\n=== SELESAI ===\n";
