<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['username' => 'admin'],
            [
                'email' => 'admin@cbt.com',
                'password' => Hash::make('123'),
                'role' => 'admin',
                'is_active' => true,
            ]
        );

        User::firstOrCreate(
            ['username' => 'gurubahasa'],
            [
                'email' => 'guru@cbt.com',
                'password' => Hash::make('321'),
                'role' => 'guru',
                'is_active' => true,
            ]
        );

        User::firstOrCreate(
            ['username' => 'siswa01'],
            [
                'email' => 'siswa@cbt.com',
                'password' => Hash::make('111'),
                'role' => 'siswa',
                'is_active' => true,
            ]
        );
    }
}
