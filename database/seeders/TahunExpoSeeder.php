<?php

namespace Database\Seeders;

use App\Models\TahunExpo;
use Illuminate\Database\Seeder;

class TahunExpoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tahun_expos = [
            [
                'tahun' => 2024,
                'deskripsi' => 'EXPO Technopreneurship 2024: Inovasi Digital untuk UMKM Indonesia',
            ],
            [
                'tahun' => 2023,
                'deskripsi' => 'EXPO Technopreneurship 2023: Transformasi Digital UMKM di Era Pasca Pandemi',
            ],
            [
                'tahun' => 2022,
                'deskripsi' => 'EXPO Technopreneurship 2022: Kebangkitan UMKM Melalui Teknologi',
            ],
        ];

        foreach ($tahun_expos as $tahun_expo) {
            TahunExpo::create($tahun_expo);
        }
    }
}
