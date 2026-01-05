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

    /**
     * PENTING: Kita pakai guarded kosong / id saja.
     * Ini biar gak kena error "Add [kolom] to fillable property" (Error 500).
     * Jadi semua kolom selain 'id' boleh diisi otomatis.
     */
    protected $guarded = ['id'];

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

    /* ================= CORE SCORING ================= */

    /**
     * Auto nilai pilihan ganda
     * Digunakan jika penilaian dilakukan di model (opsional),
     * tapi utamanya logic ini sudah ada di Controller simpanJawaban.
     */
    public function nilaiPilihanGanda(): void
    {
        $soal = $this->soal;

        // Validasi: Kalau soal gak ada atau gak punya kunci jawaban
        if (! $soal || ! $soal->jawaban_benar) {
            $this->update([
                'is_benar' => null,
                'skor' => 0,
            ]);
            return;
        }

        // Bandingkan jawaban (Case Insensitive)
        $benar = strtoupper((string) $this->jawaban_pilihan)
              === strtoupper((string) $soal->jawaban_benar);

        // Update skor pakai 'bobot_nilai' sesuai database
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
        // Kalau soal gak ada aturan kata, anggap valid
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