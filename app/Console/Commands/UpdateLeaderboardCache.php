<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class UpdateLeaderboardCache extends Command
{
    /**
     * Signature command untuk CLI
     * Usage: php artisan leaderboard:update
     */
    protected $signature = 'leaderboard:update 
                            {--force : Paksa update meskipun cache masih valid}';

    protected $description = 'Update cache leaderboard untuk landing page';

    /**
     * Execute command
     * Bisa dijadwalkan di Kernel.php atau dipanggil manual setelah ujian
     */
    public function handle(): int
    {
        $this->info(' Updating leaderboard cache...');

        try {
            // Hitung best score per siswa dengan single query
            $leaderboard = DB::table('nilai')
                ->select('siswa_id', DB::raw('MAX(skor_total) as best_score'))
                ->groupBy('siswa_id')
                ->orderByDesc('best_score')
                ->limit(6)
                ->get()
                ->map(function ($item) {
                    // Ambil data siswa (bisa dioptimalkan dengan join)
                    $siswa = DB::table('siswa')
                        ->where('id', $item->siswa_id)
                        ->whereNull('deleted_at')
                        // === PERBAIKAN DI SINI ===
                        // Ganti 'nama' jadi 'nama_lengkap' sesuai DB lu
                        ->first(['id', 'nama_lengkap', 'kelas']); 

                    return [
                        'siswa_id' => $item->siswa_id,
                        // === PERBAIKAN DI SINI JUGA ===
                        // Ambil property nama_lengkap, tapi key-nya tetep 'nama' biar View gak perlu diubah
                        'nama' => $siswa->nama_lengkap ?? 'Unknown', 
                        'kelas' => $siswa->kelas ?? '-',
                        'best_score' => $item->best_score,
                    ];
                });

            // Simpan ke cache dengan TTL 1 jam
            Cache::put('landing_leaderboard', $leaderboard, 3600);

            $this->info(' Leaderboard cache updated successfully!');
            $this->table(
                ['Rank', 'Nama', 'Kelas', 'Best Score'],
                $leaderboard->map(fn($item, $idx) => [
                    $idx + 1,
                    $item['nama'],
                    $item['kelas'],
                    $item['best_score'],
                ])
            );

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error(' Error: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}