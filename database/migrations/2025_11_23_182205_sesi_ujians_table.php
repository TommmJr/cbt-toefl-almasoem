<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel pivot: ujian_sections
     * Menyimpan konfigurasi section per ujian (flexibility tinggi)
     */
    public function up(): void
    {
        Schema::create('sesi_ujians', function (Blueprint $table) {
            $table->id(); 
            
            $table->foreignId('ujian_id')->constrained('ujian')->cascadeOnDelete()->comment('FK ke ujian');
            $table->foreignId('siswa_id')->constrained('siswa')->cascadeOnDelete();

            // Status sesi ujian (untuk lifecycle pengerjaan)
            $table->enum('status', ['belum_mulai', 'sedang_mengerjakan', 'selesai'])->default('belum_mulai')->comment('Status sesi ujian');

            // Info jaringan / client
            $table->string('ip_address', 45)->nullable()->comment('IP siswa saat sesi dibuat');
            $table->text('user_agent')->nullable()->comment('User agent siswa saat sesi dibuat');

            $table->enum('tipe_section', ['listening', 'structure', 'reading'])->nullable()->comment('Tipe section TOEFL (nullable untuk sesi siswa)');
            $table->integer('urutan')->unsigned()->default(0)->comment('Urutan pengerjaan (1, 2, 3)');
            $table->integer('durasi_menit')->unsigned()->default(0)->comment('Durasi section ini');
            $table->integer('jumlah_soal_target')->unsigned()->default(0)->comment('Target jumlah soal');
            $table->decimal('bobot_nilai', 5, 2)->default(1.0)->comment('Bobot nilai');
            $table->text('instruksi_custom')->nullable();
            $table->boolean('is_active')->default(true);
            
            $table->timestamps();

            $table->unique(['ujian_id', 'tipe_section']);
            $table->index(['ujian_id', 'urutan', 'is_active']);
        });

    
    }

    public function down(): void
    {
        Schema::table('soal', function (Blueprint $table) {
            $table->dropForeign(['ujian_section_id']);
            $table->dropColumn('ujian_section_id');
        });
        
        // Drop tabel plural
        Schema::dropIfExists('sesi_ujians');
    }
};