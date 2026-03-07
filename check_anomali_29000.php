<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== PEMBAYARAN BULAN MARET 2026 (bulan_bayar=2026-03) ===\n";
$p1 = DB::table('pembayarans')
    ->where('bulan_bayar', '2026-03')
    ->join('pelanggans', 'pembayarans.pelanggan_id', '=', 'pelanggans.id')
    ->select('pembayarans.id', 'pelanggans.id_pelanggan', 'pelanggans.nama_pelanggan', 'pembayarans.bulan_bayar', 'pembayarans.tanggal_bayar', 'pembayarans.jumlah_bayar')
    ->get();
if ($p1->isEmpty()) {
    echo "  (tidak ada)\n";
} else {
    foreach ($p1 as $p) {
        echo "  ID:{$p->id} | {$p->id_pelanggan} - {$p->nama_pelanggan} | Bulan:{$p->bulan_bayar} | Tgl:{$p->tanggal_bayar} | Rp{$p->jumlah_bayar}\n";
    }
    echo "  TOTAL: Rp" . $p1->sum('jumlah_bayar') . "\n";
}

echo "\n=== PEMBAYARAN TUNGGAKAN DIBAYAR DI MARET 2026 (bulan_bayar < 2026-03) ===\n";
$p2 = DB::table('pembayarans')
    ->where('bulan_bayar', '<', '2026-03')
    ->whereBetween('tanggal_bayar', ['2026-03-01', '2026-03-31'])
    ->join('pelanggans', 'pembayarans.pelanggan_id', '=', 'pelanggans.id')
    ->select('pembayarans.id', 'pelanggans.id_pelanggan', 'pelanggans.nama_pelanggan', 'pembayarans.bulan_bayar', 'pembayarans.tanggal_bayar', 'pembayarans.jumlah_bayar')
    ->get();
if ($p2->isEmpty()) {
    echo "  (tidak ada)\n";
} else {
    foreach ($p2 as $p) {
        echo "  ID:{$p->id} | {$p->id_pelanggan} - {$p->nama_pelanggan} | Bulan:{$p->bulan_bayar} | Tgl:{$p->tanggal_bayar} | Rp{$p->jumlah_bayar}\n";
    }
    echo "  TOTAL: Rp" . $p2->sum('jumlah_bayar') . "\n";
}

echo "\n=== TAGIHAN BULANAN KBK032 (Parmin) ===\n";
$pel = DB::table('pelanggans')->where('id_pelanggan', 'KBK032')->first();
if ($pel) {
    $tagihan = DB::table('tagihan_bulanans')
        ->where('pelanggan_id', $pel->id)
        ->orderBy('bulan')
        ->get(['bulan', 'total_tagihan', 'status_bayar', 'jumlah_terbayar']);
    if ($tagihan->isEmpty()) {
        echo "  (tidak ada tagihan)\n";
    } else {
        foreach ($tagihan as $t) {
            echo "  Bulan:{$t->bulan} | Total:Rp{$t->total_tagihan} | Terbayar:Rp{$t->jumlah_terbayar} | Status:{$t->status_bayar}\n";
        }
    }

    echo "\n=== PEMBAYARAN KBK032 di tabel pembayarans ===\n";
    $bayar = DB::table('pembayarans')->where('pelanggan_id', $pel->id)->orderBy('tanggal_bayar')->get();
    if ($bayar->isEmpty()) {
        echo "  (tidak ada record pembayaran)\n";
    } else {
        foreach ($bayar as $b) {
            echo "  ID:{$b->id} | Bulan:{$b->bulan_bayar} | Tgl:{$b->tanggal_bayar} | Rp{$b->jumlah_bayar}\n";
        }
    }
} else {
    echo "  Pelanggan KBK032 tidak ditemukan\n";
}

echo "\n=== SEMUA PEMBAYARAN MARET 2026 (tanggal_bayar di Maret, berapapun bulan_bayar-nya) ===\n";
$all = DB::table('pembayarans')
    ->whereBetween('tanggal_bayar', ['2026-03-01', '2026-03-31'])
    ->join('pelanggans', 'pembayarans.pelanggan_id', '=', 'pelanggans.id')
    ->select('pembayarans.id', 'pelanggans.id_pelanggan', 'pelanggans.nama_pelanggan', 'pembayarans.bulan_bayar', 'pembayarans.tanggal_bayar', 'pembayarans.jumlah_bayar')
    ->get();
if ($all->isEmpty()) {
    echo "  (tidak ada)\n";
} else {
    foreach ($all as $p) {
        echo "  ID:{$p->id} | {$p->id_pelanggan} - {$p->nama_pelanggan} | Bulan:{$p->bulan_bayar} | Tgl:{$p->tanggal_bayar} | Rp{$p->jumlah_bayar}\n";
    }
    echo "  TOTAL: Rp" . $all->sum('jumlah_bayar') . "\n";
}
