<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            // Gunakan UUID-based username untuk menghindari duplikasi pada load tinggi
            'username' => 'user-' . Str::uuid()->toString(),
            // Gunakan UUID agar email selalu unik bahkan saat concurrency tinggi
            'email' => Str::uuid()->toString() . '@example.com',
            'role' => 'siswa', // Default role
            'is_active' => true,
            'last_login_at' => null,
            
            // HAPUS atau Comment baris ini karena kolomnya GAK ADA di database lu
            // 'name' => fake()->name(), 
            // 'email_verified_at' => now(), 

            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            // 'email_verified_at' => null, // Hapus ini juga biar ga error
        ]);
    }
}