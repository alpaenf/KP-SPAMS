<?php
/**
 * Script untuk memperbaiki status_bayar yang tidak sesuai dengan keterangan
 * Akses: http://yourdomain.com/fix_status_hosting.php?password=pamsimas2024
 */

// Password protection
if (!isset($_GET['password']) || $_GET['password'] !== 'pamsimas2024') {
    die('Access denied. Password required.');
}

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

$action = $_GET['action'] ?? 'preview';

?>
<!DOCTYPE html>
<html>
<head>
    <title>Fix Status Bayar - PAMSIMAS</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        h1 { color: #2563eb; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; font-size: 14px; }
        th, td { padding: 8px; text-align: left; border: 1px solid #ddd; }
        th { background: #2563eb; color: white; }
        .btn { display: inline-block; padding: 10px 20px; margin: 10px 5px; border: none; border-radius: 5px; cursor: pointer; text-decoration: none; font-weight: bold; }
        .btn-primary { background: #2563eb; color: white; }
        .btn-danger { background: #dc2626; color: white; }
        .btn-success { background: #16a34a; color: white; }
        .alert { padding: 15px; margin: 15px 0; border-radius: 5px; }
        .alert-warning { background: #fef3c7; border: 1px solid #f59e0b; color: #92400e; }
        .alert-success { background: #d1fae5; border: 1px solid #10b981; color: #065f46; }
        .alert-danger { background: #fee2e2; border: 1px solid #ef4444; color: #991b1b; }
        .status-wrong { color: #dc2626; font-weight: bold; }
        .status-correct { color: #16a34a; font-weight: bold; }
        .badge { padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold; }
        .badge-red { background: #fee2e2; color: #991b1b; }
        .badge-blue { background: #dbeafe; color: #1e40af; }
        .badge-purple { background: #e0e7ff; color: #5b21b6; }
    </style>
</head>
<body>
<div class="container">
    <h1>🔧 Fix Status Bayar Berdasarkan Keterangan</h1>
    
    <?php
    try {
        // Query data yang perlu diperbaiki
        $query = "
            SELECT 
                tb.id,
                tb.bulan,
                p.nama as nama_pelanggan,
                p.wilayah,
                tb.status_bayar,
                pb.keterangan,
                tb.total_tagihan,
                tb.jumlah_terbayar
            FROM tagihan_bulanan tb
            JOIN pelanggan p ON tb.pelanggan_id = p.id
            LEFT JOIN pembayaran pb ON pb.pelanggan_id = tb.pelanggan_id 
                AND pb.bulan_bayar = tb.bulan
            WHERE pb.keterangan IS NOT NULL
            ORDER BY tb.id DESC
        ";
        
        $data = DB::select($query);
        
        $needFix = [];
        
        foreach ($data as $row) {
            $shouldBeStatus = null;
            
            if ($row->keterangan === 'TUNGGAKAN') {
                $shouldBeStatus = 'BELUM_BAYAR';
            } elseif ($row->keterangan === 'CICILAN') {
                if ($row->jumlah_terbayar >= $row->total_tagihan) {
                    $shouldBeStatus = 'SUDAH_BAYAR';
                } else {
                    $shouldBeStatus = 'BELUM_BAYAR';
                }
            } elseif ($row->keterangan === 'LUNAS') {
                $shouldBeStatus = 'SUDAH_BAYAR';
            }
            
            if ($shouldBeStatus && $row->status_bayar !== $shouldBeStatus) {
                $needFix[] = [
                    'id' => $row->id,
                    'bulan' => $row->bulan,
                    'nama' => $row->nama_pelanggan,
                    'wilayah' => $row->wilayah,
                    'current_status' => $row->status_bayar,
                    'should_be' => $shouldBeStatus,
                    'keterangan' => $row->keterangan,
                    'total_tagihan' => $row->total_tagihan,
                    'jumlah_terbayar' => $row->jumlah_terbayar ?? 0
                ];
            }
        }
        
        if ($action === 'fix' && count($needFix) > 0) {
            // Execute fix
            DB::beginTransaction();
            
            $fixed = 0;
            foreach ($needFix as $item) {
                if ($item['keterangan'] === 'TUNGGAKAN') {
                    DB::table('tagihan_bulanan')
                        ->where('id', $item['id'])
                        ->update([
                            'status_bayar' => 'BELUM_BAYAR',
                            'jumlah_terbayar' => 0
                        ]);
                } else {
                    DB::table('tagihan_bulanan')
                        ->where('id', $item['id'])
                        ->update([
                            'status_bayar' => $item['should_be']
                        ]);
                }
                $fixed++;
            }
            
            DB::commit();
            
            echo '<div class="alert alert-success">';
            echo '<strong>✅ Berhasil!</strong> ' . $fixed . ' data telah diperbaiki.';
            echo '</div>';
            echo '<a href="?password=pamsimas2024" class="btn btn-primary">Lihat Data Lagi</a>';
            
        } else {
            // Preview mode
            echo '<div class="alert alert-warning">';
            echo '<strong>⚠️ Total Data:</strong> ' . count($data) . ' tagihan dengan pembayaran<br>';
            echo '<strong>❌ Perlu Diperbaiki:</strong> ' . count($needFix) . ' data status tidak sesuai';
            echo '</div>';
            
            if (count($needFix) > 0) {
                echo '<h2>Data Yang Perlu Diperbaiki:</h2>';
                echo '<table>';
                echo '<thead><tr>';
                echo '<th>ID</th><th>Bulan</th><th>Nama</th><th>Wilayah</th>';
                echo '<th>Status Sekarang</th><th>Status Benar</th><th>Keterangan</th>';
                echo '<th>Terbayar / Total</th>';
                echo '</tr></thead><tbody>';
                
                foreach ($needFix as $item) {
                    $badgeClass = 'badge-red';
                    if ($item['keterangan'] === 'CICILAN') $badgeClass = 'badge-purple';
                    if ($item['keterangan'] === 'LUNAS') $badgeClass = 'badge-blue';
                    
                    echo '<tr>';
                    echo '<td>' . $item['id'] . '</td>';
                    echo '<td>' . $item['bulan'] . '</td>';
                    echo '<td>' . htmlspecialchars($item['nama']) . '</td>';
                    echo '<td>' . htmlspecialchars($item['wilayah']) . '</td>';
                    echo '<td class="status-wrong">' . $item['current_status'] . '</td>';
                    echo '<td class="status-correct">' . $item['should_be'] . '</td>';
                    echo '<td><span class="badge ' . $badgeClass . '">' . $item['keterangan'] . '</span></td>';
                    echo '<td>' . number_format($item['jumlah_terbayar']) . ' / ' . number_format($item['total_tagihan']) . '</td>';
                    echo '</tr>';
                }
                
                echo '</tbody></table>';
                
                echo '<div style="margin: 20px 0; padding: 15px; background: #fff3cd; border: 1px solid #ffc107; border-radius: 5px;">';
                echo '<strong>⚠️ PERHATIAN:</strong> Tindakan ini akan mengubah status_bayar di database.<br>';
                echo '<strong>Logika perbaikan:</strong><br>';
                echo '• TUNGGAKAN → status = BELUM_BAYAR, jumlah_terbayar = 0<br>';
                echo '• CICILAN → status = BELUM_BAYAR (kecuali terbayar >= total)<br>';
                echo '• LUNAS → status = SUDAH_BAYAR<br>';
                echo '</div>';
                
                echo '<a href="?password=pamsimas2024&action=fix" class="btn btn-danger" onclick="return confirm(\'Yakin akan memperbaiki ' . count($needFix) . ' data?\')">🔧 Perbaiki Sekarang</a>';
                
            } else {
                echo '<div class="alert alert-success">';
                echo '<strong>✅ Sempurna!</strong> Semua data sudah konsisten.';
                echo '</div>';
            }
        }
        
    } catch (Exception $e) {
        echo '<div class="alert alert-danger">';
        echo '<strong>❌ Error:</strong> ' . htmlspecialchars($e->getMessage());
        echo '</div>';
    }
    ?>
    
</div>
</body>
</html>
