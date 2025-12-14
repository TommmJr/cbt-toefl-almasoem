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
    DB::statement("
        ALTER TABLE sesi_ujians 
        MODIFY status ENUM(
            'belum_mulai',
            'sedang_mengerjakan',
            'selesai',
            'diskualifikasi'
        ) NOT NULL DEFAULT 'belum_mulai'
    ");
}

public function down(): void
{
    DB::statement("
        ALTER TABLE sesi_ujians 
        MODIFY status ENUM(
            'ongoing',
            'completed',
            'stopped'
        ) NOT NULL DEFAULT 'ongoing'
    ");
}
};
