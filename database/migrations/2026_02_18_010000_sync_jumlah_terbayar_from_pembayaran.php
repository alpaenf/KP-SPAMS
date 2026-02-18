<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Migration ini akan:
     * 1. Ambil semua pembayaran yang sudah ada
     * 2. Hitung total pembayaran per tagihan
     * 3. Update jumlah_terbayar di tagihan_bulanan
     * 4. Update status_bayar jika sudah lunas
     */
    public function up(): void
    {
        echo "🔄 Syncing jumlah_terbayar from pembayarans...\n";

        
        // Ambil semua tagihan yang punya pembayaran
        $tagihanList = DB::table('tagihan_bulanan')
            ->select('id', 'pelanggan_id', 'bulan', 'total_tagihan', 'status_bayar')
            ->get();
        
        $updated = 0;
        $statusUpdated = 0;
        
        foreach ($tagihanList as $tagihan) {
            // Hitung total pembayaran untuk tagihan ini
            // Ambil semua pembayaran dengan bulan_bayar yang sama
            $totalBayar = DB::table('pembayarans')
                ->where('pelanggan_id', $tagihan->pelanggan_id)
                ->where('bulan_bayar', $tagihan->bulan)
                ->whereNotIn('keterangan', ['TUNGGAKAN', 'Tunggakan', 'tunggakan']) // Exclude tunggakan
                ->sum('jumlah_bayar');
            
            if ($totalBayar > 0) {
                // Update jumlah_terbayar
                DB::table('tagihan_bulanan')
                    ->where('id', $tagihan->id)
                    ->update(['jumlah_terbayar' => $totalBayar]);
                
                $updated++;
                
                // Cek apakah sudah lunas
                if ($totalBayar >= $tagihan->total_tagihan && $tagihan->status_bayar !== 'SUDAH_BAYAR') {
                    DB::table('tagihan_bulanan')
                        ->where('id', $tagihan->id)
                        ->update(['status_bayar' => 'SUDAH_BAYAR']);
                    
                    $statusUpdated++;
                } elseif ($totalBayar > 0 && $totalBayar < $tagihan->total_tagihan && $tagihan->status_bayar === 'BELUM_BAYAR') {
                    // Update ke CICILAN jika bayar sebagian
                    DB::table('tagihan_bulanan')
                        ->where('id', $tagihan->id)
                        ->update(['status_bayar' => 'CICILAN']);
                }
            }
        }
        
        echo "✅ Updated jumlah_terbayar for {$updated} tagihan\n";
        echo "✅ Updated status_bayar for {$statusUpdated} tagihan\n";
        echo "✅ Sync completed!\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reset semua jumlah_terbayar ke 0
        DB::table('tagihan_bulanan')
            ->update(['jumlah_terbayar' => 0]);
        
        echo "⚠️ Reset all jumlah_terbayar to 0\n";
    }
};
