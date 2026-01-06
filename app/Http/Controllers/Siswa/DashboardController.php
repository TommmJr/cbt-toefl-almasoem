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
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        // 1. User Login (Sudah Lolos Auth & Role)
        $user = Auth::user();

        if (! $user) {
            abort(401, 'Unauthorized');
        }

        // 3. Ambil Data Siswa (Pindah ke atas biar bisa dipake buat cek ujian)
        $siswa = Siswa::where('user_id', $user->id)->first();
        $siswaId = $siswa?->id;

        // ==========================================================
        // 🛡️ SATPAM UJIAN BASI (AUTO-FIX LOGIC)
        // ==========================================================
        if ($siswaId) {
            // 1. AMBIL DATA DULU (Bagian ini tadi ketinggalan bang!)
            $sesiLewatWaktu = SesiUjian::where('siswa_id', $siswaId)
                ->where('waktu_selesai', '<', now()) // Cari yang waktunya dah abis
                ->get();

            // 2. BARU DI-LOOPING
            foreach ($sesiLewatWaktu as $sesi) {
                
                // Cek Status pakai Enum (Biar gak error convert string)
                if ($sesi->status !== \App\Enums\StatusUjian::SELESAI) {
                    
                    if (method_exists($sesi, 'hitungNilaiDanSelesai')) {
                        $sesi->hitungNilaiDanSelesai();
                    } else {
                        // Fallback darurat
                        $sesi->update(['status' => \App\Enums\StatusUjian::SELESAI]);
                    }
                }
            }
        }
        // ==========================================================

        // 2. Page Param (Aman Buat Sidebar / Tab)
        $page = $request->query('page', 'home');

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
        $ujianAktif = Ujian::published()
            ->where('waktu_mulai', '<=', now())
            ->where('waktu_selesai', '>=', now())
            ->get();

        $ujianAkanDatang = Ujian::published()
            ->where('waktu_mulai', '>', now())
            ->get();

        // ==========================================================
        //  7.5. PREPARASI DATA UNTUK CHART (COLUMNS VERIFIED)
        // ==========================================================
        $chartData = [
            'labels' => [],
            'scores' => []
        ];

        if ($siswaId) {
            $nilais = Nilai::where('siswa_id', $siswaId)
                ->orderBy('created_at', 'asc') 
                ->limit(10)
                ->get();

            foreach ($nilais as $n) {
                // Pake created_at buat label tanggal di chart
                $chartData['labels'][] = $n->created_at->format('d/m');
                $chartData['scores'][] = $n->skor_total;
            }
        }
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
            'ujianAkanDatang'=> $ujianAkanDatang,
            'chartData'      => $chartData,
        ]);

    }
}