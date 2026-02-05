<?php

namespace Database\Seeders;

use App\Models\VisiMisi;
use App\Models\Sejarah;
use Illuminate\Database\Seeder;

class LandingPageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create default Visi Misi
        VisiMisi::create([
            'visi' => 'Mewujudkan akses air bersih yang berkelanjutan untuk kesehatan dan kesejahteraan masyarakat desa',
            'misi' => [
                'Menyediakan air bersih berkualitas tinggi dan berkelanjutan',
                'Meningkatkan kesadaran masyarakat tentang pentingnya air bersih',
                'Membangun infrastruktur air yang modern dan terawat',
                'Memberikan pelayanan terbaik kepada seluruh pelanggan',
            ],
        ]);

        // Create default Sejarah
        Sejarah::create([
            'konten' => 'KP-SPAMS (DAMAR WULAN) adalah sistem penyediaan air minum yang didirikan untuk memenuhi kebutuhan air bersih bagi masyarakat desa. Dengan teknologi modern dan manajemen yang baik, kami berkomitmen untuk menyediakan air berkualitas tinggi dengan tarif yang terjangkau untuk semua kalangan masyarakat, khususnya kategori sosial yang dibebaskan dari biaya tagihan bulanan.',
        ]);
    }
}
