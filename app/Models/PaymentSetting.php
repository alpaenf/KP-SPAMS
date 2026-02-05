<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentSetting extends Model
{
    protected $fillable = [
        'qris_image',
        'bank_name',
        'account_number',
        'account_name',
        'payment_instructions',
        'qris_enabled',
        'bank_transfer_enabled',
    ];
    
    protected $casts = [
        'qris_enabled' => 'boolean',
        'bank_transfer_enabled' => 'boolean',
    ];

    /**
     * Get the singleton instance
     */
    public static function getSettings()
    {
        return self::firstOrCreate(
            ['id' => 1],
            [
                'bank_name' => 'BRI',
                'account_number' => '1234-5678-9012',
                'account_name' => 'KP-SPAMS Desa',
                'payment_instructions' => 'Transfer ke rekening di atas atau scan QRIS',
                'qris_enabled' => false,
                'bank_transfer_enabled' => true,
            ]
        );
    }
}
