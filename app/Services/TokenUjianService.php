<?php

namespace App\Services;

use App\Models\{TokenUjian, Siswa};
use Illuminate\Support\Facades\Cache;

class TokenUjianService
{
    /**
     * Validasi token dengan Redis cache (untuk 3000 users)
     */
    public function validasiTokenDenganCache(string $kodeToken, Siswa $siswa): TokenUjian
    {
        // Cache key unik per kombinasi token + siswa
        $cacheKey = "token_validation:{$kodeToken}:{$siswa->id}";

        // Cek cache dulu (TTL 5 menit)
        $cachedResult = Cache::remember($cacheKey, 300, function () use ($kodeToken) {
            return TokenUjian::where('kode_token', $kodeToken)
                ->with('ujian')
                ->aktifDanValid()
                ->first();
        });

        if (!$cachedResult) {
            throw new \Exception('Token tidak valid atau sudah expired');
        }

        // Validasi ulang dengan DB lock (race condition protection)
        $cachedResult->validasiToken($siswa);

        return $cachedResult;
    }
}