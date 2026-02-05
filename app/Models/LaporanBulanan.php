<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LaporanBulanan extends Model
{
    protected $fillable = [
        'bulan',
        'wilayah',
        'biaya_operasional_penarik',
        'catatan',
    ];
    
    protected $casts = [
        'biaya_operasional_penarik' => 'decimal:2',
    ];
}
