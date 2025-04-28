<?php

namespace Database\Seeders;

use App\Models\InformasiWebsite;
use Illuminate\Database\Seeder;

class InformasiWebsiteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $informasi = [
            [
                'judul' => 'Tentang EXPO Technopreneurship',
                'konten' => 'EXPO Technopreneurship adalah event tahunan yang bertujuan mempertemukan UMKM dengan teknologi terkini untuk meningkatkan daya saing usaha. Event ini memberikan platform bagi UMKM untuk memamerkan produk dan layanan sambil mengadopsi solusi teknologi untuk bisnis mereka.',
            ],
            [
                'judul' => 'Cara Berpartisipasi',
                'konten' => 'UMKM yang ingin berpartisipasi dalam EXPO Technopreneurship dapat mendaftar melalui website ini. Pendaftaran dibuka 3 bulan sebelum event berlangsung dan akan ditutup 1 bulan sebelum event. Setiap UMKM akan mendapatkan booth dan akses untuk mempromosikan produk mereka di platform digital kami.',
            ],
            [
                'judul' => 'Pre-Order System',
                'konten' => 'EXPO Technopreneurship menyediakan sistem pre-order online yang memungkinkan pengunjung untuk memesan produk dari tenant sebelum, selama, dan setelah event berlangsung. Sistem ini bertujuan meningkatkan penjualan tenant dan memberikan pengalaman berbelanja yang nyaman bagi pengunjung.',
            ],
        ];

        foreach ($informasi as $info) {
            InformasiWebsite::create($info);
        }
    }
}
