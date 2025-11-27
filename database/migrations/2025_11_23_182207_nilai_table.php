<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migration untuk tabel nilai
     * Summary hasil ujian per siswa
     */
    public function up(): void
    {
        Schema::create('nilai', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sesi_ujian_id')->constrained('sesi_ujians')->cascadeOnDelete()->comment('FK ke sesi ujian');
            $table->foreignId('siswa_id')->constrained('siswa')->cascadeOnDelete()->comment('FK ke siswa');
            $table->foreignId('ujian_id')->constrained('ujian')->cascadeOnDelete()->comment('FK ke ujian');
            
            // Skor per section
            $table->decimal('skor_listening', 5, 2)->default(0)->comment('Skor listening section');
            $table->decimal('skor_reading', 5, 2)->default(0)->comment('Skor reading section');
            $table->decimal('skor_writing', 5, 2)->default(0)->comment('Skor writing section');
            
            // Total score (TOEFL scale: 310-677)
            $table->integer('skor_total')->unsigned()->default(0)->comment('Total skor TOEFL');
            
            // Statistik
            $table->integer('jumlah_benar')->unsigned()->default(0)->comment('Jumlah jawaban benar');
            $table->integer('jumlah_salah')->unsigned()->default(0)->comment('Jumlah jawaban salah');
            $table->integer('jumlah_kosong')->unsigned()->default(0)->comment('Jumlah soal tidak dijawab');
            
            // Status kelulusan
            $table->boolean('is_lulus')->default(false)->comment('Lulus atau tidak berdasarkan passing score');
            $table->string('predikat', 20)->nullable()->comment('Predikat nilai (Excellent, Good, Fair, Poor)');
            
            // Metadata
            $table->integer('durasi_pengerjaan_detik')->unsigned()->nullable()->comment('Total durasi pengerjaan dalam detik');
            $table->dateTime('tanggal_penilaian')->nullable()->comment('Tanggal nilai final dihitung');
            
            $table->timestamps();

            // Unique constraint: Satu sesi ujian = satu nilai
            $table->unique('sesi_ujian_id');

            // Index untuk report & analytics
            $table->index(['ujian_id', 'skor_total']);
            $table->index(['siswa_id', 'tanggal_penilaian']);
            $table->index(['ujian_id', 'is_lulus']);
        });
    }

    /**
     * Rollback migration
     */
    public function down(): void
    {
        Schema::dropIfExists('nilai');
    }
};