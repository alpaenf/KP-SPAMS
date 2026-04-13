<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "🔧 Memperbaiki data A01 Januari...\n\n";

// Cari ID pelanggan A01
$pelanggan = DB::table('pelanggan')->where('id_pelanggan', 'A01')->first();

if (!$pelanggan) {
    echo "❌ Pelanggan A01 tidak ditemukan!\n";
    exit(1);
}

echo "✓ Pelanggan ditemukan: {$pelanggan->nama_pelanggan} (ID: {$pelanggan->id})\n\n";

// Cek pembayaran Januari 2026
$pembayaran = DB::table('pembayaran')
    ->where('pelanggan_id', $pelanggan->id)
    ->where('bulan_bayar', '2026-01')
    ->first();

if ($pembayaran) {
    echo "📋 Pembayaran Januari 2026:\n";
    echo "   - Jumlah bayar: Rp " . number_format($pembayaran->jumlah_bayar, 0, ',', '.') . "\n";
    echo "   - Keterangan: " . ($pembayaran->keterangan ?: '(kosong)') . "\n";
    echo "   - Tanggal: {$pembayaran->tanggal_bayar}\n\n";
    
    // Update keterangan jika kosong atau bukan TUNGGAKAN
    if (empty($pembayaran->keterangan) || !in_array(strtoupper($pembayaran->keterangan), ['TUNGGAKAN', 'CICILAN', 'LUNAS'])) {
        echo "🔄 Update keterangan pembayaran ke TUNGGAKAN...\n";
        DB::table('pembayaran')
            ->where('id', $pembayaran->id)
            ->update(['keterangan' => 'TUNGGAKAN']);
        echo "   ✓ Keterangan diupdate ke TUNGGAKAN\n\n";
    }
} else {
    echo "⚠️  Tidak ada pembayaran Januari 2026\n\n";
}

// Cek tagihan Januari 2026
$tagihan = DB::table('tagihan_bulanan')
    ->where('pelanggan_id', $pelanggan->id)
    ->where('bulan', '2026-01')
    ->first();

if ($tagihan) {
    echo "📋 Tagihan Januari 2026:\n";
    echo "   - Total tagihan: Rp " . number_format($tagihan->total_tagihan, 0, ',', '.') . "\n";
    echo "   - Jumlah terbayar: Rp " . number_format($tagihan->jumlah_terbayar ?? 0, 0, ',', '.') . "\n";
    echo "   - Status: {$tagihan->status_bayar}\n\n";
    
    // Update status jadi BELUM_BAYAR jika masih SUDAH_BAYAR
    if ($tagihan->status_bayar === 'SUDAH_BAYAR') {
        echo "🔄 Update status tagihan ke BELUM_BAYAR...\n";
        DB::table('tagihan_bulanan')
            ->where('id', $tagihan->id)
            ->update([
                'status_bayar' => 'BELUM_BAYAR',
                'jumlah_terbayar' => 0
            ]);
        echo "   ✓ Status diupdate ke BELUM_BAYAR\n";
        echo "   ✓ Jumlah terbayar direset ke 0\n\n";
    }
} else {
    echo "⚠️  Tidak ada tagihan Januari 2026\n\n";
}

echo "✅ Selesai! Silakan cek kembali di halaman tagihan/pembayaran.\n";
