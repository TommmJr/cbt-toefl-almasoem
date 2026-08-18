<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migration untuk tabel siswa (relasi dengan users)
     */
    public function up(): void
    {
        Schema::create('siswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete()->comment('FK ke tabel users');
            $table->string('nis', 20)->unique()->comment('Nomor Induk Siswa');
            $table->string('nama_lengkap', 100)->comment('Nama lengkap siswa');
            $table->string('kelas', 20)->comment('Kelas siswa (contoh: XII IPA 1)');
            $table->enum('jenis_kelamin', ['L', 'P'])->comment('Jenis kelamin (L/P)');
            $table->date('tanggal_lahir')->nullable()->comment('Tanggal lahir siswa');
            $table->text('alamat')->nullable()->comment('Alamat lengkap siswa');
            $table->string('no_telepon', 20)->nullable()->comment('Nomor telepon siswa/ortu');
            $table->string('foto_profil')->nullable()->comment('Path foto profil siswa');
            $table->timestamps();
            $table->softDeletes();

            // Index untuk pencarian dan filtering
            $table->index('nis');
            $table->index('kelas');
            $table->index(['kelas', 'nama_lengkap']);
        });
    }

    /**
     * Rollback migration
     */
    public function down(): void
    {
        Schema::dropIfExists('siswa');
    }
};