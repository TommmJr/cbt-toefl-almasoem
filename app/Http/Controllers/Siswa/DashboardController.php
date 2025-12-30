<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Nilai;
use App\Models\Siswa;
use App\Models\SesiUjian;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        // 1 USER LOGIN (SUDAH LOLOS AUTH & ROLE)
        $user = Auth::user();

        // Fallback keras, tapi jelas (harusnya gak kejadian)
        if (! $user) {
            abort(401, 'Unauthorized');
        }

        // 2 PAGE PARAM (AMAN BUAT SIDEBAR / TAB)
        $page = $request->query('page', 'home');

        // 3 AMBIL DATA SISWA (DEFENSIVE)
        $siswa = Siswa::where('user_id', $user->id)->first();

        // Kalau somehow user siswa tapi record siswa belum ada
        // JANGAN redirect, dashboard tetap kebuka
        $siswaId = $siswa?->id;

        // 4 STATISTIK (SEMUA AMAN WALAUPUN DATA KOSONG)

        $totalAttempts = $siswaId
            ? Nilai::where('siswa_id', $siswaId)->count()
            : 0;

        $readingAvg = ($siswaId && Schema::hasColumn('nilai', 'skor_reading'))
            ? (float) Nilai::where('siswa_id', $siswaId)->avg('skor_reading')
            : 0;

        $listeningAvg = ($siswaId && Schema::hasColumn('nilai', 'skor_listening'))
            ? (float) Nilai::where('siswa_id', $siswaId)->avg('skor_listening')
            : 0;

        $writingAvg = ($siswaId && Schema::hasColumn('nilai', 'skor_writing'))
            ? (float) Nilai::where('siswa_id', $siswaId)->avg('skor_writing')
            : 0;

        // 5 NILAI TERAKHIR (MAX 3)
        $recentNilais = $siswaId
            ? Nilai::with('ujian')
                ->where('siswa_id', $siswaId)
                ->latest()
                ->limit(3)
                ->get()
            : collect();

        // 6 SESI UJIAN TERAKHIR YANG SELESAI
        $sesiTerakhir = $siswaId
            ? SesiUjian::with('ujian')
                ->where('siswa_id', $siswaId)
                ->where('status', 'selesai')
                ->latest()
                ->first()
            : null;

        // 7 RENDER DASHBOARD (TANPA REDIRECT LICIK)
        return view('siswa.dashboard', [
            'user'           => $user,
            'page'           => $page,
            'totalAttempts'  => $totalAttempts,
            'readingAvg'     => $readingAvg,
            'listeningAvg'   => $listeningAvg,
            'writingAvg'     => $writingAvg,
            'recentNilais'   => $recentNilais,
            'sesiTerakhir'   => $sesiTerakhir,
        ]);
    }
}
