<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migration untuk tabel guru
     */
    public function up(): void
    {
        Schema::create('guru', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete()->comment('FK ke tabel users');
            $table->string('nip', 30)->unique()->comment('Nomor Induk Pegawai');
            $table->string('nama_lengkap', 100)->comment('Nama lengkap guru');
            $table->string('mata_pelajaran', 50)->default('Bahasa Inggris')->comment('Mata pelajaran yang diampu');
            $table->string('no_telepon', 20)->nullable()->comment('Nomor telepon guru');
            $table->string('foto_profil')->nullable()->comment('Path foto profil guru');
            $table->timestamps();
            $table->softDeletes();

            // Index
            $table->index('nip');
        });
    }

    /**
     * Rollback migration
     */
    public function down(): void
    {
        Schema::dropIfExists('guru');
    }
};