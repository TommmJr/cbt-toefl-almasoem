<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migration untuk tabel ujian
     */
    public function up(): void
    {
        Schema::create('ujian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guru_id')->constrained('guru')->cascadeOnDelete()->comment('Guru yang membuat ujian');
            $table->string('kode_ujian', 20)->unique()->comment('Kode unik ujian (contoh: TOEFL-UAS-2025)');
            $table->string('judul', 150)->comment('Judul ujian');
            $table->text('deskripsi')->nullable()->comment('Deskripsi/instruksi ujian');
            $table->enum('tipe_ujian', ['practice', 'exam'])->default('exam')->comment('Tipe: latihan atau ujian resmi');
            $table->integer('durasi_menit')->unsigned()->comment('Durasi ujian dalam menit');
            $table->dateTime('waktu_mulai')->comment('Waktu mulai ujian');
            $table->dateTime('waktu_selesai')->comment('Waktu selesai ujian');
            $table->string('target_kelas')->nullable()->comment('Kelas yang ditargetkan (JSON array atau comma separated)');
            $table->integer('passing_score')->unsigned()->default(500)->comment('Nilai minimal kelulusan');
            $table->boolean('is_published')->default(false)->comment('Status publikasi ujian');
            $table->boolean('tab_lock_enabled')->default(true)->comment('Aktifkan fitur tab lock');
            $table->boolean('show_result_immediately')->default(false)->comment('Tampilkan hasil langsung setelah selesai');
            $table->timestamps();
            $table->softDeletes();

            // Index untuk query performance
            $table->index('kode_ujian');
            $table->index(['is_published', 'waktu_mulai']);
            $table->index(['tipe_ujian', 'is_published']);
            $table->index(['waktu_mulai', 'waktu_selesai']);
        });
    }

    /**
     * Rollback migration
     */
    public function down(): void
    {
        Schema::dropIfExists('ujian');
    }
};