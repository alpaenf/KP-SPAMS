<?php

namespace Database\Seeders;

use App\Models\MapSetting;
use Illuminate\Database\Seeder;

class MapSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Hapus data lama jika ada
        MapSetting::truncate();

        // Tambah lokasi Kantor KP-SPAMS
        MapSetting::create([
            'location_type' => 'kp_spams',
            'name' => 'Kantor Pusat KP-SPAMS',
            'latitude' => -6.200000,
            'longitude' => 106.816666,
            'description' => 'Sekretariat dan kantor pusat KP-SPAMS Desa',
            'is_active' => true,
        ]);

        // Tambah lokasi Sumber Air
        MapSetting::create([
            'location_type' => 'sumber_air',
            'name' => 'Sumber Air Utama',
            'latitude' => -6.201000,
            'longitude' => 106.817000,
            'description' => 'Sumber air utama yang melayani wilayah desa',
            'is_active' => true,
        ]);

        MapSetting::create([
            'location_type' => 'sumber_air',
            'name' => 'Sumber Air Cadangan',
            'latitude' => -6.199500,
            'longitude' => 106.818000,
            'description' => 'Sumber air cadangan untuk kebutuhan darurat',
            'is_active' => true,
        ]);
    }
}
