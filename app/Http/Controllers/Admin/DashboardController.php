<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{User, Ujian, SesiUjian, Nilai};
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(): View
    {
        // Stats untuk cards atas
        $totalSiswa = User::where('role', 'siswa')->count();
        $totalGuru = User::where('role', 'guru')->count();
        
        // Siswa yang sedang mengerjakan ujian (SesiUjian belum selesai)
        $siswaAktif = SesiUjian::whereNull('waktu_selesai')->count();
        
        // Ujian yang sedang ongoing (pastikan kolom 'status' ada)
        $currentUjian = Ujian::where('status', 'ongoing')->first();
        
        // Jadwal ujian mendatang (pakai waktu_mulai sesuai schema)
        $upcomingUjians = Ujian::where('waktu_mulai', '>', now())
            ->orderBy('waktu_mulai')
            ->limit(5)
            ->get();
        
        // Leaderboard (top 6) — pakai skor_total sesuai schema
        $leaderboard = Nilai::with('siswa')
            ->select('siswa_id', DB::raw('MAX(skor_total) as best_score'))
            ->groupBy('siswa_id')
            ->orderByDesc('best_score')
            ->limit(6)
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->siswa->name ?? 'Unknown',
                    'role' => 'Siswa',
                    'score' => (int) $item->best_score,
                ];
            });

        // Recent activities (placeholder)
        $recentActivities = [
            ['time' => '10:00', 'user' => 'Guru 1', 'action' => 'Menambahkan soal reading'],
            ['time' => '09:30', 'user' => 'Admin', 'action' => 'Membuat ujian baru'],
            ['time' => '09:00', 'user' => 'Siswa 1', 'action' => 'Menyelesaikan ujian'],
        ];

        return view('admin.dashboard', compact(
            'totalSiswa',
            'totalGuru',
            'siswaAktif',
            'currentUjian',
            'upcomingUjians',
            'leaderboard',
            'recentActivities'
        ));
    }
}
