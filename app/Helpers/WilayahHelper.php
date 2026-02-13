<?php

namespace App\Helpers;

class WilayahHelper
{
    /**
     * Normalisasi nama wilayah untuk matching
     * - Lowercase
     * - Trim
     * - Ganti underscore dengan spasi
     * - Multiple spasi jadi 1 spasi
     */
    public static function normalize($wilayah)
    {
        if (empty($wilayah)) {
            return '';
        }
        
        $normalized = strtolower(trim($wilayah));
        $normalized = str_replace('_', ' ', $normalized);
        $normalized = preg_replace('/\s+/', ' ', $normalized);
        
        return $normalized;
    }
    
    /**
     * SQL expression untuk normalisasi wilayah di query
     */
    public static function getSqlExpression()
    {
        return "LOWER(TRIM(REPLACE(REPLACE(wilayah, '_', ' '), '  ', ' ')))";
    }
}
