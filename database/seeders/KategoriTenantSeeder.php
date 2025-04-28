<?php

namespace Database\Seeders;

use App\Models\KategoriTenant;
use Illuminate\Database\Seeder;

class KategoriTenantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kategori = [
            ['nama_kategori' => 'Food & Beverage'],
            ['nama_kategori' => 'Bakery & Dessert'],
            ['nama_kategori' => 'Fashion & Accessories'],
            ['nama_kategori' => 'Handicraft'],
            ['nama_kategori' => 'Beauty & Wellness'],
            ['nama_kategori' => 'Home Decor'],
            ['nama_kategori' => 'Digital & Technology'],
            ['nama_kategori' => 'Services'],
        ];

        foreach ($kategori as $item) {
            KategoriTenant::create($item);
        }
    }
}
