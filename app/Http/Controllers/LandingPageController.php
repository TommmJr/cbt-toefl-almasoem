<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Ujian;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class LandingPageController extends Controller
{
    /**
     * Menampilkan landing page dengan upcoming ujian & leaderboard
     */
    public function index(): View
    {
        // Ambil semua kolom yang ada (tidak specify kolom tertentu)
        // Nanti kita filter di view berdasarkan kolom yang ada
        $upcomingUjian = Ujian::query()
            ->where('waktu_mulai', '>', now())
            ->orderBy('waktu_mulai', 'asc')
            ->limit(4)
            ->get(); // Tanpa parameter array kolom

        // Leaderboard dengan cache 5 menit
        $leaderboard = Cache::remember('landing_leaderboard', 300, function () {
            return $this->getOptimizedLeaderboard();
        });

        return view('landing', [
            'upcomingUjian' => $upcomingUjian,
            'leaderboard' => $leaderboard,
        ]);
    }

    /**
     * Query leaderboard yang dioptimalkan
     */
private function getOptimizedLeaderboard(): \Illuminate\Support\Collection
    {
        try {
            return DB::table('nilai')
                ->join('siswa', 'nilai.siswa_id', '=', 'siswa.id')
                ->select(
                    'siswa.id',
                    'siswa.nama_lengkap as nama', 
                    'siswa.kelas',
                    DB::raw('MAX(nilai.skor_total) as best_score')
                )
                ->whereNull('siswa.deleted_at')
                ->groupBy('siswa.id', 'siswa.nama_lengkap', 'siswa.kelas')
                ->orderByDesc('best_score')
                ->limit(6)
                ->get();
        } catch (\Exception $e) {
            \Log::error('Leaderboard query failed: ' . $e->getMessage());
            return collect([]);
        }
    }

    /**
     * Clear cache leaderboard (untuk admin)
     */
    public function clearCache(): \Illuminate\Http\JsonResponse
    {
        Cache::forget('landing_leaderboard');
        
        return response()->json([
            'success' => true,
            'message' => 'Cache leaderboard berhasil dihapus',
        ]);
    }
}