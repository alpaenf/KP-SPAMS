<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LayananSpam;

class LayananSeeder extends Seeder
{
    public function run(): void
    {
        $layanan = [
            [
                'icon' => 'water',
                'judul' => 'Penyediaan Air Bersih',
                'deskripsi' => 'Distribusi air bersih 24 jam untuk kebutuhan rumah tangga dengan kualitas yang terjamin dan terjangkau.',
                'urutan' => 1
            ],
            [
                'icon' => 'document',
                'judul' => 'Pemasangan Sambungan Baru',
                'deskripsi' => 'Layanan pemasangan sambungan air bersih untuk rumah baru dengan proses cepat dan biaya terjangkau.',
                'urutan' => 2
            ],
            [
                'icon' => 'cog',
                'judul' => 'Perbaikan & Maintenance',
                'deskripsi' => 'Layanan perbaikan meteran, pipa bocor, dan perawatan rutin untuk memastikan kelancaran distribusi air.',
                'urutan' => 3
            ],
            [
                'icon' => 'chat',
                'judul' => 'Konsultasi & Pengaduan',
                'deskripsi' => 'Layanan konsultasi gratis dan penanganan pengaduan melalui WhatsApp, telepon, atau datang langsung ke kantor.',
                'urutan' => 4
            ],
            [
                'icon' => 'payment',
                'judul' => 'Pembayaran Online',
                'deskripsi' => 'Sistem pembayaran yang mudah dengan konfirmasi otomatis melalui transfer bank dan bukti pembayaran digital.',
                'urutan' => 5
            ],
            [
                'icon' => 'chart',
                'judul' => 'Monitoring Pemakaian',
                'deskripsi' => 'Akses informasi tagihan dan riwayat pembayaran secara online dengan sistem yang terintegrasi.',
                'urutan' => 6
            ]
        ];

        foreach ($layanan as $item) {
            LayananSpam::create($item);
        }
    }
}
