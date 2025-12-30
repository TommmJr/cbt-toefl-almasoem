<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Siswa;
use Illuminate\Support\Facades\Hash;

class SiswaSeeder extends Seeder
{
    public function run(): void
    {
        Siswa::firstOrCreate(
            ['nis' => '2025001'],
            [
                'nama' => 'Siswa Dummy',
                'password' => Hash::make('111'),
                'is_active' => true,
            ]
        );
    }
}
