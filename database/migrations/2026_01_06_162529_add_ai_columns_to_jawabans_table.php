<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('jawaban', function (Blueprint $table) {
            
            if (!Schema::hasColumn('jawaban', 'ai_score')) {
                $table->decimal('ai_score', 5, 2)->nullable()->after('skor');
            }
            
            if (!Schema::hasColumn('jawaban', 'ai_feedback')) {
                $table->json('ai_feedback')->nullable()->after('ai_score');
            }
        });
    }

    public function down()
    {
        Schema::table('jawaban', function (Blueprint $table) {
            $table->dropColumn(['ai_score', 'ai_feedback']);
        });
    }
};