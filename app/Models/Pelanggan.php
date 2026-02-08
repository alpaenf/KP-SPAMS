<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class Pelanggan extends Model
{
    protected $table = 'pelanggan';
    
    protected $fillable = [
        'id_pelanggan',
        'nama_pelanggan',
        'no_whatsapp',
        'rt',
        'rw',
        'status_aktif',
        'kategori',
        'wilayah',
        'latitude',
        'longitude',
        'google_maps_url',
        'bulan_bayar',
        'tanggal_bayar',
        'jumlah_bayar',
    ];
    
    protected $casts = [
        'status_aktif' => 'boolean',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'tanggal_bayar' => 'date',
        'jumlah_bayar' => 'decimal:2',
    ];
    
    public function pembayarans(): HasMany
    {
        return $this->hasMany(Pembayaran::class);
    }
    
    public function tagihanBulanan(): HasMany
    {
        return $this->hasMany(TagihanBulanan::class);
    }
    
    public function hasCoordinates(): bool
    {
        return !is_null($this->latitude) && !is_null($this->longitude);
    }
    
    /**
     * Scope: Filter berdasarkan wilayah user yang login
     * Jika user admin, return semua data
     * Jika user penarik, return hanya data wilayahnya
     */
    public function scopeForUser($query, $user = null)
    {
        if (!$user) {
            $user = auth()->user();
        }
        
        if (!$user) {
            return $query;
        }
        
        // Admin bisa lihat semua
        if ($user->isAdmin()) {
            return $query;
        }
        
        // Penarik hanya bisa lihat wilayahnya
        if ($user->isPenarik() && $user->hasWilayah()) {
            return $query->where('wilayah', $user->getWilayah());
        }
        
        return $query;
    }
    
    /**
     * Scope: Filter berdasarkan wilayah tertentu
     */
    public function scopeByWilayah($query, $wilayah)
    {
        if (empty($wilayah)) {
            return $query;
        }
        
        return $query->where('wilayah', $wilayah);
    }
    
    public function getGoogleMapsLinkAttribute(): ?string
    {
        if (!$this->hasCoordinates()) {
            return null;
        }
        
        return "https://www.google.com/maps?q={$this->latitude},{$this->longitude}";
    }
    
    /**
     * Generate QR Code untuk pelanggan ini
     * QR Code berisi ID pelanggan yang akan di-scan oleh petugas penarikan
     */
    public function generateQrCode($format = 'svg', $size = 200)
    {
        return QrCode::format($format)
            ->size($size)
            ->errorCorrection('H')
            ->generate($this->id_pelanggan);
    }
    
    /**
     * Get QR Code sebagai base64 untuk display di frontend
     */
    public function getQrCodeBase64Attribute(): string
    {
        $svg = $this->generateQrCode('svg', 200);
        return base64_encode($svg);
    }
    
    /**
     * Get QR Code sebagai data URL untuk display langsung
     */
    public function getQrCodeDataUrlAttribute(): string
    {
        $svg = $this->generateQrCode('svg', 200);
        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }
}
