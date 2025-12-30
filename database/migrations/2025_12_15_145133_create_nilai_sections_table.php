<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('nilai_sections', function (Blueprint $table) {
            $table->id();

            // Relasi ke tabel sesi_ujians (pastikan nama tabel di DB 'sesi_ujians')
            $table->foreignId('sesi_ujian_id')->constrained('sesi_ujians')->cascadeOnDelete();
            
            // Relasi ke tabel ujian_sections
            $table->foreignId('ujian_section_id')->constrained('ujian_sections')->cascadeOnDelete();

            $table->unsignedInteger('total_soal')->default(0);
            $table->unsignedInteger('total_pg')->default(0);
            $table->unsignedInteger('benar')->default(0);
            $table->unsignedInteger('salah')->default(0);

            $table->timestamps();

            //  Idempotent guard: Satu sesi hanya boleh punya satu nilai per section
            $table->unique(['sesi_ujian_id', 'ujian_section_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nilai_sections');
    }
};