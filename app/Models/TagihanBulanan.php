<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TagihanBulanan extends Model
{
    protected $table = 'tagihan_bulanan';
    
    protected $fillable = [
        'pelanggan_id',
        'bulan',
        'meteran_sebelum',
        'meteran_sesudah',
        'pemakaian_kubik',
        'tarif_per_kubik',
        'ada_abunemen',
        'biaya_abunemen',
        'total_tagihan',
        'jumlah_terbayar',
        'status_bayar',
        'keterangan',
    ];
    
    protected $casts = [
        'meteran_sebelum' => 'decimal:2',
        'meteran_sesudah' => 'decimal:2',
        'pemakaian_kubik' => 'decimal:2',
        'tarif_per_kubik' => 'decimal:2',
        'biaya_abunemen' => 'decimal:2',
        'total_tagihan' => 'decimal:2',
        'jumlah_terbayar' => 'decimal:2',
        'ada_abunemen' => 'boolean',
    ];
    
    public function pelanggan(): BelongsTo
    {
        return $this->belongsTo(Pelanggan::class);
    }
    
    /**
     * Hitung total tagihan berdasarkan pemakaian
     */
    public function hitungTagihan(): void
    {
        // Hitung pemakaian kubik
        if ($this->meteran_sebelum !== null && $this->meteran_sesudah !== null) {
            $this->pemakaian_kubik = $this->meteran_sesudah - $this->meteran_sebelum;
        } else {
            $this->pemakaian_kubik = 0;
        }
        
        // Hitung biaya pemakaian (pemakaian × tarif per kubik)
        $biaya_pemakaian = $this->pemakaian_kubik * $this->tarif_per_kubik;
        
        // Hitung biaya abunemen
        $biaya_abunemen = $this->ada_abunemen ? $this->biaya_abunemen : 0;
        
        // Total tagihan = biaya pemakaian + biaya abunemen
        $this->total_tagihan = $biaya_pemakaian + $biaya_abunemen;
    }
    
    /**
     * Hitung sisa tagihan yang belum dibayar
     */
    public function getSisaTagihanAttribute(): float
    {
        return max(0, $this->total_tagihan - $this->jumlah_terbayar);
    }
    
    /**
     * Cek apakah sudah lunas
     */
    public function isLunas(): bool
    {
        return $this->jumlah_terbayar >= $this->total_tagihan;
    }
    
    /**
     * Tambah pembayaran cicilan
     */
    public function tambahCicilan(float $jumlah): void
    {
        $this->jumlah_terbayar += $jumlah;
        
        // Update status jika sudah lunas
        if ($this->isLunas()) {
            $this->status_bayar = 'SUDAH_BAYAR';
        }
    }
    
    /**
     * Boot method untuk auto-hitung tagihan sebelum save
     */
    protected static function booted()
    {
        static::saving(function ($tagihan) {
            // Auto-hitung tagihan sebelum save jika ada meteran
            if ($tagihan->meteran_sebelum !== null && $tagihan->meteran_sesudah !== null) {
                $tagihan->hitungTagihan();
            }
        });
    }
}
