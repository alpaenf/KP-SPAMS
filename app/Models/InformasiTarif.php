<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InformasiTarif extends Model
{
    protected $fillable = [
        'judul',
        'deskripsi',
        'harga',
        'satuan',
        'kategori',
        'urutan',
        'is_active'
    ];

    protected $casts = [
        'harga' => 'decimal:2',
        'is_active' => 'boolean',
        'urutan' => 'integer'
    ];
}
