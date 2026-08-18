<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('token_ujian', function (Blueprint $table) {
            // relasi ke siswa
            $table->foreignId('siswa_id')
                ->nullable()
                ->after('ujian_id')
                ->constrained('siswa')
                ->cascadeOnDelete();

            // 1 token = 1 siswa
            $table->unsignedInteger('kuota_pemakaian')->default(1)->change();
        });
    }

    public function down(): void
    {
        Schema::table('token_ujian', function (Blueprint $table) {
            $table->dropForeign(['siswa_id']);
            $table->dropColumn('siswa_id');
        });
    }
};
