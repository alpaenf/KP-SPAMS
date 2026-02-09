<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Pembayaran;
use App\Models\TagihanBulanan;

class BackfillTagihanFromPembayaran extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tagihan:backfill';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backfill tagihan_bulanan from existing pembayaran records';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== Backfill Tagihan dari Pembayaran ===');
        $this->newLine();

        // Ambil semua pembayaran
        $pembayaranList = Pembayaran::with('pelanggan')->get();
        
        $created = 0;
        $skipped = 0;
        $errors = 0;

        foreach ($pembayaranList as $pembayaran) {
            try {
                // Cek apakah sudah ada tagihan untuk pelanggan dan bulan ini
                $existingTagihan = TagihanBulanan::where('pelanggan_id', $pembayaran->pelanggan_id)
                    ->where('bulan', $pembayaran->bulan_bayar)
                    ->first();
                
                if ($existingTagihan) {
                    $skipped++;
                    $this->warn("⏭️  Skip: {$pembayaran->pelanggan->nama_pelanggan} - {$pembayaran->bulan_bayar} (sudah ada)");
                    continue;
                }
                
                // Tentukan status bayar berdasarkan keterangan
                $keterangan = strtoupper($pembayaran->keterangan ?? '');
                $statusBayar = 'SUDAH_BAYAR';
                
                if ($keterangan === 'TUNGGAKAN') {
                    $statusBayar = 'BELUM_BAYAR';
                } elseif ($keterangan === 'CICILAN') {
                    $statusBayar = 'BELUM_BAYAR';
                }
                
                // Buat tagihan baru
                TagihanBulanan::create([
                    'pelanggan_id' => $pembayaran->pelanggan_id,
                    'bulan' => $pembayaran->bulan_bayar,
                    'meteran_sebelum' => $pembayaran->meteran_sebelum ?? 0,
                    'meteran_sesudah' => $pembayaran->meteran_sesudah ?? 0,
                    'pemakaian_kubik' => $pembayaran->jumlah_kubik ?? 0,
                    'ada_abunemen' => $pembayaran->abunemen ?? false,
                    'total_tagihan' => $pembayaran->jumlah_bayar,
                    'status_bayar' => $statusBayar,
                    'tanggal_bayar' => $pembayaran->tanggal_bayar,
                ]);
                
                $created++;
                $this->info("✅ Created: {$pembayaran->pelanggan->nama_pelanggan} - {$pembayaran->bulan_bayar} - Rp " . number_format($pembayaran->jumlah_bayar, 0, ',', '.') . " ({$statusBayar})");
                
            } catch (\Exception $e) {
                $errors++;
                $this->error("❌ Error: {$pembayaran->pelanggan->nama_pelanggan} - {$pembayaran->bulan_bayar} - {$e->getMessage()}");
            }
        }

        $this->newLine();
        $this->info('=== Summary ===');
        $this->info("✅ Created: {$created}");
        $this->warn("⏭️  Skipped: {$skipped}");
        if ($errors > 0) {
            $this->error("❌ Errors: {$errors}");
        }
        $this->info('Selesai!');

        return Command::SUCCESS;
    }
}
