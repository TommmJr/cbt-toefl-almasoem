<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migration untuk tabel soal
     */
    public function up(): void
    {
        Schema::create('soal', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ujian_section_id')->constrained('ujian_sections')->cascadeOnDelete()->comment('FK ke tabel ujian_section');
            $table->enum('tipe_soal', ['listening', 'reading', 'writing'])->comment('Tipe soal TOEFL');
            $table->integer('nomor_urut')->unsigned()->comment('Nomor urut soal dalam ujian');
            $table->text('pertanyaan')->comment('Isi pertanyaan/soal');
            
            // Untuk soal listening
            $table->string('audio_path')->nullable()->comment('Path file audio untuk listening');
            $table->integer('audio_duration')->unsigned()->nullable()->comment('Durasi audio dalam detik');
            
            // Untuk soal reading/writing
            $table->text('passage')->nullable()->comment('Teks bacaan untuk reading/writing');
            
            // Opsi jawaban (untuk pilihan ganda reading/listening)
            $table->json('opsi_jawaban')->nullable()->comment('Array opsi jawaban (A, B, C, D)');
            $table->string('jawaban_benar', 5)->nullable()->comment('Kunci jawaban (A/B/C/D atau NULL untuk essay)');
            
            // Untuk soal essay (writing)
            $table->text('rubrik_penilaian')->nullable()->comment('Rubrik penilaian essay (JSON)');
            $table->integer('min_kata')->unsigned()->nullable()->comment('Minimal jumlah kata untuk essay');
            $table->integer('max_kata')->unsigned()->nullable()->comment('Maksimal jumlah kata untuk essay');
            
            $table->integer('bobot_nilai')->unsigned()->default(1)->comment('Bobot poin untuk soal ini');
            $table->timestamps();
            $table->softDeletes();

            // Index untuk query optimization
            $table->index(['ujian_section_id', 'tipe_soal', 'nomor_urut']);
            $table->index(['ujian_section_id', 'nomor_urut']);

        });
    }

    /**
     * Rollback migration
     */
    public function down(): void
    {
        Schema::dropIfExists('soal');
    }
};