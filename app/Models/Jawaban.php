<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Jawaban extends Model
{
    use HasFactory;

    protected $table = 'jawaban';

    protected $fillable = [
        'sesi_ujian_id',
        'soal_id',
        'jawaban_pilihan',
        'jawaban_essay',
        'jumlah_kata',
        'is_benar',
        'skor',
        'ai_score',
        'ai_feedback',
        'is_reviewed_by_teacher',
        'teacher_score',
        'teacher_feedback',
        'waktu_jawab',
        'is_locked',
    ];

    protected function casts(): array
    {
        return [
            'is_benar' => 'boolean',
            'skor' => 'decimal:2',
            'ai_score' => 'decimal:2',
            'ai_feedback' => 'array',
            'is_reviewed_by_teacher' => 'boolean',
            'teacher_score' => 'decimal:2',
            'waktu_jawab' => 'datetime',
            'jumlah_kata' => 'integer',
            'is_locked' => 'boolean',
        ];
    }

    /* ================= RELATIONS ================= */

    public function sesiUjian(): BelongsTo
    {
        return $this->belongsTo(SesiUjian::class);
    }

    public function soal(): BelongsTo
    {
        return $this->belongsTo(Soal::class);
    }

    /* ================= SCOPES ================= */

    public function scopeBenar($query)
    {
        return $query->where('is_benar', true);
    }

    public function scopeSalah($query)
    {
        return $query->where('is_benar', false);
    }

    public function scopeBelumDireview($query)
    {
        return $query
            ->whereNotNull('ai_score')
            ->where('is_reviewed_by_teacher', false);
    }

    /* ================= CORE SCORING (FASE 2) ================= */

    /**
     * Auto nilai pilihan ganda
     * - PG → auto-score
     * - Essay → skip (aman)
     */
    public function nilaiPilihanGanda(): void
    {
        $soal = $this->soal;

        // Soal tidak valid / essay / tidak punya kunci
        if (! $soal || ! $soal->jawaban_benar) {
            $this->update([
                'is_benar' => null,
                'skor' => 0,
            ]);
            return;
        }

        $benar = strtoupper((string) $this->jawaban_pilihan)
              === strtoupper((string) $soal->jawaban_benar);

        $this->update([
            'is_benar' => $benar,
            'skor' => $benar ? $soal->bobot_nilai : 0,
        ]);
    }

    /* ================= AI & MANUAL SCORING ================= */

    public function setSkorAI(float $score, array $feedback): void
    {
        $this->update([
            'ai_score' => $score,
            'ai_feedback' => $feedback,
            'skor' => $score,
        ]);
    }

    public function setSkorGuru(float $score, string $feedback): void
    {
        $this->update([
            'teacher_score' => $score,
            'teacher_feedback' => $feedback,
            'skor' => $score,
            'is_reviewed_by_teacher' => true,
        ]);
    }

    /* ================= ESSAY UTIL ================= */

    public function hitungJumlahKata(): void
    {
        if ($this->jawaban_essay) {
            $jumlah = str_word_count(strip_tags($this->jawaban_essay));
            $this->update(['jumlah_kata' => $jumlah]);
        }
    }

    public function isJumlahKataValid(): bool
    {
        if (! $this->soal->min_kata && ! $this->soal->max_kata) {
            return true;
        }

        $jumlah = $this->jumlah_kata ?? 0;

        if ($this->soal->min_kata && $jumlah < $this->soal->min_kata) {
            return false;
        }

        if ($this->soal->max_kata && $jumlah > $this->soal->max_kata) {
            return false;
        }

        return true;
    }
}
