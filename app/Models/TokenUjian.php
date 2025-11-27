<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TokenUjian extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'token_ujian';

    protected $fillable = [
        'ujian_id',
        'kode_token',
        'kuota_pemakaian',
        'jumlah_terpakai',
        'berlaku_dari',
        'berlaku_sampai',
        'is_active',
        'dibuat_oleh',
        'ip_address',
        'last_used_at',
    ];

    protected function casts(): array
    {
        return [
            'kuota_pemakaian' => 'integer',
            'jumlah_terpakai' => 'integer',
            'berlaku_dari' => 'datetime',
            'berlaku_sampai' => 'datetime',
            'is_active' => 'boolean',
            'last_used_at' => 'datetime',
        ];
    }

    /**
     * Generate token unik 6 digit (RACE CONDITION SAFE)
     */
    public static function generateKodeToken(): string
    {
        // Karakter yang aman (tanpa 0, O, I, 1 untuk avoid confusion)
        $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        $maxAttempts = 10;

        for ($attempt = 0; $attempt < $maxAttempts; $attempt++) {
            $token = '';
            for ($i = 0; $i < 6; $i++) {
                $token .= $chars[random_int(0, strlen($chars) - 1)];
            }

            // Cek uniqueness dengan DB lock (CRITICAL untuk race condition)
            $exists = DB::table('token_ujian')
                ->where('kode_token', $token)
                ->lockForUpdate() // Pessimistic lock
                ->exists();

            if (!$exists) {
                return $token;
            }
        }

        throw new \RuntimeException('Gagal generate token unik setelah ' . $maxAttempts . ' percobaan');
    }

    /**
     * Relasi ke ujian
     */
    public function ujian(): BelongsTo
    {
        return $this->belongsTo(Ujian::class);
    }

    /**
     * Relasi ke user yang buat token
     */
    public function pembuatToken(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dibuat_oleh');
    }

    /**
     * Relasi ke siswa yang sudah pakai token (many-to-many via pivot)
     */
    public function siswaYangMemakai(): BelongsToMany
    {
        return $this->belongsToMany(Siswa::class, 'token_ujian_usage')
            ->withPivot(['sesi_ujian_id', 'ip_address', 'digunakan_pada'])
            ->withTimestamps();
    }

    /**
     * Relasi ke usage log
     */
    public function usageLogs(): HasMany
    {
        return $this->hasMany(TokenUjianUsage::class);
    }

    /**
     * Scope: Token yang masih aktif dan valid
     */
    public function scopeAktifDanValid($query)
    {
        return $query->where('is_active', true)
            ->where('berlaku_dari', '<=', now())
            ->where('berlaku_sampai', '>=', now())
            ->whereColumn('jumlah_terpakai', '<', 'kuota_pemakaian');
    }

    /**
     * CRITICAL: Validasi token (RACE CONDITION SAFE)
     * 
     * @throws \Exception jika token invalid
     */
    public function validasiToken(Siswa $siswa): void
    {
        // 1. Cek apakah token aktif
        if (!$this->is_active) {
            throw new \Exception('Token sudah dinonaktifkan');
        }

        // 2. Cek expired
        if (now()->lessThan($this->berlaku_dari)) {
            throw new \Exception('Token belum bisa digunakan');
        }

        if (now()->greaterThan($this->berlaku_sampai)) {
            throw new \Exception('Token sudah kadaluarsa');
        }

        // 3. Cek kuota (dengan DB lock untuk race condition)
        $currentToken = self::where('id', $this->id)
            ->lockForUpdate()
            ->first();

        if ($currentToken->jumlah_terpakai >= $currentToken->kuota_pemakaian) {
            throw new \Exception('Kuota token sudah habis');
        }

        // 4. Cek apakah siswa ini sudah pakai token ini sebelumnya
        $sudahPakai = DB::table('token_ujian_usage')
            ->where('token_ujian_id', $this->id)
            ->where('siswa_id', $siswa->id)
            ->exists();

        if ($sudahPakai) {
            throw new \Exception('Anda sudah menggunakan token ini sebelumnya');
        }

        // 5. Cek apakah siswa sudah punya sesi aktif untuk ujian ini
        $sesiAktif = SesiUjian::where('ujian_id', $this->ujian_id)
            ->where('siswa_id', $siswa->id)
            ->whereIn('status', ['belum_mulai', 'sedang_mengerjakan'])
            ->exists();

        if ($sesiAktif) {
            throw new \Exception('Anda sudah memiliki sesi ujian aktif');
        }
    }

    /**
     * Gunakan token untuk create sesi ujian (ATOMIC TRANSACTION)
     */
    public function gunakanToken(Siswa $siswa, string $ipAddress, string $userAgent): SesiUjian
    {
        return DB::transaction(function () use ($siswa, $ipAddress, $userAgent) {
            // Validasi ulang di dalam transaction
            $this->validasiToken($siswa);

            // Increment jumlah terpakai (dengan lock)
            $this->lockForUpdate()->increment('jumlah_terpakai');
            $this->update(['last_used_at' => now()]);

            // Buat sesi ujian baru
            $sesiUjian = SesiUjian::create([
                'ujian_id' => $this->ujian_id,
                'siswa_id' => $siswa->id,
                'status' => 'belum_mulai',
                'ip_address' => $ipAddress,
                'user_agent' => $userAgent,
            ]);

            // Catat penggunaan token
            DB::table('token_ujian_usage')->insert([
                'token_ujian_id' => $this->id,
                'siswa_id' => $siswa->id,
                'sesi_ujian_id' => $sesiUjian->id,
                'ip_address' => $ipAddress,
                'user_agent' => $userAgent,
                'digunakan_pada' => now(),
            ]);

            return $sesiUjian;
        });
    }

    /**
     * Helper: Cek apakah token masih bisa dipakai
     */
    public function isMasihBisaDipakai(): bool
    {
        return $this->is_active
            && now()->between($this->berlaku_dari, $this->berlaku_sampai)
            && $this->jumlah_terpakai < $this->kuota_pemakaian;
    }

    /**
     * Helper: Hitung sisa kuota
     */
    public function getSisaKuotaAttribute(): int
    {
        return max(0, $this->kuota_pemakaian - $this->jumlah_terpakai);
    }
}