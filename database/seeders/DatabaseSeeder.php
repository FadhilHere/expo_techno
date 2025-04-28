<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            TahunExpoSeeder::class,
            InformasiWebsiteSeeder::class,
            KategoriTenantSeeder::class,
            TenantSeeder::class,
            ProductSeeder::class,
            PreOrderSeeder::class,
            UserSeeder::class,
        ]);
    }
}
