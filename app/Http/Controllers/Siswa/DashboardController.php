<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Nilai;
use Illuminate\View\View;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function index(): View
    {
        $siswaId = auth()->id();
        
        // Statistik siswa
        $totalAttempts = Nilai::where('siswa_id', $siswaId)->count();
        
        // Average scores — gunakan nama kolom yang ada: skor_*
        $readingAvg = Schema::hasColumn('nilai', 'skor_reading')
            ? Nilai::where('siswa_id', $siswaId)->avg('skor_reading')
            : null;
        $listeningAvg = Schema::hasColumn('nilai', 'skor_listening')
            ? Nilai::where('siswa_id', $siswaId)->avg('skor_listening')
            : null;
        $writingAvg = Schema::hasColumn('nilai', 'skor_writing')
            ? Nilai::where('siswa_id', $siswaId)->avg('skor_writing')
            : null;
        
        // Recent activity (3 ujian terakhir)
        $recentNilais = Nilai::with('ujian')
            ->where('siswa_id', $siswaId)
            ->latest()
            ->limit(3)
            ->get();

        return view('siswa.dashboard', compact(
            'totalAttempts',
            'readingAvg',
            'listeningAvg',
            'writingAvg',
            'recentNilais'
        ));
    }
}
