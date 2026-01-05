<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,       // Bikin Admin & User dasar
            GuruSeeder::class,       // Bikin Data Guru (Wajib sebelum Ujian)
            DummyUjianSeeder::class, // Bikin Ujian & Soal Dummy
            SiswaSeeder::class,      // Bikin Siswa Dummy (2025001)
        ]);
    }
}