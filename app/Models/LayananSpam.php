<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LayananSpam extends Model
{
    protected $table = 'layanan_spams';
    protected $guarded = ['id'];

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
