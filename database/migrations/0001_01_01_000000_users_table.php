<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migration untuk tabel users (tabel induk autentikasi)
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username', 50)->unique()->comment('Username untuk login');
            $table->string('email', 100)->unique()->nullable()->comment('Email pengguna (optional)');
            $table->string('password')->comment('Password ter-hash');
            $table->enum('role', ['admin', 'guru', 'siswa'])->default('siswa')->comment('Role pengguna dalam sistem');
            $table->boolean('is_active')->default(true)->comment('Status aktif user');
            $table->timestamp('last_login_at')->nullable()->comment('Waktu login terakhir');
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();

            // Index untuk optimasi query
            $table->index('role');
            $table->index('is_active');
            $table->index(['username', 'is_active']);
        });
    }

    /**
     * Rollback migration
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};