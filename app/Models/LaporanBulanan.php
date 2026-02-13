<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LaporanBulanan extends Model
{
    protected $fillable = [
        'bulan',
        'wilayah',
        'biaya_operasional_penarik',
        'biaya_pad_desa',
        'biaya_operasional_lapangan',
        'biaya_lain_lain',
        'biaya_csr',
        'catatan',
    ];
    
    protected $casts = [
        'biaya_operasional_penarik' => 'decimal:2',
        'biaya_pad_desa' => 'decimal:2',
        'biaya_operasional_lapangan' => 'decimal:2',
        'biaya_lain_lain' => 'decimal:2',
        'biaya_csr' => 'decimal:2',
    ];
}
