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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str; 

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

    /* ================= SCOPE ================= */

    public function scopeAktifDanValid($query)
    {
        return $query
            ->where('is_active', true)
            ->where('berlaku_dari', '<=', now())
            ->where('berlaku_sampai', '>=', now())
            ->whereRaw('COALESCE(jumlah_terpakai, 0) < kuota_pemakaian');
    }

    /* ================= HELPER (TAMBAHAN BARU) ================= */

    /**
     * Helper: Generate token unik 6 digit
     */
    public static function generateKodeToken(): string
    {
        do {
            // Bikin 6 karakter random uppercase
            $token = strtoupper(Str::random(6));
            
            // Cek di DB biar gak duplikat
        } while (self::where('kode_token', $token)->exists());

        return $token;
    }

    /* ================= VALIDASI TOKEN (HANYA UNTUK SESI BARU) ================= */

    private function validasiUntukSesiBaru(Siswa $siswa): void
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

        $token = self::where('id', $this->id)->lockForUpdate()->first();

        if ($token->jumlah_terpakai >= $token->kuota_pemakaian) {
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
     * IDPOTENT
     * - Kalau sesi masih ada → resume
     * - Kalau tidak ada → buat baru
     */
    public function gunakanToken(
        Siswa $siswa,
        string $ipAddress,
        string $userAgent
    ): SesiUjian {
        return DB::transaction(function () use ($siswa, $ipAddress, $userAgent) {

            // 1️⃣ RESUME SESI AKTIF
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

            // 2️⃣ VALIDASI TOKEN (KHUSUS SESI BARU)
            $this->validasiUntukSesiBaru($siswa);

            // 3️⃣ AMANKAN TOKEN
            $this->increment('jumlah_terpakai');
            $this->update(['last_used_at' => now()]);

            // 4️⃣ BUAT SESI BARU
            $sesi = SesiUjian::create([
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
                'sesi_ujian_id'  => $sesi->id,
                'ip_address'     => $ipAddress,
                'user_agent'     => $userAgent,
                'digunakan_pada' => now(),
            ]);

            return $sesi;
        });
    }
}