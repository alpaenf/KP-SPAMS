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
        'catatan',
    ];
    
    protected $casts = [
        'biaya_operasional_penarik' => 'decimal:2',
        'biaya_pad_desa' => 'decimal:2',
    ];
}
