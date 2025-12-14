<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Jawaban extends Model
{
    use HasFactory;

    /**
     * Nama tabel
     */
    protected $table = 'jawaban';

    /**
     * Mass assignable attributes
     */
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
    ];

    /**
     * Cast attributes
     */
    protected function casts(): array
    {
        return [
            'is_benar' => 'boolean',
            'skor' => 'decimal:2',
            'ai_score' => 'decimal:2',
            'ai_feedback' => 'array', // JSON ke array
            'is_reviewed_by_teacher' => 'boolean',
            'teacher_score' => 'decimal:2',
            'waktu_jawab' => 'datetime',
            'jumlah_kata' => 'integer',
            'is_locked' => 'boolean',
        ];
    }

    /**
     * Relasi ke sesi ujian (many:1)
     */
    public function sesiUjian(): BelongsTo
    {
        return $this->belongsTo(SesiUjian::class);
    }

    /**
     * Relasi ke soal (many:1)
     */
    public function soal(): BelongsTo
    {
        return $this->belongsTo(Soal::class);
    }

    /**
     * Scope: Filter jawaban yang benar
     */
    public function scopeBenar($query)
    {
        return $query->where('is_benar', true);
    }

    /**
     * Scope: Filter jawaban yang salah
     */
    public function scopeSalah($query)
    {
        return $query->where('is_benar', false);
    }

    /**
     * Scope: Filter jawaban essay yang belum direview guru
     */
    public function scopeBelumDireview($query)
    {
        return $query->whereNotNull('ai_score')
                     ->where('is_reviewed_by_teacher', false);
    }

    /**
     * : Cek jawaban pilihan ganda otomatis
     */
    public function cekJawabanPilihanGanda(): void
    {
        if ($this->soal->isPilihanGanda()) {
            $isBenar = strtoupper($this->jawaban_pilihan) === strtoupper($this->soal->jawaban_benar);
            
            $this->update([
                'is_benar' => $isBenar,
                'skor' => $isBenar ? $this->soal->bobot_nilai : 0,
            ]);
        }
    }

    /**
     * : Set skor dari AI grading
     */
    public function setSkorAI(float $score, array $feedback): void
    {
        $this->update([
            'ai_score' => $score,
            'ai_feedback' => $feedback,
            'skor' => $score, // Default gunakan skor AI dulu
        ]);
    }

    /**
     * : Override skor dengan penilaian manual guru
     */
    public function setSkorGuru(float $score, string $feedback): void
    {
        $this->update([
            'teacher_score' => $score,
            'teacher_feedback' => $feedback,
            'skor' => $score, // Override skor AI dengan skor guru
            'is_reviewed_by_teacher' => true,
        ]);
    }

    /**
     * : Hitung jumlah kata dari essay
     */
    public function hitungJumlahKata(): void
    {
        if ($this->jawaban_essay) {
            $jumlah = str_word_count(strip_tags($this->jawaban_essay));
            $this->update(['jumlah_kata' => $jumlah]);
        }
    }

    /**
     * : Validasi jumlah kata essay (min/max)
     */
    public function isJumlahKataValid(): bool
    {
        if (!$this->soal->min_kata && !$this->soal->max_kata) {
            return true; // Tidak ada batasan
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