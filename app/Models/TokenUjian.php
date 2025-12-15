<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\StatusUjian;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

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
            'berlaku_dari'    => 'datetime',
            'berlaku_sampai'  => 'datetime',
            'is_active'       => 'boolean',
            'last_used_at'    => 'datetime',
        ];
    }

    /* ================= RELATIONS ================= */

    public function ujian(): BelongsTo
    {
        return $this->belongsTo(Ujian::class);
    }

    public function pembuatToken(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dibuat_oleh');
    }

    public function siswaYangMemakai(): BelongsToMany
    {
        return $this->belongsToMany(Siswa::class, 'token_ujian_usage')
            ->withPivot(['sesi_ujian_id', 'ip_address', 'digunakan_pada'])
            ->withTimestamps();
    }

    public function usageLogs(): HasMany
    {
        return $this->hasMany(TokenUjianUsage::class);
    }

    /* ================= QUERY SCOPE ================= */

    /**
     * Token aktif, masih berlaku, dan kuota belum habis
     * Dipakai di TokenUjianService (cache layer)
     */
    public function scopeAktifDanValid($query)
    {
        return $query
            ->where('is_active', true)
            ->where('berlaku_dari', '<=', now())
            ->where('berlaku_sampai', '>=', now())
            ->whereRaw('COALESCE(jumlah_terpakai, 0) < kuota_pemakaian');
    }

    /* ================= VALIDATION ================= */

    /**
     * PURE validation.
     * Tidak mengurus sesi ujian.
     * Tidak return apa pun.
     */
    public function validasiToken(Siswa $siswa): void
    {
        if (! $this->is_active) {
            throw new \Exception('Token sudah dinonaktifkan');
        }

        if (now()->lessThan($this->berlaku_dari)) {
            throw new \Exception('Token belum bisa digunakan');
        }

        if (now()->greaterThan($this->berlaku_sampai)) {
            throw new \Exception('Token sudah kadaluarsa');
        }

        $currentToken = self::where('id', $this->id)
            ->lockForUpdate()
            ->first();

        if ($currentToken->jumlah_terpakai >= $currentToken->kuota_pemakaian) {
            throw new \Exception('Kuota token sudah habis');
        }

        $sudahPakai = DB::table('token_ujian_usage')
            ->where('token_ujian_id', $this->id)
            ->where('siswa_id', $siswa->id)
            ->exists();

        if ($sudahPakai) {
            throw new \Exception('Anda sudah menggunakan token ini sebelumnya');
        }
    }

    /* ================= CORE ================= */

    /**
     * Idempotent.
     * Resume kalau ada sesi aktif.
     * Buat baru kalau tidak ada.
     */
    public function gunakanToken(
        Siswa $siswa,
        string $ipAddress,
        string $userAgent
    ): SesiUjian {
        return DB::transaction(function () use ($siswa, $ipAddress, $userAgent) {

            // 1 CEK SESI AKTIF (RESUME)
            $sesiAktif = SesiUjian::where('ujian_id', $this->ujian_id)
                ->where('siswa_id', $siswa->id)
                ->whereIn('status', [
                    StatusUjian::BELUM_MULAI,
                    StatusUjian::SEDANG_MENGERJAKAN,
                ])
                ->lockForUpdate()
                ->first();

            if ($sesiAktif) {
                return $sesiAktif;
            }

            // 2VALIDASI TOKEN (BARU DIPAKAI)
            $this->validasiToken($siswa);

            // 3 AMANKAN TOKEN
            $this->lockForUpdate()->increment('jumlah_terpakai');
            $this->update(['last_used_at' => now()]);

            // 4 BUAT SESI BARU
            $sesiUjian = SesiUjian::create([
                'ujian_id'              => $this->ujian_id,
                'siswa_id'              => $siswa->id,
                'status'                => StatusUjian::SEDANG_MENGERJAKAN,
                'current_section_index' => 0,
                'section_mulai_at'      => now(),
                'waktu_mulai'           => now(),
                'ip_address'            => $ipAddress,
                'user_agent'            => $userAgent,
            ]);

            DB::table('token_ujian_usage')->insert([
                'token_ujian_id' => $this->id,
                'siswa_id'       => $siswa->id,
                'sesi_ujian_id'  => $sesiUjian->id,
                'ip_address'     => $ipAddress,
                'user_agent'     => $userAgent,
                'digunakan_pada' => now(),
            ]);

            return $sesiUjian;
        });
    }
}
