<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\TipeSoal;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Soal extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Nama tabel
     */
    protected $table = 'soal';
    /**
     * Mass assignable attributes
     */
    protected $fillable = [
        'ujian_section_id',
        'nomor_urut',
        'pertanyaan',
        'tipe_soal',

        'opsi_jawaban',
        'jawaban_benar',

        'bobot_nilai',
        'min_kata',
        'max_kata',
        'audio_path',
        'audio_duration',
        'passage',
    ];


        protected function casts(): array 
        {
            return [
             'opsi_jawaban' => 'array',
            ];
        }

    /**
     * Relasi ke jawaban siswa (1:many)
     */
    public function jawaban(): HasMany
    {
        return $this->hasMany(Jawaban::class);
    }

    /**
     * Scope: Filter berdasarkan tipe soal
     */
    public function scopeByTipe($query, TipeSoal $tipe)
    {
        return $query->where('tipe_soal', $tipe->value);
    }

    /**
     * Scope: Urutkan berdasarkan nomor urut
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('nomor_urut');
    }

    /**
     * : Cek apakah soal adalah pilihan ganda
     */
    public function isPilihanGanda(): bool
    {
        return in_array($this->tipe_soal, ['listening', 'reading'], true);
    }


    /**
     * : Cek apakah soal adalah essay/writing
     */
    public function isEssay(): bool
    {
        return $this->tipe_soal === 'writing';
    }


    /**
     * : Cek apakah soal punya audio
     */
    public function hasAudio(): bool
    {
        return !empty($this->audio_path);
    }

    /**
     * : Ambil URL audio lengkap
     */
    public function getAudioUrlAttribute(): ?string
    {
        if (!$this->audio_path) {
            return null;
        }

        return asset('storage/' . $this->audio_path);
    }

    /**
     * : Format durasi audio ke menit:detik
     */
    public function getAudioDurationFormattedAttribute(): ?string
    {
        if (!$this->audio_duration) {
            return null;
        }

        $minutes = floor($this->audio_duration / 60);
        $seconds = $this->audio_duration % 60;

        return sprintf('%02d:%02d', $minutes, $seconds);
    }

    /**
     * : Hitung tingkat kesulitan soal (berdasarkan jawaban benar siswa)
     */
    public function getTingkatKesulitanAttribute(): string
    {
        $totalJawaban = $this->jawaban()->count();
        
        if ($totalJawaban === 0) {
            return 'Belum Ada Data';
        }

        $jawabanBenar = $this->jawaban()->where('is_benar', true)->count();
        $persentase = ($jawabanBenar / $totalJawaban) * 100;

        return match(true) {
            $persentase >= 80 => 'Mudah',
            $persentase >= 50 => 'Sedang',
            default => 'Sulit',
        };
    }

    /**
 * Relasi ke ujian section (many:1)
 */
public function ujianSection(): BelongsTo
{
    return $this->belongsTo(UjianSection::class);
}

}