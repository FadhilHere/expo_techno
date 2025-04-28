<?php

namespace Database\Seeders;

use App\Models\Tenant;
use Illuminate\Database\Seeder;

class TenantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tenants = [
            [
                'tahun_expo_id' => 1, // 2024
                'kategori_id' => 1, // Food & Beverage
                'nama_tenant' => 'Coffee Tech',
                'deskripsi' => 'Coffee Tech adalah kedai kopi yang mengadopsi teknologi IoT untuk monitoring suhu dan kelembaban dalam proses brewing. Kami juga menggunakan aplikasi mobile untuk pemesanan dan program loyalitas pelanggan.',
                'whatsapp_tenant' => '6281234567890',
            ],
            [
                'tahun_expo_id' => 1, // 2024
                'kategori_id' => 2, // Bakery & Dessert
                'nama_tenant' => 'Sweet Bytes',
                'deskripsi' => 'Sweet Bytes menggunakan teknologi 3D printing untuk membuat kue dan dessert dengan desain yang unik dan personalisasi. Kami juga menggunakan AI untuk menghasilkan resep baru berdasarkan preferensi pelanggan.',
                'whatsapp_tenant' => '6281234567891',
            ],
            [
                'tahun_expo_id' => 1, // 2024
                'kategori_id' => 3, // Fashion & Accessories
                'nama_tenant' => 'EcoStyle',
                'deskripsi' => 'EcoStyle menerapkan teknologi blockchain untuk memastikan transparansi rantai pasokan fashion berkelanjutan. Kami juga menggunakan teknologi digital printing ramah lingkungan untuk desain pakaian.',
                'whatsapp_tenant' => '6281234567892',
            ],
            [
                'tahun_expo_id' => 1, // 2024
                'kategori_id' => 4, // Handicraft
                'nama_tenant' => 'CraftTech',
                'deskripsi' => 'CraftTech menggabungkan kerajinan tradisional dengan teknologi laser cutting dan CNC untuk menghasilkan produk kerajinan dengan presisi tinggi namun tetap mempertahankan sentuhan tangan pengrajin.',
                'whatsapp_tenant' => '6281234567893',
            ],
            [
                'tahun_expo_id' => 1, // 2024
                'kategori_id' => 5, // Beauty & Wellness
                'nama_tenant' => 'NatureScan',
                'deskripsi' => 'NatureScan mengembangkan aplikasi AI untuk menganalisis kondisi kulit dan merekomendasikan produk skincare yang tepat. Semua produk kami berbahan alami dan menggunakan teknologi ekstraksi canggih.',
                'whatsapp_tenant' => '6281234567894',
            ],
        ];

        foreach ($tenants as $tenant) {
            Tenant::create($tenant);
        }
    }
}
