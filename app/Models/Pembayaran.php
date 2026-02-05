<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pembayaran extends Model
{
    protected $fillable = [
        'pelanggan_id',
        'meteran_sebelum',
        'meteran_sesudah',
        'abunemen',
        'tunggakan',
        'bulan_bayar',
        'jumlah_kubik',
        'tanggal_bayar',
        'jumlah_bayar',
        'keterangan',
    ];
    
    protected $casts = [
        'tanggal_bayar' => 'date',
        'meteran_sebelum' => 'decimal:2',
        'meteran_sesudah' => 'decimal:2',
        'abunemen' => 'boolean',
        'tunggakan' => 'decimal:2',
        'jumlah_kubik' => 'decimal:2',
        'jumlah_bayar' => 'decimal:2',
    ];
    
    public function pelanggan(): BelongsTo
    {
        return $this->belongsTo(Pelanggan::class);
    }
}
