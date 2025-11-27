<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migration untuk tabel jawaban
     * Menyimpan semua jawaban siswa (pilihan ganda & essay)
     */
    public function up(): void
    {
        Schema::create('jawaban', function (Blueprint $table) {
            $table->id();
            
            // FK ke sesi_ujians (Plural/Jamak karena tabelnya sesi_ujians)
            $table->foreignId('sesi_ujian_id')->constrained('sesi_ujians')->cascadeOnDelete()->comment('FK ke sesi ujian');            
            
            // FK ke soal (Singular/Tunggal karena tabelnya soal)
            $table->foreignId('soal_id')->constrained('soal')->cascadeOnDelete()->comment('FK ke soal'); 

            // Jawaban pilihan ganda (A/B/C/D)
            $table->string('jawaban_pilihan', 5)->nullable()->comment('Jawaban pilihan ganda');
            
            // Jawaban essay (writing)
            $table->text('jawaban_essay')->nullable()->comment('Jawaban essay siswa');
            $table->integer('jumlah_kata')->unsigned()->nullable()->comment('Jumlah kata dalam essay');
            
            // Penilaian
            $table->boolean('is_benar')->nullable()->comment('TRUE jika jawaban benar (untuk pilihan ganda)');
            $table->decimal('skor', 5, 2)->nullable()->comment('Skor untuk soal ini');
            
            // AI Grading (khusus essay)
            $table->decimal('ai_score', 5, 2)->nullable()->comment('Skor dari AI grading');
            $table->text('ai_feedback')->nullable()->comment('Feedback dari AI (JSON)');
            $table->boolean('is_reviewed_by_teacher')->default(false)->comment('Sudah direview guru atau belum');
            $table->decimal('teacher_score', 5, 2)->nullable()->comment('Skor manual dari guru (override AI)');
            $table->text('teacher_feedback')->nullable()->comment('Feedback manual dari guru');
            
            $table->dateTime('waktu_jawab')->nullable()->comment('Timestamp saat dijawab');
            $table->timestamps();

            // Unique constraint: Satu soal hanya bisa dijawab sekali per sesi
            $table->unique(['sesi_ujian_id', 'soal_id']);

            // Index untuk kalkulasi nilai
            $table->index(['sesi_ujian_id', 'is_benar']);
            $table->index(['soal_id', 'is_benar']);
        });
    } 

    /**
     * Rollback migration
     */
    public function down(): void
    {
        Schema::dropIfExists('jawaban');
    }
};