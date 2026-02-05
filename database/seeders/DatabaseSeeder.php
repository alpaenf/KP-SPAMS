<?php

namespace Database\Seeders;

use App\Models\Pelanggan;
use App\Models\Pembayaran;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Admin User
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@pamsimas.id',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Create Pengelola User
        User::create([
            'name' => 'Pengelola PAMSIMAS',
            'email' => 'pengelola@pamsimas.id',
            'password' => Hash::make('password'),
            'role' => 'pengelola',
        ]);

        // Create Sample Pelanggan
        $pelangganData = [
            [
                'id_pelanggan' => 'PLG001',
                'nama_pelanggan' => 'Budi Santoso',
                'no_whatsapp' => '081234567801',
                'rt' => '001',
                'rw' => '001',
                'kategori' => 'umum',
                'status_aktif' => true,
                'latitude' => -6.200100,
                'longitude' => 106.816700,
            ],
            [
                'id_pelanggan' => 'PLG002',
                'nama_pelanggan' => 'Siti Aminah',
                'no_whatsapp' => '081234567802',
                'rt' => '001',
                'rw' => '001',
                'kategori' => 'sosial',
                'status_aktif' => true,
                'latitude' => -6.200200,
                'longitude' => 106.816800,
            ],
            [
                'id_pelanggan' => 'PLG003',
                'nama_pelanggan' => 'Ahmad Wijaya',
                'no_whatsapp' => '081234567803',
                'rt' => '002',
                'rw' => '001',
                'kategori' => 'umum',
                'status_aktif' => true,
                'latitude' => -6.200300,
                'longitude' => 106.816900,
            ],
            [
                'id_pelanggan' => 'PLG004',
                'nama_pelanggan' => 'Dewi Lestari',
                'no_whatsapp' => '081234567804',
                'rt' => '002',
                'rw' => '001',
                'kategori' => 'umum',
                'status_aktif' => true,
                'latitude' => -6.200400,
                'longitude' => 106.817000,
            ],
            [
                'id_pelanggan' => 'PLG005',
                'nama_pelanggan' => 'Bambang Sutrisno',
                'no_whatsapp' => '081234567805',
                'rt' => '003',
                'rw' => '002',
                'kategori' => 'sosial',
                'status_aktif' => true,
                'latitude' => -6.200500,
                'longitude' => 106.817100,
            ],
            [
                'id_pelanggan' => 'PLG006',
                'nama_pelanggan' => 'Ratih Kusuma',
                'no_whatsapp' => '081234567806',
                'rt' => '003',
                'rw' => '002',
                'kategori' => 'umum',
                'status_aktif' => false,
                'latitude' => null,
                'longitude' => null,
            ],
            [
                'id_pelanggan' => 'PLG007',
                'nama_pelanggan' => 'Hendra Gunawan',
                'no_whatsapp' => '081234567807',
                'rt' => '004',
                'rw' => '002',
                'kategori' => 'umum',
                'status_aktif' => true,
                'latitude' => -6.200700,
                'longitude' => 106.817300,
            ],
            [
                'id_pelanggan' => 'PLG008',
                'nama_pelanggan' => 'Fitri Handayani',
                'no_whatsapp' => '081234567808',
                'rt' => '004',
                'rw' => '002',
                'kategori' => 'umum',
                'status_aktif' => true,
                'latitude' => null,
                'longitude' => null,
            ],
        ];

        foreach ($pelangganData as $data) {
            Pelanggan::create($data);
        }

        // Create Sample Pembayaran for December 2025
        $pelangganIds = Pelanggan::where('status_aktif', true)->pluck('id')->toArray();
        foreach ($pelangganIds as $pelangganId) {
            Pembayaran::create([
                'pelanggan_id' => $pelangganId,
                'bulan_bayar' => '2025-12',
                'tanggal_bayar' => '2025-12-31',
                'jumlah_bayar' => 50000,
                'keterangan' => 'Pembayaran pertama - Desember 2025',
            ]);
        }

        // Seed Landing Page Data
        $this->call(LandingPageSeeder::class);
    }
}
