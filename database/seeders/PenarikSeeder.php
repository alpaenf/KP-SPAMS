<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PenarikSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $wilayahList = [
            ['name' => 'Penarik Dawuhan', 'wilayah' => 'dawuhan', 'email' => 'dawuhan@pamsimas.com'],
            ['name' => 'Penarik Kubangsari Kulon', 'wilayah' => 'kubangsari_kulon', 'email' => 'kubkul@pamsimas.com'],
            ['name' => 'Penarik Kubangsari Wetan', 'wilayah' => 'kubangsari_wetan', 'email' => 'kubwet@pamsimas.com'],
            ['name' => 'Penarik Sokarame', 'wilayah' => 'sokarame', 'email' => 'sokarame@pamsimas.com'],
            ['name' => 'Penarik Tiparjaya', 'wilayah' => 'tiparjaya', 'email' => 'tiparjaya@pamsimas.com'],
        ];

        foreach ($wilayahList as $data) {
            User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => Hash::make('password'),
                    'role' => 'penarik',
                    'wilayah' => $data['wilayah'],
                    'pin' => '1234', // Default PIN
                ]
            );
        }

        $this->command->info('✅ 5 akun penarik berhasil dibuat!');
        $this->command->info('📧 Email: dawuhan@pamsimas.com, kubkul@pamsimas.com, dll');
        $this->command->info('🔑 Password: password');
        $this->command->info('📍 PIN: 1234');
    }
}
