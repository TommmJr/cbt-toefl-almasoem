<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Skip ENUM modification for SQLite as it doesn't support MODIFY in ALTER TABLE
        if (DB::getDriverName() === 'mysql') {
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
    }

    public function down(): void
    {
        // Skip ENUM modification for SQLite as it doesn't support MODIFY in ALTER TABLE
        if (DB::getDriverName() === 'mysql') {
            DB::statement("
                ALTER TABLE sesi_ujians 
                MODIFY status ENUM(
                    'ongoing',
                    'completed',
                    'stopped'
                ) NOT NULL DEFAULT 'ongoing'
            ");
        }
    }
};
