<?php

namespace App\Http\Controllers;

use App\Models\TokenUjian;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TokenUjianController extends Controller
{
    /**
     * [GURU] Generate Token Baru
     */
    public function generate(Request $request)
    {
        // Validasi input dari Guru
        $request->validate([
            'ujian_id' => 'required|exists:ujian,id',
            'berlaku_sampai' => 'required|date|after:now',
            'kuota' => 'required|integer|min:1',
        ]);

        try {
            // Panggil fungsi generate kode unik dari Model
            $kodeToken = TokenUjian::generateKodeToken();

            // Simpan ke database
            $token = TokenUjian::create([
                'ujian_id' => $request->ujian_id,
                'kode_token' => $kodeToken,
                'kuota_pemakaian' => $request->kuota,
                'jumlah_terpakai' => 0,
                'berlaku_dari' => now(),
                'berlaku_sampai' => $request->berlaku_sampai,
                'is_active' => true,
                'dibuat_oleh' => Auth::id(), // ID Guru yang login
                'ip_address' => $request->ip(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Token berhasil digenerate!',
                'data' => [
                    'kode_token' => $token->kode_token,
                    'expired_at' => $token->berlaku_sampai->format('d M Y H:i'),
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Gagal generate token: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * [SISWA] Validasi Token Sebelum Masuk Ujian
     */
    public function validateToken(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'kode_token' => 'required|string|size:6',
        ]);

        $kodeToken = strtoupper($request->kode_token);
        $user = Auth::user();

        // 2. Cek apakah user ini beneran siswa?
        $siswa = Siswa::where('user_id', $user->id)->first();
        if (!$siswa) {
            return response()->json(['success' => false, 'message' => 'Data siswa tidak ditemukan.'], 403);
        }

        // 3. Cari Token di Database
        $token = TokenUjian::with('ujian')->where('kode_token', $kodeToken)->first();

        if (!$token) {
            return response()->json(['success' => false, 'message' => 'Token tidak ditemukan!'], 404);
        }

        try {
            // 4. Panggil Logic Validasi di Model (Cek Expired, Kuota, dll)
            // Function ini bakal throw exception kalo ada yang salah
            $token->validasiToken($siswa);

            // 5. Kalau lolos, balikin data ujiannya buat konfirmasi di Frontend
            return response()->json([
                'success' => true,
                'message' => 'Token valid!',
                'data' => [
                    'ujian_judul' => $token->ujian->judul,
                    'deskripsi' => $token->ujian->deskripsi,
                    'durasi' => $token->ujian->durasi_menit . ' Menit',
                    'token_id' => $token->id,
                ]
            ]);

        } catch (\Exception $e) {
            // Tangkap error dari Model (misal: "Token expired") dan balikin ke API
            return response()->json([
                'success' => false, 
                'message' => $e->getMessage()
            ], 400);
        }
    }
}