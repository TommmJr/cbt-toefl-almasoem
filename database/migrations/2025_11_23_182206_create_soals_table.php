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
    Schema::create('soals', function (Blueprint $table) {
        $table->id();
        $table->foreignId('ujian_id')->constrained('ujians')->onDelete('cascade');
        
        // Jenis: Pilihan ganda atau Essay (Writing)
        $table->enum('tipe', ['pilihan_ganda', 'essay']);
        
        // Konten Soal
        $table->longText('pertanyaan'); 
        $table->string('audio_path')->nullable(); // Buat Listening (path file)
        
        // Opsi Jawaban (Disimpan sebagai JSON biar fleksibel: ["A": "...", "B": "..."])
        $table->json('opsi_jawaban')->nullable(); 
        
        // Kunci Jawaban
        // Kalau PG: isinya "A" atau "B". 
        // Kalau Essay: isinya "Rubrik Penilaian" buat AI.
        $table->text('kunci_jawaban')->nullable(); 
        
        $table->integer('bobot_nilai')->default(1);
        $table->timestamps();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('soals');
    }
};
