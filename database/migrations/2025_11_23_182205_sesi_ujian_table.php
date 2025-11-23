<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migration untuk tabel sesi_ujian
     * Tracking siswa yang sedang/sudah mengerjakan ujian
     */
    public function up(): void
    {
        Schema::create('sesi_ujian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ujian_id')->constrained('ujian')->cascadeOnDelete()->comment('FK ke ujian');
            $table->foreignId('siswa_id')->constrained('siswa')->cascadeOnDelete()->comment('FK ke siswa');
            $table->string('token_akses', 100)->unique()->comment('Token unik untuk akses ujian');
            $table->enum('status', ['belum_mulai', 'sedang_mengerjakan', 'selesai', 'diskualifikasi'])->default('belum_mulai')->comment('Status pengerjaan');
            $table->dateTime('waktu_mulai')->nullable()->comment('Waktu siswa mulai mengerjakan');
            $table->dateTime('waktu_selesai')->nullable()->comment('Waktu siswa submit jawaban');
            $table->integer('sisa_waktu_detik')->unsigned()->nullable()->comment('Sisa waktu dalam detik (untuk resume)');
            $table->integer('jumlah_tab_switch')->unsigned()->default(0)->comment('Hitungan ganti tab (untuk monitoring)');
            $table->integer('jumlah_peringatan')->unsigned()->default(0)->comment('Jumlah peringatan yang diterima');
            $table->text('catatan_pengawas')->nullable()->comment('Catatan dari admin/guru pengawas');
            $table->string('ip_address', 45)->nullable()->comment('IP address siswa');
            $table->text('user_agent')->nullable()->comment('Browser/device info');
            $table->timestamps();
            $table->softDeletes();

            // Unique constraint: Satu siswa hanya bisa punya 1 sesi per ujian
            $table->unique(['ujian_id', 'siswa_id']);

            // Index untuk monitoring real-time
            $table->index(['ujian_id', 'status']);
            $table->index('token_akses');
            $table->index(['status', 'waktu_mulai']);
        });
    }

    /**
     * Rollback migration
     */
    public function down(): void
    {
        Schema::dropIfExists('sesi_ujian');
    }
};