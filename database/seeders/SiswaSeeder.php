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
        // 1. DAFTAR AKUN TIM
        // Format: [Label (buat email dummy), Nama Lengkap, NIS (User Login)]
        // Password default: '123'
        $timDev = [
            ['frontend', 'Si Paling Frontend', 'DEV001'],
            ['backend',  'Si Paling Backend',  'DEV002'],
            ['testing',  'Tukang Testing',     'DEV003'],
            ['siswa',    'Siswa Contoh',       '2025001'],
        ];

        foreach ($timDev as $dev) {
            $nisLogin = $dev[2]; // Ambil NIS buat jadi Username

            // Cek biar gak duplikat (cek berdasarkan username/NIS)
            if (User::where('username', $nisLogin)->exists()) {
                continue;
            }

            // Bikin User Login (Username = NIS)
            $user = User::create([
                'username' => $nisLogin, // LOGIN PAKE NIS
                // FIX: Gunakan NIS sebagai email biar unik dan gak bentrok duplicate entry
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