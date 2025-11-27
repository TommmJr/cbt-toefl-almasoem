<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Migration untuk tabel token_ujian
     * System token ala UNBK dengan 6 digit alphanumeric
     */
    public function up(): void
    {
        Schema::create('token_ujian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ujian_id')->constrained('ujian')->cascadeOnDelete()->comment('FK ke ujian');
            
            // Token 6 karakter (UPPERCASE alphanumeric tanpa 0,O,I,1 untuk avoid confusion)
            $table->string('kode_token', 6)->unique()->comment('Token 6 digit (contoh: A3B7K9)');
            
            // Metadata token
            $table->integer('kuota_pemakaian')->unsigned()->default(1)->comment('Berapa kali token bisa dipakai (default: 1x per siswa)');
            $table->integer('jumlah_terpakai')->unsigned()->default(0)->comment('Hitungan sudah dipakai berapa kali');
            
            // Validitas token
            $table->dateTime('berlaku_dari')->comment('Token aktif mulai kapan');
            $table->dateTime('berlaku_sampai')->comment('Token expired kapan');
            $table->boolean('is_active')->default(true)->comment('Status token (bisa di-disable manual)');
            
            // Audit trail
            $table->foreignId('dibuat_oleh')->constrained('users')->comment('Admin/Guru yang generate token');
            $table->string('ip_address', 45)->nullable()->comment('IP yang generate token');
            $table->timestamp('last_used_at')->nullable()->comment('Terakhir dipakai kapan');
            
            $table->timestamps();
            $table->softDeletes();

            // Index untuk performance
            $table->index(['kode_token', 'is_active']); // Query validasi token super cepat
            $table->index(['ujian_id', 'is_active']);
            $table->index(['berlaku_dari', 'berlaku_sampai']);
        });

        // Tabel pivot: tracking siapa aja yang udah pakai token ini
        Schema::create('token_ujian_usage', function (Blueprint $table) {
            $table->id();
            
            // Relasi ke tabel token_ujian (yang dibuat di atas)
            $table->foreignId('token_ujian_id')->constrained('token_ujian')->cascadeOnDelete();          
            $table->foreignId('siswa_id')->constrained('siswa')->cascadeOnDelete();
            $table->foreignId('sesi_ujian_id')->nullable()->constrained('sesi_ujians')->nullOnDelete()->comment('Link ke sesi ujian yang dibuat');
            
            $table->string('ip_address', 45)->comment('IP siswa saat pakai token');
            $table->text('user_agent')->nullable()->comment('Browser info');
            $table->timestamp('digunakan_pada')->useCurrent()->comment('Timestamp pakai token');

            // Constraint: 1 siswa hanya bisa pakai 1 token per ujian
            $table->unique(['token_ujian_id', 'siswa_id']);
            
            // Index
            $table->index(['token_ujian_id', 'digunakan_pada']);
            $table->index('siswa_id');
        });
    }

    /**
     * Rollback migration
     */
    public function down(): void
    {
        Schema::dropIfExists('token_ujian_usage');
        Schema::dropIfExists('token_ujian');
    }
};
