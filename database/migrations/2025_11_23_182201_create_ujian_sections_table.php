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
    Schema::create('ujian_sections', function (Blueprint $table) {
        $table->id();
        $table->foreignId('ujian_id')->constrained('ujian')->cascadeOnDelete();
        $table->enum('tipe_section', ['listening', 'structure', 'reading', 'writing']);
        $table->string('judul_section'); 
        $table->integer('urutan');
        $table->integer('durasi_menit'); 
        $table->text('instruksi')->nullable(); 
        
       
        $table->string('audio_path')->nullable(); 
        $table->text('passage')->nullable(); 

        $table->timestamps();
    });
}
};
