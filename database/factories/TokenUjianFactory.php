<?php

namespace Database\Factories;

use App\Models\TokenUjian;
use App\Models\Ujian;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class TokenUjianFactory extends Factory
{
    protected $model = TokenUjian::class;

    public function definition(): array
    {
        return [
            // Relasi otomatis bikin data parent-nya (Ujian & User)
            'ujian_id' => Ujian::factory(), 
            'dibuat_oleh' => User::factory(), 
            
            // Data dummy token
            'kode_token' => strtoupper(Str::random(6)),
            'kuota_pemakaian' => 100,
            'jumlah_terpakai' => 0,
            'berlaku_dari' => now(),
            'berlaku_sampai' => now()->addHours(2), // Defaultnya aktif 2 jam
            'is_active' => true,
            'ip_address' => fake()->ipv4(),
        ];
    }

    /**
     * State khusus buat token yang udah expired
     * Dipanggil pake: TokenUjian::factory()->expired()->create();
     */
    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'berlaku_sampai' => now()->subHour(), // Mundurin waktu jadi 1 jam yang lalu
        ]);
    }
}