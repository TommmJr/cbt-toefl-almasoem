<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel pivot: ujian_section
     * Menyimpan konfigurasi section per ujian (flexibility tinggi)
     */
    public function up(): void
    {
        Schema::create('ujian_section', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ujian_id')->constrained('ujian')->cascadeOnDelete()->comment('FK ke ujian');
            
            // Tipe section (tetap pakai Enum untuk type-safe)
            $table->enum('tipe_section', ['listening', 'structure', 'reading'])->comment('Tipe section TOEFL');
            
            // Urutan section dalam ujian (bisa di-custom)
            $table->integer('urutan')->unsigned()->comment('Urutan pengerjaan (1, 2, 3)');
            
            // Konfigurasi per section (OVERRIDE dari config default)
            $table->integer('durasi_menit')->unsigned()->comment('Durasi section ini (bisa beda dari default)');
            $table->integer('jumlah_soal_target')->unsigned()->comment('Target jumlah soal di section ini');
            $table->decimal('bobot_nilai', 5, 2)->default(1.0)->comment('Bobot nilai section (untuk custom scoring)');
            
            // Instruksi custom per section (optional)
            $table->text('instruksi_custom')->nullable()->comment('Instruksi tambahan untuk section ini');
            
            // Status
            $table->boolean('is_active')->default(true)->comment('Section aktif atau tidak');
            
            $table->timestamps();

            // Unique constraint: Satu ujian tidak bisa punya section yang sama 2x
            $table->unique(['ujian_id', 'tipe_section']);
            
            // Unique constraint: Urutan tidak boleh duplicate dalam 1 ujian
            $table->unique(['ujian_id', 'urutan']);
            
            // Index
            $table->index(['ujian_id', 'urutan', 'is_active']);
        });

        // Update tabel soal: tambah FK ke ujian_section
        Schema::table('soal', function (Blueprint $table) {
            $table->foreignId('ujian_section_id')
                ->nullable()
                ->after('ujian_id')
                ->constrained('ujian_section')
                ->cascadeOnDelete()
                ->comment('FK ke ujian_section (menggantikan tipe_soal)');
            
            // Index untuk query soal per section
            $table->index(['ujian_section_id', 'nomor_urut']);
        });
    }

    /**
     * Rollback migration
     */
    public function down(): void
    {
        Schema::table('soal', function (Blueprint $table) {
            $table->dropForeign(['ujian_section_id']);
            $table->dropColumn('ujian_section_id');
        });
        
        Schema::dropIfExists('ujian_section');
    }
};