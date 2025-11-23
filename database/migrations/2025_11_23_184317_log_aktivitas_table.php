<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Migration untuk tabel log_aktivitas
     * Tracking semua aktivitas penting dalam sistem (audit trail)
     */
    public function up(): void
    {
        Schema::create('log_aktivitas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete()->comment('User yang melakukan aktivitas');
            $table->string('aksi', 100)->comment('Jenis aksi (login, buat_ujian, submit_jawaban, dll)');
            $table->string('modul', 50)->comment('Modul sistem (auth, ujian, soal, dll)');
            $table->text('deskripsi')->nullable()->comment('Deskripsi detail aktivitas');
            $table->json('data_lama')->nullable()->comment('Data sebelum perubahan (untuk update/delete)');
            $table->json('data_baru')->nullable()->comment('Data setelah perubahan');
            $table->string('ip_address', 45)->nullable()->comment('IP address user');
            $table->text('user_agent')->nullable()->comment('Browser/device info');
            $table->timestamp('created_at')->useCurrent()->comment('Waktu aktivitas');

            // Index untuk filtering log
            $table->index(['user_id', 'created_at']);
            $table->index(['aksi', 'created_at']);
            $table->index('modul');
        });
    }

    /**
     * Rollback migration
     */
    public function down(): void
    {
        Schema::dropIfExists('log_aktivitas');
    }
};