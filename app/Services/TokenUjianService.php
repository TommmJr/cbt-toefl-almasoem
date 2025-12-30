<?php

namespace App\Services;

use App\Models\{TokenUjian, Siswa};
use Illuminate\Support\Facades\Cache;

class TokenUjianService
{
    /**
     * Validasi token (cache-friendly, TANPA cek pemakaian siswa).
     * Cek "pernah pakai" & resume SESI dilakukan di gunakanToken().
     */
    public function validasiTokenDenganCache(string $kodeToken, Siswa $siswa): TokenUjian
    {
        // Cache hanya berdasarkan KODE TOKEN
        // Bukan per siswa, biar resume gak ke-block
        $cacheKey = "token_valid:{$kodeToken}";

        $token = Cache::remember(
            $cacheKey,
            now()->addMinutes(5),
            function () use ($kodeToken) {
                return TokenUjian::where('kode_token', $kodeToken)
                    ->with('ujian')
                    ->aktifDanValid()
                    ->first();
            }
        );

        if (! $token) {
            throw new \Exception('Token tidak valid atau sudah expired');
        }

        /**
         * PENTING:
         * - JANGAN cek "sudah pernah dipakai siswa" di sini
         * - Logic itu ada di TokenUjian::gunakanToken()
         * - Biar:
         *   - input ulang token → RESUME
         *   - refresh → RESUME
         *   - sesi selesai → BARU DITOLAK
         */

        return $token;
    }
}
