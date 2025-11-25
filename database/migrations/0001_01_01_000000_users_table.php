<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menjalankan migration untuk membuat tabel users (tabel utama autentikasi)
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username', 50)->unique()->comment('Username untuk login');
            $table->string('email', 100)->unique()->nullable()->comment('Email pengguna (opsional)');
            $table->string('password')->comment('Password yang sudah di-hash');
            $table->enum('role', ['admin', 'guru', 'siswa'])->default('siswa')->comment('Peran pengguna dalam sistem');
            $table->boolean('is_active')->default(true)->comment('Status aktif pengguna');
            $table->timestamp('last_login_at')->nullable()->comment('Waktu terakhir login');
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();

            // Index untuk meningkatkan performa query
            $table->index('role');
            $table->index('is_active');
            $table->index(['username', 'is_active']);
        });
    }

    /**
     * Menghapus tabel users (rollback)
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
