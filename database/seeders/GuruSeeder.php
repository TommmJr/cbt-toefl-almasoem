<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Guru;
use App\Models\User;

class GuruSeeder extends Seeder
{
    public function run(): void
    {
        $userGuru = User::where('role', 'guru')->first();

        if (! $userGuru) {
            throw new \Exception('Seeder error: User guru belum ada.');
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
