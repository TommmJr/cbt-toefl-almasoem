<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Log Sesi Ujian
     * Menyimpan konfigurasi section per ujian (flexibility tinggi)
     */
    public function up(): void
{
    Schema::create('sesi_ujians', function (Blueprint $table) {
        $table->id();
        // Relasi: Siapa yang ujian, ujian apa?
        $table->foreignId('ujian_id')->constrained('ujian')->cascadeOnDelete();
        $table->foreignId('siswa_id')->constrained('siswa')->cascadeOnDelete();
        
        // Status Pengerjaan (Log Siswa)
        $table->dateTime('waktu_mulai')->nullable();
        $table->dateTime('waktu_selesai')->nullable();
        $table->enum('status', ['belum_mulai', 'sedang_mengerjakan', 'selesai', 'diskualifikasi'])->default('belum_mulai');
        
        // Security & Teknis
        $table->string('ip_address')->nullable();
        $table->text('user_agent')->nullable();
        
        $table->timestamps();

        // Opsional: Satu siswa hanya boleh punya 1 sesi aktif di ujian yang sama
        // $table->unique(['ujian_id', 'siswa_id']); 
    });
}

    public function down(): void
    { 
        // Drop tabel plural
        Schema::dropIfExists('sesi_ujians');
    }
};