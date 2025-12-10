<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menambahkan index untuk optimasi query leaderboard
     * Run migration: php artisan migrate
     */
    public function up(): void
    {
        Schema::table('nilai', function (Blueprint $table) {
            // Index untuk query GROUP BY siswa_id
            $table->index('siswa_id', 'idx_nilai_siswa_id');
            
            // Index untuk sorting by skor_total
            $table->index('skor_total', 'idx_nilai_skor_total');
            
            // Composite index untuk optimasi maksimal
            $table->index(['siswa_id', 'skor_total'], 'idx_nilai_siswa_skor');
        });
    }

    public function down(): void
    {
        Schema::table('nilai', function (Blueprint $table) {
            $table->dropIndex('idx_nilai_siswa_id');
            $table->dropIndex('idx_nilai_skor_total');
            $table->dropIndex('idx_nilai_siswa_skor');
        });
    }
};