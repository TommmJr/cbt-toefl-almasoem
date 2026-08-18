<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SiswaSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Hubungkan user 'siswa01' (dari UserSeeder) ke tabel Siswa
        $userSiswa01 = User::where('username', 'siswa01')->first();
        if ($userSiswa01) {
            Siswa::firstOrCreate(
                ['user_id' => $userSiswa01->id],
                [
                    'nis'           => '2025001',
                    'nama_lengkap'  => 'Siswa 01',
                    'kelas'         => 'XII RPL 1',
                    'jenis_kelamin' => 'L',
                ]
            );
        }

        // 2. DAFTAR AKUN TIM / SISWA TAMBAHAN
        $timDev = [
            ['siswa',    'Siswa Contoh',       '2025002'],
        ];

        foreach ($timDev as $dev) {
            $nisLogin = $dev[2]; // Ambil NIS buat jadi Username

            // Cek biar gak duplikat
            if (User::where('username', $nisLogin)->exists()) {
                continue;
            }

            // Bikin User Login (Username = NIS)
            $user = User::create([
                'username' => $nisLogin,
                'email'    => strtolower($nisLogin) . '@cbt.com', 
                'password' => Hash::make('123'),
                'role'     => 'siswa',
                'is_active'=> true,
            ]);

            // Bikin Profil Siswa
            Siswa::create([
                'user_id'       => $user->id,
                'nis'           => $nisLogin,
                'nama_lengkap'  => $dev[1],
                'kelas'         => 'XII RPL 1',
                'jenis_kelamin' => 'L',
            ]);
        }
    }
}