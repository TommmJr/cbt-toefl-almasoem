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
    Schema::create('sesi_ujians', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Siswa
        $table->foreignId('ujian_id')->constrained('ujians')->onDelete('cascade');
        
        $table->dateTime('waktu_mulai');
        $table->dateTime('waktu_selesai')->nullable();
        
        // Status pengerjaan
        $table->enum('status', ['sedang_mengerjakan', 'selesai', 'dihentikan_paksa'])->default('sedang_mengerjakan');
        
        // Nilai Akhir
        $table->float('nilai_total')->default(0);
        $table->float('nilai_listening')->default(0);
        $table->float('nilai_structure')->default(0);
        $table->float('nilai_reading')->default(0);
        $table->float('nilai_writing')->default(0); // Hasil AI
        
        // Keamanan
        $table->integer('jumlah_pelanggaran')->default(0); // Hitung berapa kali ganti tab
        $table->text('user_agent')->nullable(); // Info browser/device
        
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sesi_ujians');
    }
};
