<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Guru;
use App\Models\User;

class GuruSeeder extends Seeder
{
    public function run(): void
    {
        // Cari user spesifik berdasarkan username yang dibuat di UserSeeder
        $userGuru = User::where('username', 'gurubahasa')->first();

        // Fallback jika tidak ketemu username, cari berdasarkan role
        if (! $userGuru) {
            $userGuru = User::where('role', 'guru')->first();
        }

        if (! $userGuru) {
            throw new \Exception('Seeder error: User guru belum ada. Pastikan UserSeeder dijalankan duluan.');
        }

        Guru::firstOrCreate(
            ['user_id' => $userGuru->id],
            [
                'nip' => '194468600943',
                'nama_lengkap' => 'Joana Olson',
                'mata_pelajaran' => 'Bahasa Inggris',
            ]
        );
    }
}