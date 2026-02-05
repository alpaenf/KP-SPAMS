<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Faq;
use App\Models\InformasiTarif;

class LandingContentSeeder extends Seeder
{
    public function run(): void
    {
        // Seed FAQs
        $faqs = [
            [
                'pertanyaan' => 'Bagaimana cara mengajukan pemasangan sambungan baru?',
                'jawaban' => 'Untuk mengajukan pemasangan sambungan baru, silakan datang ke kantor KP-SPAMS dengan membawa fotocopy KTP dan KK. Kemudian isi formulir pendaftaran yang tersedia.',
                'urutan' => 1,
                'is_active' => true
            ],
            [
                'pertanyaan' => 'Bagaimana cara membayar tagihan air?',
                'jawaban' => 'Pembayaran tagihan air dapat dilakukan secara langsung di kantor KP-SPAMS atau melalui transfer bank. Bukti pembayaran dapat dikonfirmasi melalui WhatsApp atau langsung ke kantor.',
                'urutan' => 2,
                'is_active' => true
            ],
            [
                'pertanyaan' => 'Apa yang harus dilakukan jika terjadi kebocoran pipa?',
                'jawaban' => 'Jika terjadi kebocoran pipa, segera hubungi petugas KP-SPAMS melalui nomor telepon yang tersedia atau datang langsung ke kantor. Tim teknis kami akan segera menangani masalah tersebut.',
                'urutan' => 3,
                'is_active' => true
            ],
            [
                'pertanyaan' => 'Berapa lama proses pemasangan sambungan baru?',
                'jawaban' => 'Proses pemasangan sambungan baru biasanya memakan waktu 3-7 hari kerja setelah semua persyaratan administratif terpenuhi dan pembayaran biaya pemasangan dilakukan.',
                'urutan' => 4,
                'is_active' => true
            ],
            [
                'pertanyaan' => 'Apakah ada denda jika terlambat bayar?',
                'jawaban' => 'Ya, terdapat denda keterlambatan pembayaran sebesar 10% dari tagihan bulanan jika pembayaran dilakukan setelah tanggal jatuh tempo.',
                'urutan' => 5,
                'is_active' => true
            ]
        ];

        foreach ($faqs as $faq) {
            Faq::create($faq);
        }

        // Seed Informasi Tarif
        $tarifs = [
            [
                'judul' => 'Tarif Pemakaian 0-10 m³',
                'deskripsi' => 'Tarif untuk pemakaian air bersih 0 sampai 10 meter kubik per bulan',
                'harga' => 2000,
                'satuan' => 'm³',
                'kategori' => 'tarif',
                'urutan' => 1,
                'is_active' => true
            ],
            [
                'judul' => 'Tarif Pemakaian 11-20 m³',
                'deskripsi' => 'Tarif untuk pemakaian air bersih 11 sampai 20 meter kubik per bulan',
                'harga' => 2500,
                'satuan' => 'm³',
                'kategori' => 'tarif',
                'urutan' => 2,
                'is_active' => true
            ],
            [
                'judul' => 'Tarif Pemakaian > 20 m³',
                'deskripsi' => 'Tarif untuk pemakaian air bersih lebih dari 20 meter kubik per bulan',
                'harga' => 3000,
                'satuan' => 'm³',
                'kategori' => 'tarif',
                'urutan' => 3,
                'is_active' => true
            ],
            [
                'judul' => 'Biaya Pemasangan Baru',
                'deskripsi' => 'Biaya administrasi dan instalasi untuk pemasangan sambungan baru',
                'harga' => 500000,
                'satuan' => 'unit',
                'kategori' => 'biaya',
                'urutan' => 4,
                'is_active' => true
            ],
            [
                'judul' => 'Biaya Administrasi Bulanan',
                'deskripsi' => 'Biaya administrasi dan pemeliharaan yang dikenakan setiap bulan',
                'harga' => 5000,
                'satuan' => 'bulan',
                'kategori' => 'biaya',
                'urutan' => 5,
                'is_active' => true
            ],
            [
                'judul' => 'Denda Keterlambatan',
                'deskripsi' => 'Denda yang dikenakan untuk pembayaran yang melewati jatuh tempo',
                'harga' => 10000,
                'satuan' => 'bulan',
                'kategori' => 'retribusi',
                'urutan' => 6,
                'is_active' => true
            ]
        ];

        foreach ($tarifs as $tarif) {
            InformasiTarif::create($tarif);
        }
    }
}
