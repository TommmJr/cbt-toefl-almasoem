<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\StatusUjian;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class SesiUjian extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Nama tabel
     */
    protected $table = 'sesi_ujian';

    /**
     * Mass assignable attributes
     */
    protected $fillable = [
        'ujian_id',
        'siswa_id',
        'token_akses',
        'status',
        'waktu_mulai',
        'waktu_selesai',
        'sisa_waktu_detik',
        'jumlah_tab_switch',
        'jumlah_peringatan',
        'catatan_pengawas',
        'ip_address',
        'user_agent',
    ];

    /**
     * Cast attributes
     */
    protected function casts(): array
    {
        return [
            'status' => StatusUjian::class,
            'waktu_mulai' => 'datetime',
            'waktu_selesai' => 'datetime',
            'sisa_waktu_detik' => 'integer',
            'jumlah_tab_switch' => 'integer',
            'jumlah_peringatan' => 'integer',
        ];
    }

    /**
     * Boot method untuk auto-generate token
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($sesiUjian) {
            if (empty($sesiUjian->token_akses)) {
                $sesiUjian->token_akses = Str::random(100);
            }
        });
    }

    /**
     * Relasi ke ujian (many:1)
     */
    public function ujian(): BelongsTo
    {
        return $this->belongsTo(Ujian::class);
    }

    /**
     * Relasi ke siswa (many:1)
     */
    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class);
    }

    /**
     * Relasi ke jawaban (1:many)
     */
    public function jawaban(): HasMany
    {
        return $this->hasMany(Jawaban::class);
    }

    /**
     * Relasi ke nilai (1:1)
     */
    public function nilai(): HasOne
    {
        return $this->hasOne(Nilai::class);
    }

    /**
     * Scope: Filter berdasarkan status
     */
    public function scopeByStatus($query, StatusUjian $status)
    {
        return $query->where('status', $status->value);
    }

    /**
     * Scope: Filter sesi yang sedang aktif
     */
    public function scopeAktif($query)
    {
        return $query->where('status', StatusUjian::SEDANG_MENGERJAKAN->value);
    }

    /**
     * : Mulai sesi ujian
     */
    public function mulaiUjian(): void
    {
        $this->update([
            'status' => StatusUjian::SEDANG_MENGERJAKAN,
            'waktu_mulai' => now(),
            'sisa_waktu_detik' => $this->ujian->durasi_menit * 60,
        ]);
    }

    /**
     * : Selesaikan sesi ujian
     */
    public function selesaikanUjian(): void
    {
        $this->update([
            'status' => StatusUjian::SELESAI,
            'waktu_selesai' => now(),
            'sisa_waktu_detik' => 0,
        ]);
    }

    /**
     * : Diskualifikasi siswa
     */
    public function diskualifikasi(string $alasan): void
    {
        $this->update([
            'status' => StatusUjian::DISKUALIFIKASI,
            'waktu_selesai' => now(),
            'catatan_pengawas' => $alasan,
        ]);
    }

    /**
     * : Tambah hitungan tab switch
     */
    public function tambahTabSwitch(): void
    {
        $this->increment('jumlah_tab_switch');
        $this->increment('jumlah_peringatan');
    }

    /**
     * : Hitung progress pengerjaan (%)
     */
    public function getProgressAttribute(): int
    {
        $totalSoal = $this->ujian->soal()->count();
        
        if ($totalSoal === 0) {
            return 0;
        }

        $soalDijawab = $this->jawaban()->count();
        
        return (int) (($soalDijawab / $totalSoal) * 100);
    }

    /**
     * : Hitung sisa waktu dalam format menit:detik
     */
    public function getSisaWaktuFormattedAttribute(): string
    {
        if (!$this->sisa_waktu_detik) {
            return '00:00';
        }

        $minutes = floor($this->sisa_waktu_detik / 60);
        $seconds = $this->sisa_waktu_detik % 60;

        return sprintf('%02d:%02d', $minutes, $seconds);
    }
}