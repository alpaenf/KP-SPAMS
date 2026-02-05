<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MapSetting extends Model
{
    protected $fillable = [
        'location_type',
        'name',
        'latitude',
        'longitude',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];
}
