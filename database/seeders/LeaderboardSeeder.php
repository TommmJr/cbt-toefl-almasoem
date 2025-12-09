<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Siswa;
use App\Models\Ujian;
use App\Models\Nilai;

class LeaderboardSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Ambil ID Guru
        $guruId = DB::table('users')->first()->id ?? 1;

        // 2. Bikin Ujian Dummy
        $ujian = Ujian::first();
        if (!$ujian) {
            $this->command->info('  Gak ada ujian, bikin ujian dummy dulu...');
            $ujianId = DB::table('ujian')->insertGetId([
                'guru_id' => $guruId,
                'kode_ujian' => 'TOEFL-SIM-01',
                'judul' => 'TOEFL Simulation Batch 1',
                'deskripsi' => 'Simulasi Ujian TOEFL Leaderboard',
                'tipe_ujian' => 'TOEFL ITP',
                'durasi_menit' => 120,
                'waktu_mulai' => now()->addDays(1),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $ujian = Ujian::find($ujianId);
        }

        // 3. Bikin Siswa Dummy
        if (Siswa::count() < 10) {
            $this->command->info('  Generating siswa dummy...');
            for ($i = 1; $i <= 10; $i++) {
                if (!Siswa::where('nis', '202500' . $i)->exists()) {
                    Siswa::create([
                        'user_id' => $guruId,
                        'nis' => '202500' . $i,
                        'nama_lengkap' => 'Siswa Cerdas ' . $i,
                        'kelas' => 'XII IPA ' . rand(1, 3),
                        'jenis_kelamin' => ($i % 2 == 0) ? 'L' : 'P',
                        'tanggal_lahir' => '2007-01-01',
                        'alamat' => 'Jl. Percobaan No. ' . $i,
                        'no_telepon' => '0812000000' . $i,
                    ]);
                }
            }
        }

        // 4. Generate Sesi & Nilai
        $siswas = Siswa::limit(10)->get();
        $this->command->info('  Generating Sesi & Nilai...');

        foreach ($siswas as $siswa) {
            // Bersihin data lama
            Nilai::where('siswa_id', $siswa->id)->delete();
            DB::table('sesi_ujians')->where('siswa_id', $siswa->id)->delete();

            // === FIX DISINI ===
            // Kita sesuaikan kolom insert dengan hasil Tinker lu tadi
            $sesiId = DB::table('sesi_ujians')->insertGetId([
                'ujian_id' => $ujian->id,
                'siswa_id' => $siswa->id,
                'status' => 'SELESAI', // Kita tembak status selesai
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Mozilla/5.0 (Seeder)',
                // 'waktu_mulai' => ... HAPUS INI KARENA GAK ADA DI DB LU
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Hitung skor random
            $listening = rand(45, 65); 
            $reading = rand(40, 60);
            $writing = rand(40, 60);
            $avg = ($listening + $reading + $writing) / 3;
            $total = (int) round($avg * 10); 
            
            $predikat = match(true) {
                $total >= 600 => 'Excellent',
                $total >= 500 => 'Good',
                $total >= 400 => 'Fair',
                default => 'Poor',
            };

            // Bikin Nilai
            Nilai::create([
                'siswa_id' => $siswa->id,
                'ujian_id' => $ujian->id,
                'sesi_ujian_id' => $sesiId, // Link ke ID sesi yang baru
                'skor_listening' => $listening,
                'skor_reading' => $reading,
                'skor_writing' => $writing,
                'skor_total' => $total,
                'jumlah_benar' => rand(80, 120),
                'jumlah_salah' => rand(10, 30),
                'jumlah_kosong' => rand(0, 5),
                'is_lulus' => $total >= 450,
                'predikat' => $predikat,
                'durasi_pengerjaan_detik' => 7000,
                'tanggal_penilaian' => now(),
            ]);
        }
        
        $this->command->info('  SIIP! Data Leaderboard Masuk Pak Eko!');
    }
}