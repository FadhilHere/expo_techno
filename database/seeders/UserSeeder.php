<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Super Admin
        User::create([
            'username' => 'superadmin',
            'password' => Hash::make('password123'),
            'role' => 'super_admin',
            'is_active' => true,
        ]);

        // Admin
        User::create([
            'username' => 'admin',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        // Mahasiswa 1
        User::create([
            'username' => 'mahasiswa1',
            'password' => Hash::make('password123'),
            'role' => 'mahasiswa',
            'is_active' => true,
        ]);

        // Mahasiswa 2
        User::create([
            'username' => 'mahasiswa2',
            'password' => Hash::make('password123'),
            'role' => 'mahasiswa',
            'is_active' => true,
        ]);

        // Mahasiswa 3
        User::create([
            'username' => 'mahasiswa3',
            'password' => Hash::make('password123'),
            'role' => 'mahasiswa',
            'is_active' => true,
        ]);
    }
}
