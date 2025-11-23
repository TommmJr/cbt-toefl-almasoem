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
    Schema::create('ujians', function (Blueprint $table) {
        $table->id();
        $table->string('judul'); // Misal: "TOEFL Simulation Batch 1"
        $table->text('deskripsi')->nullable();
        
        // Tipe: apakah ini Reading, Listening, Structure, atau Full TOEFL
        $table->enum('kategori', ['listening', 'structure', 'reading', 'writing', 'full_toefl']);
        
        $table->integer('durasi_menit'); // Contoh: 120
        $table->dateTime('waktu_mulai');
        $table->dateTime('waktu_selesai');
        
        $table->string('token_ujian', 10)->nullable(); // Token masuk ujian
        $table->boolean('is_active')->default(false); // Status aktif/tidak
        $table->boolean('acak_soal')->default(true);
        
        $table->foreignId('dibuat_oleh')->constrained('users')->onDelete('cascade'); // ID Guru
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ujians');
    }
};
