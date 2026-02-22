<?php

/**
 * Script untuk update tarif_per_kubik dari 2500 ke 2000
 * Upload file ini ke hosting, lalu akses via browser:
 * https://domain-anda.com/fix_tarif_hosting.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\TagihanBulanan;

// Untuk keamanan, bisa ditambahkan password
$password_required = 'pamsimas2024'; // Ganti dengan password Anda
$password_input = $_GET['password'] ?? '';

if ($password_input !== $password_required) {
    die('🔒 Akses ditolak. Tambahkan ?password=pamsimas2024 di URL');
}

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Fix Tarif 2500 ke 2000</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 900px; margin: 50px auto; padding: 20px; }
        .success { color: green; }
        .error { color: red; }
        .info { color: blue; }
        pre { background: #f4f4f4; padding: 15px; border-radius: 5px; overflow-x: auto; }
    </style>
</head>
<body>
    <h1>🔧 Update Tarif Per Kubik dari Rp 2.500 → Rp 2.000</h1>
    
<?php

try {
    echo "<h2>📊 Cek Data Saat Ini:</h2>";
    
    // Cek berapa tagihan dengan tarif 2500
    $count2500 = TagihanBulanan::where('tarif_per_kubik', 2500)->count();
    echo "<p class='info'>✓ Tagihan dengan tarif Rp 2.500: <strong>{$count2500}</strong></p>";
    
    // Cek berapa tagihan dengan tarif 2000
    $count2000 = TagihanBulanan::where('tarif_per_kubik', 2000)->count();
    echo "<p class='info'>✓ Tagihan dengan tarif Rp 2.000: <strong>{$count2000}</strong></p>";
    
    if ($count2500 === 0) {
        echo "<p class='success'>✅ <strong>Tidak ada tagihan yang perlu diupdate!</strong></p>";
        echo "<p>Semua tagihan sudah menggunakan tarif Rp 2.000</p>";
    } else {
        echo "<h2>🔍 Sample Data yang Akan Diupdate:</h2>";
        echo "<pre>";
        
        $samples = TagihanBulanan::where('tarif_per_kubik', 2500)
            ->with('pelanggan')
            ->take(5)
            ->get();
        
        foreach ($samples as $t) {
            echo "ID Pelanggan: {$t->pelanggan->id_pelanggan}\n";
            echo "Nama: {$t->pelanggan->nama_pelanggan}\n";
            echo "Bulan: {$t->bulan}\n";
            echo "Pemakaian: {$t->pemakaian_kubik} m³\n";
            echo "Tarif Lama: Rp " . number_format($t->tarif_per_kubik, 0, ',', '.') . " / m³\n";
            echo "Abunemen: " . ($t->ada_abunemen ? "Ya (Rp " . number_format($t->biaya_abunemen, 0, ',', '.') . ")" : "Tidak") . "\n";
            echo "Total Lama: Rp " . number_format($t->total_tagihan, 0, ',', '.') . "\n";
            echo "---\n";
        }
        echo "</pre>";
        
        // Konfirmasi untuk update
        if (!isset($_GET['confirm']) || $_GET['confirm'] !== 'yes') {
            echo "<p class='error'>⚠️ <strong>Belum diupdate!</strong></p>";
            echo "<p>Untuk mengupdate, <a href='?password={$password_required}&confirm=yes' style='color: red; font-weight: bold;'>KLIK DI SINI</a></p>";
        } else {
            echo "<h2>🔄 Memulai Update...</h2>";
            
            $updated = 0;
            $errors = [];
            
            $tagihanList = TagihanBulanan::where('tarif_per_kubik', 2500)->get();
            
            foreach ($tagihanList as $tagihan) {
                try {
                    $oldTarif = $tagihan->tarif_per_kubik;
                    $oldTotal = $tagihan->total_tagihan;
                    
                    // Update tarif ke 2000
                    $tagihan->tarif_per_kubik = 2000;
                    
                    // Recalculate total tagihan
                    $biayaPemakaian = $tagihan->pemakaian_kubik * 2000;
                    $biayaAbunemen = $tagihan->ada_abunemen ? ($tagihan->biaya_abunemen ?? 3000) : 0;
                    $tagihan->total_tagihan = $biayaPemakaian + $biayaAbunemen;
                    
                    $tagihan->save();
                    
                    $updated++;
                    
                    if ($updated <= 10) { // Tampilkan 10 pertama
                        echo "<p class='success'>✅ {$tagihan->pelanggan->id_pelanggan} - {$tagihan->pelanggan->nama_pelanggan} ({$tagihan->bulan})</p>";
                        echo "<p style='margin-left: 20px;'>Tarif: Rp " . number_format($oldTarif, 0, ',', '.') . " → Rp 2.000</p>";
                        echo "<p style='margin-left: 20px;'>Total: Rp " . number_format($oldTotal, 0, ',', '.') . " → Rp " . number_format($tagihan->total_tagihan, 0, ',', '.') . "</p>";
                    }
                } catch (Exception $e) {
                    $errors[] = "Error pada tagihan ID {$tagihan->id}: " . $e->getMessage();
                }
            }
            
            if ($updated > 10) {
                echo "<p class='info'>... dan " . ($updated - 10) . " tagihan lainnya</p>";
            }
            
            echo "<hr>";
            echo "<h2>📈 Hasil Update:</h2>";
            echo "<p class='success'>✅ <strong>Berhasil update {$updated} tagihan</strong></p>";
            
            if (count($errors) > 0) {
                echo "<p class='error'>❌ Error: " . count($errors) . "</p>";
                echo "<pre>";
                foreach ($errors as $err) {
                    echo $err . "\n";
                }
                echo "</pre>";
            }
            
            // Verifikasi hasil
            $countAfter = TagihanBulanan::where('tarif_per_kubik', 2500)->count();
            if ($countAfter === 0) {
                echo "<p class='success'>✅ <strong>SELESAI! Semua tarif sudah Rp 2.000</strong></p>";
            } else {
                echo "<p class='error'>⚠️ Masih ada {$countAfter} tagihan dengan tarif Rp 2.500</p>";
            }
        }
    }
    
} catch (Exception $e) {
    echo "<p class='error'>❌ <strong>Error:</strong> " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

?>

    <hr>
    <p style="color: #666; font-size: 12px;">
        💡 Setelah selesai, sebaiknya hapus file ini dari hosting untuk keamanan.
    </p>
</body>
</html>
