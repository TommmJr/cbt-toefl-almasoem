<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\TipeSoal;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UjianSection extends Model
{
    use HasFactory;

    protected $table = 'ujian_section';

    protected $fillable = [
        'ujian_id',
        'tipe_section',
        'urutan',
        'durasi_menit',
        'jumlah_soal_target',
        'bobot_nilai',
        'instruksi_custom',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'tipe_section' => TipeSoal::class,
            'urutan' => 'integer',
            'durasi_menit' => 'integer',
            'jumlah_soal_target' => 'integer',
            'bobot_nilai' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Relasi ke ujian
     */
    public function ujian(): BelongsTo
    {
        return $this->belongsTo(Ujian::class);
    }

    /**
     * Relasi ke soal-soal dalam section ini
     */
    public function soal(): HasMany
    {
        return $this->hasMany(Soal::class)->orderBy('nomor_urut');
    }

    /**
     * Scope: Urutkan berdasarkan urutan section
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('urutan');
    }

    /**
     * Scope: Section yang aktif
     */
    public function scopeAktif($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Helper: Ambil config default dari Enum
     */
    public function getConfigDefaultAttribute(): array
    {
        return $this->tipe_section->config();
    }

    /**
     * Helper: Ambil label section
     */
    public function getLabelAttribute(): string
    {
        return $this->tipe_section->label();
    }

    /**
     * Helper: Ambil icon section
     */
    public function getIconAttribute(): string
    {
        return $this->tipe_section->icon();
    }

    /**
     * Helper: Cek apakah jumlah soal sudah sesuai target
     */
    public function isSoalLengkap(): bool
    {
        return $this->soal()->count() >= $this->jumlah_soal_target;
    }

    /**
     * Helper: Hitung progress input soal (%)
     */
    public function getProgressSoalAttribute(): int
    {
        if ($this->jumlah_soal_target === 0) {
            return 0;
        }

        $jumlahSoal = $this->soal()->count();
        return (int) min(100, ($jumlahSoal / $this->jumlah_soal_target) * 100);
    }
}