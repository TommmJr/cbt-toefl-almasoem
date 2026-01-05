<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Ambil data Guru yang sedang login
        $user = Auth::user();
        $guru = $user->guru; // Relasi dari User ke Guru

        if (!$guru) {
            abort(403, 'Data profil guru belum lengkap. Hubungi Admin.');
        }

        // 2. Ambil Statistik
        $totalUjian = $guru->ujian()->count();
        
        // Hitung ujian yang statusnya Published (Siap/Sedang jalan)
        $ujianAktif = $guru->ujian()->where('is_published', true)->count();
        
        // Hitung total siswa yang pernah ikut ujian buatan guru ini (Lewat tabel Nilai -> Ujian -> Guru)
        $totalPeserta = $guru->ujian()
            ->withCount('sesiUjian') 
            ->get()
            ->sum('sesi_ujian_count');

        // 3. Ambil 5 Ujian Terakhir 
        $ujianTerbaru = $guru->ujian()
            ->latest()
            ->limit(5)
            ->get();

        return view('guru.dashboard', compact(
            'guru', 
            'totalUjian', 
            'ujianAktif', 
            'totalPeserta', 
            'ujianTerbaru'
        ));
    }
}