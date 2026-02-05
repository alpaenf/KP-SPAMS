<?php

namespace App\Observers;

use App\Models\Pelanggan;
use App\Models\TagihanBulanan;

class PelangganObserver
{
    /**
     * Handle the Pelanggan "created" event.
     */
    public function created(Pelanggan $pelanggan): void
    {
        // Pelanggan baru TIDAK otomatis dibuatkan tagihan
        // Tagihan akan dibuat saat:
        // 1. Admin generate tagihan bulanan massal
        // 2. Petugas input meteran via QR Scanner
        // 
        // Ini untuk menghindari pelanggan baru langsung dapat tagihan bulan-bulan sebelumnya
        
        // TIDAK ADA AUTO-CREATE TAGIHAN untuk pelanggan baru
        // Termasuk untuk kategori sosial sekalipun
    }

    /**
     * Handle the Pelanggan "updated" event.
     */
    public function updated(Pelanggan $pelanggan): void
    {
        // Jika pelanggan diubah menjadi kategori sosial
        if ($pelanggan->wasChanged('kategori') && $pelanggan->kategori === 'sosial') {
            // Update semua tagihan yang belum bayar menjadi SUDAH_BAYAR dan total 0
            TagihanBulanan::where('pelanggan_id', $pelanggan->id)
                ->where('status_bayar', 'BELUM_BAYAR')
                ->update([
                    'total_tagihan' => 0,
                    'tarif_per_kubik' => 0,
                    'biaya_abunemen' => 0,
                    'ada_abunemen' => false,
                    'status_bayar' => 'SUDAH_BAYAR',
                ]);
        }
    }
}
