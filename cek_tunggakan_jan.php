<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\TagihanBulanan;
use App\Models\Pembayaran;
use Illuminate\Support\Facades\DB;

echo "=== INVESTIGASI TUNGGAKAN JANUARI ===\n\n";

// 1. Semua tagihan Januari per tahun
echo "--- 1. Jumlah tagihan Januari per tahun (BELUM_BAYAR) ---\n";
$perTahun = TagihanBulanan::where('bulan', 'like', '%-01')
    ->where('status_bayar', 'BELUM_BAYAR')
    ->selectRaw("LEFT(bulan, 4) as tahun, COUNT(*) as jumlah")
    ->groupBy('tahun')
    ->orderBy('tahun')
    ->get();
foreach ($perTahun as $r) {
    echo "  Tahun {$r->tahun}: {$r->jumlah} tagihan BELUM_BAYAR\n";
}

echo "\n--- 2. Contoh 10 tagihan Januari BELUM_BAYAR ---\n";
$contoh = TagihanBulanan::where('bulan', 'like', '%-01')
    ->where('status_bayar', 'BELUM_BAYAR')
    ->with('pelanggan:id,id_pelanggan,nama_pelanggan,status_aktif')
    ->orderBy('bulan')
    ->take(10)
    ->get(['id', 'pelanggan_id', 'bulan', 'status_bayar', 'total_tagihan', 'meteran_sebelum', 'meteran_sesudah', 'created_at']);
foreach ($contoh as $t) {
    $nama = $t->pelanggan->nama_pelanggan ?? 'N/A';
    $id_p = $t->pelanggan->id_pelanggan ?? 'N/A';
    $aktif = $t->pelanggan->status_aktif ? 'aktif' : 'nonaktif';
    echo "  [{$t->bulan}] $id_p - $nama | total_tagihan: {$t->total_tagihan} | meter: {$t->meteran_sebelum}->{$t->meteran_sesudah} | pelanggan: $aktif | created: {$t->created_at}\n";
}

echo "\n--- 3. Cek apakah mereka punya pembayaran di bulan January ---\n";
$pelangganNunggakJan = TagihanBulanan::where('bulan', 'like', '%-01')
    ->where('status_bayar', 'BELUM_BAYAR')
    ->pluck('pelanggan_id')
    ->unique();

// Cek jika ada pembayaran Januari untuk pelanggan2 tersebut
$bayarJan = Pembayaran::whereIn('pelanggan_id', $pelangganNunggakJan)
    ->where('bulan_bayar', 'like', '%-01')
    ->count();
echo "  Jumlah pelanggan yang 'BELUM_BAYAR' di tagihan Jan: {$pelangganNunggakJan->count()}\n";
echo "  Dari mereka, yang punya record pembayaran bulan Jan: $bayarJan\n";

echo "\n--- 4. Cek tagihan dengan total_tagihan = 0 di Januari ---\n";
$zeroTagihan = TagihanBulanan::where('bulan', 'like', '%-01')
    ->where('status_bayar', 'BELUM_BAYAR')
    ->where('total_tagihan', 0)
    ->count();
echo "  Tagihan Januari BELUM_BAYAR dengan total_tagihan = 0: $zeroTagihan\n";

echo "\n--- 5. Distribusi total_tagihan Januari BELUM_BAYAR ---\n";
$dist = TagihanBulanan::where('bulan', 'like', '%-01')
    ->where('status_bayar', 'BELUM_BAYAR')
    ->selectRaw("CASE WHEN total_tagihan = 0 THEN 'Rp 0' WHEN total_tagihan < 5000 THEN '< 5rb' WHEN total_tagihan < 20000 THEN '5-20rb' ELSE '> 20rb' END as range, COUNT(*) as jumlah")
    ->groupBy('range')
    ->get();
foreach ($dist as $r) {
    echo "  {$r->range}: {$r->jumlah} tagihan\n";
}

echo "\n--- 6. Total tagihan ANY bulan BELUM_BAYAR (bukan hanya Januari) ---\n";
$totalBelumBayar = TagihanBulanan::where('status_bayar', 'BELUM_BAYAR')
    ->selectRaw("LEFT(bulan, 7) as bulan, COUNT(*) as jumlah")
    ->groupBy('bulan')
    ->orderBy('bulan')
    ->get();
foreach ($totalBelumBayar as $r) {
    echo "  {$r->bulan}: {$r->jumlah} tagihan BELUM_BAYAR\n";
}

echo "\nSelesai.\n";
?>
