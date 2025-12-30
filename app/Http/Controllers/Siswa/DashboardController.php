<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Nilai;
use App\Models\Siswa;
use App\Models\SesiUjian;
use App\Models\Ujian; 
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        // 1. User Login (Sudah Lolos Auth & Role)
        $user = Auth::user();

        if (! $user) {
            abort(401, 'Unauthorized');
        }

        // 2. Page Param (Aman Buat Sidebar / Tab)
        $page = $request->query('page', 'home');

        // 3. Ambil Data Siswa (Defensive)
        $siswa = Siswa::where('user_id', $user->id)->first();
        $siswaId = $siswa?->id;

        // 4. Statistik (Semua Aman Walaupun Data Kosong)
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

        // 5. Nilai Terakhir (Max 3)
        $recentNilais = $siswaId
            ? Nilai::with('ujian')
                ->where('siswa_id', $siswaId)
                ->latest()
                ->limit(3)
                ->get()
            : collect();

        // 6. Sesi Ujian Terakhir Yang Selesai
        $sesiTerakhir = $siswaId
            ? SesiUjian::with('ujian')
                ->where('siswa_id', $siswaId)
                ->where('status', 'selesai')
                ->latest()
                ->first()
            : null;

        // 7. Data Ujian Aktif (Untuk Tab Test)
        // Ambil ujian yang Published DAN Waktunya Masuk Range (Aktif)
        $ujianAktif = Ujian::published()
            ->aktif() 
            ->orderBy('waktu_selesai', 'asc')
            ->get();

        // 8. Render Dashboard
        return view('siswa.dashboard', [
            'user'           => $user,
            'page'           => $page,
            'totalAttempts'  => $totalAttempts,
            'readingAvg'     => $readingAvg,
            'listeningAvg'   => $listeningAvg,
            'writingAvg'     => $writingAvg,
            'recentNilais'   => $recentNilais,
            'sesiTerakhir'   => $sesiTerakhir,
            'ujianAktif'     => $ujianAktif, 
        ]);
    }
}