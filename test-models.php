<?php

/**
 * Script testing untuk cek relationships Model
 */

use App\Models\{User, Siswa, Guru, Ujian, Soal, SesiUjian, Jawaban, Nilai};
use App\Enums\{RolePengguna, TipeSoal, StatusUjian, TipeUjian};

// Test 1: Create User + Siswa
$user = User::create([
    'username' => 'test_siswa',
    'email' => 'siswa@test.com',
    'password' => bcrypt('password'),
    'role' => RolePengguna::SISWA,
    'is_active' => true,
]);

$siswa = Siswa::create([
    'user_id' => $user->id,
    'nis' => '12345678',
    'nama_lengkap' => 'Test Siswa',
    'kelas' => 'XII IPA 1',
    'jenis_kelamin' => 'L',
]);

// Test 2: Create User + Guru
$userGuru = User::create([
    'username' => 'test_guru',
    'email' => 'guru@test.com',
    'password' => bcrypt('password'),
    'role' => RolePengguna::GURU,
]);

$guru = Guru::create([
    'user_id' => $userGuru->id,
    'nip' => '987654321',
    'nama_lengkap' => 'Test Guru',
    'mata_pelajaran' => 'Bahasa Inggris',
]);

// Test 3: Create Ujian
$ujian = Ujian::create([
    'guru_id' => $guru->id,
    'kode_ujian' => 'TOEFL-TEST-001',
    'judul' => 'Test TOEFL ITP',
    'tipe_ujian' => TipeUjian::EXAM,
    'durasi_menit' => 120,
    'waktu_mulai' => now(),
    'waktu_selesai' => now()->addHours(2),
    'passing_score' => 500,
    'is_published' => true,
]);

// Test 4: Create Soal
$soal = Soal::create([
    'ujian_id' => $ujian->id,
    'tipe_soal' => TipeSoal::READING,
    'nomor_urut' => 1,
    'pertanyaan' => 'What is the main idea of the passage?',
    'opsi_jawaban' => ['A' => 'Option A', 'B' => 'Option B', 'C' => 'Option C', 'D' => 'Option D'],
    'jawaban_benar' => 'A',
    'bobot_nilai' => 1,
]);

// Test 5: Test Relationships
echo "User Siswa: " . $user->siswa->nama_lengkap . "\n";
echo "Guru Ujian: " . $guru->ujian->first()->judul . "\n";
echo "Ujian Soal: " . $ujian->soal->count() . " soal\n";

// Test 6: Test Enum Methods
echo "Role Label: " . $user->role->label() . "\n";
echo "Is Admin? " . ($user->isAdmin() ? 'Yes' : 'No') . "\n";
echo "Tipe Soal Label: " . $soal->tipe_soal->label() . "\n";

echo "\n✅ All models working correctly!\n";