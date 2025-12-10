<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Nilai;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        // 1. Ambil User & Page (PENTING BUAT SIDEBAR NATIVE)
        $user = Auth::user();
        $page = $request->query('page', 'home'); // Default ke 'home'

        // 2. Ambil Data Siswa (Relasi User -> Siswa)
        // Kita butuh ID Siswa buat query Nilai, bukan ID User
        $siswa = Siswa::where('user_id', $user->id)->first();
        $siswaId = $siswa ? $siswa->id : 0;

        // 3. Logic Statistik (Kodingan Lu)
        $totalAttempts = Nilai::where('siswa_id', $siswaId)->count();
        
        $readingAvg = Schema::hasColumn('nilai', 'skor_reading')
            ? Nilai::where('siswa_id', $siswaId)->avg('skor_reading')
            : 0;
            
        $listeningAvg = Schema::hasColumn('nilai', 'skor_listening')
            ? Nilai::where('siswa_id', $siswaId)->avg('skor_listening')
            : 0;
            
        $writingAvg = Schema::hasColumn('nilai', 'skor_writing')
            ? Nilai::where('siswa_id', $siswaId)->avg('skor_writing')
            : 0;
        
        // Recent activity (3 nilai terakhir)
        $recentNilais = Nilai::with('ujian')
            ->where('siswa_id', $siswaId)
            ->latest()
            ->limit(3)
            ->get();

        // 4. Kirim Semua ke View
        return view('siswa.dashboard', compact(
            'user', 
            'page',        
            'totalAttempts',
            'readingAvg',
            'listeningAvg',
            'writingAvg',
            'recentNilais'
        ));
    }
}