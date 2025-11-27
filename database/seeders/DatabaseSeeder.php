<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Bikin Akun ADMIN
        User::create([
            'username' => 'admin',
            'email' => 'admin@cbt.com',
            'password' => Hash::make('123'), 
            'role' => 'admin',
            'is_active' => true,
        ]);

        // 2. Bikin Akun GURU
        User::create([
            'username' => 'gurubahasa',
            'email' => 'guru@cbt.com',
            'password' => Hash::make('321'),
            'role' => 'guru',
            'is_active' => true,
        ]);

        // 3. Bikin Akun SISWA
        User::create([
            'username' => 'siswa01',
            'email' => 'siswa@cbt.com',
            'password' => Hash::make('111'),
            'role' => 'siswa',
            'is_active' => true,
        ]);

    
    }
}