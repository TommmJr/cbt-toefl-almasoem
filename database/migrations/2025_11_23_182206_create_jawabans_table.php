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
    Schema::create('jawabans', function (Blueprint $table) {
        $table->id();
        $table->foreignId('sesi_ujian_id')->constrained('sesi_ujians')->onDelete('cascade');
        $table->foreignId('soal_id')->constrained('soals')->onDelete('cascade');
        
        // Jawaban siswa. Kalau PG: "A", kalau Essay: "Teks panjang..."
        $table->longText('jawaban_siswa')->nullable();
        
        // Status penilaian (khusus essay butuh waktu buat AI mikir)
        $table->boolean('ragu_ragu')->default(false);
        $table->boolean('is_koreksi_ai')->default(false); // Flag kalo udah dinilai AI
        
        $table->float('nilai')->default(0); // Nilai per soal
        $table->text('feedback_ai')->nullable(); // Saran dari AI kenapa nilainya segitu
        
        $table->timestamps();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jawabans');
    }
};
