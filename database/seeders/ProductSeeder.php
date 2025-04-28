<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            // Coffee Tech Products
            [
                'tenant_id' => 1,
                'nama_produk' => 'Smart Brewing Kit',
                'harga' => 350000,
                'deskripsi' => 'Kit brewing kopi dilengkapi dengan sensor suhu dan kelembaban yang terhubung dengan aplikasi mobile.',
                'foto_produk' => 'brewing_kit.jpg',
            ],
            [
                'tenant_id' => 1,
                'nama_produk' => 'Single Origin Specialty Coffee',
                'harga' => 120000,
                'deskripsi' => 'Biji kopi specialty dari perkebunan terpilih, proses roasting dipantau dengan teknologi IoT.',
                'foto_produk' => 'coffee_beans.jpg',
            ],
            // Sweet Bytes Products
            [
                'tenant_id' => 2,
                'nama_produk' => '3D Printed Birthday Cake',
                'harga' => 450000,
                'deskripsi' => 'Kue ulang tahun dengan desain 3D printing kustom sesuai permintaan.',
                'foto_produk' => '3d_cake.jpg',
            ],
            [
                'tenant_id' => 2,
                'nama_produk' => 'AI-Generated Cookie Box',
                'harga' => 180000,
                'deskripsi' => 'Kotak berisi berbagai kue kering dengan rasa yang dikembangkan oleh AI berdasarkan preferensi pelanggan.',
                'foto_produk' => 'cookie_box.jpg',
            ],
            // EcoStyle Products
            [
                'tenant_id' => 3,
                'nama_produk' => 'Sustainable T-Shirt',
                'harga' => 299000,
                'deskripsi' => 'Kaos dari bahan organik dengan desain digital printing ramah lingkungan.',
                'foto_produk' => 'eco_tshirt.jpg',
            ],
            [
                'tenant_id' => 3,
                'nama_produk' => 'Recycled Tote Bag',
                'harga' => 150000,
                'deskripsi' => 'Tas belanja dari bahan daur ulang dengan teknologi waterproof.',
                'foto_produk' => 'tote_bag.jpg',
            ],
            // CraftTech Products
            [
                'tenant_id' => 4,
                'nama_produk' => 'Laser Cut Wooden Organizer',
                'harga' => 275000,
                'deskripsi' => 'Organizer kayu dengan detail laser cutting presisi tinggi.',
                'foto_produk' => 'wooden_organizer.jpg',
            ],
            [
                'tenant_id' => 4,
                'nama_produk' => 'CNC Carved Wall Art',
                'harga' => 500000,
                'deskripsi' => 'Hiasan dinding dengan motif tradisional yang dibuat dengan teknologi CNC.',
                'foto_produk' => 'wall_art.jpg',
            ],
            // NatureScan Products
            [
                'tenant_id' => 5,
                'nama_produk' => 'AI Skincare Analysis Session',
                'harga' => 150000,
                'deskripsi' => 'Sesi analisis kondisi kulit menggunakan aplikasi AI dilanjutkan dengan konsultasi.',
                'foto_produk' => 'skin_analysis.jpg',
            ],
            [
                'tenant_id' => 5,
                'nama_produk' => 'Personalized Natural Serum',
                'harga' => 385000,
                'deskripsi' => 'Serum wajah alami yang diformulasikan khusus berdasarkan hasil analisis kulit.',
                'foto_produk' => 'natural_serum.jpg',
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
