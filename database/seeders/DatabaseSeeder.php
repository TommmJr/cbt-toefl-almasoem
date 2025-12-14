<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
public function run(): void
{
    // USERS dulu
    $this->call(UserSeeder::class); // atau seeder user lu

    // GURU HARUS SEBELUM UJIAN
    $this->call(GuruSeeder::class);

    // BARU ujian + section
    $this->call(DummyUjianSeeder::class);
}

}
