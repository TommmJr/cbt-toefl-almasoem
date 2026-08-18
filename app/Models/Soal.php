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

    protected $table = 'soal';

    // PENTING: Ganti fillable jadi guarded biar kolom virtual (pilihan_a dll) bisa lewat
    protected $guarded = ['id'];

    // Biar 'pilihan_a', 'pilihan_b' dst muncul pas data diambil (JSON/Array)
    protected $appends = ['pilihan_a', 'pilihan_b', 'pilihan_c', 'pilihan_d', 'pilihan_e', 'kunci_jawaban', 'bobot'];

    protected function casts(): array 
    {
        return [
            'opsi_jawaban' => 'array',
        ];
    }

    /* |======
    |  MUTATORS (Simpan Data: Controller -> Database)
    |  Ini "jembatan" biar inputan 'pilihan_a' masuk ke JSON 'opsi_jawaban'
    |====== */

    // 1. Tangkap 'pilihan_a' -> Masukkan ke array opsi_jawaban['a']
    public function setPilihanAAttribute($value) { $this->setOpsi('a', $value); }
    public function setPilihanBAttribute($value) { $this->setOpsi('b', $value); }
    public function setPilihanCAttribute($value) { $this->setOpsi('c', $value); }
    public function setPilihanDAttribute($value) { $this->setOpsi('d', $value); }
    public function setPilihanEAttribute($value) { $this->setOpsi('e', $value); }

    // 2. Tangkap 'kunci_jawaban' -> Simpan ke kolom 'jawaban_benar'
    public function setKunciJawabanAttribute($value)
    {
        $this->attributes['jawaban_benar'] = $value;
    }

    // 3. Tangkap 'bobot' -> Simpan ke kolom 'bobot_nilai'
    public function setBobotAttribute($value)
    {
        $this->attributes['bobot_nilai'] = $value;
    }

    // Helper private buat update JSON Opsi
    private function setOpsi($key, $value)
    {
        // Ambil data opsi yang ada (kalau belum ada, inisialisasi array)
        $opsi = isset($this->attributes['opsi_jawaban']) 
            ? json_decode($this->attributes['opsi_jawaban'], true) 
            : [];
            
        if ($value) {
            $opsi[$key] = $value;
        } else {
            unset($opsi[$key]);
        }

        $this->attributes['opsi_jawaban'] = json_encode($opsi);
    }

    /* |======
    |  ACCESSORS (Ambil Data: Database -> View)
    |====== */

    public function getPilihanAAttribute() { return $this->opsi_jawaban['a'] ?? null; }
    public function getPilihanBAttribute() { return $this->opsi_jawaban['b'] ?? null; }
    public function getPilihanCAttribute() { return $this->opsi_jawaban['c'] ?? null; }
    public function getPilihanDAttribute() { return $this->opsi_jawaban['d'] ?? null; }
    public function getPilihanEAttribute() { return $this->opsi_jawaban['e'] ?? null; }

    // Mapping balik: jawaban_benar -> kunci_jawaban
    public function getKunciJawabanAttribute()
    {
        return $this->attributes['jawaban_benar'] ?? null;
    }

    // Mapping balik: bobot_nilai -> bobot
    public function getBobotAttribute()
    {
        return $this->attributes['bobot_nilai'] ?? null;
    }

    /* |======
    |  RELASI & HELPER
    |====== */

    public function jawaban(): HasMany
    {
        return $this->hasMany(Jawaban::class);
    }

    public function ujianSection(): BelongsTo
    {
        // Pastikan nama kolom foreign key bener: ujian_section_id
        return $this->belongsTo(UjianSection::class, 'ujian_section_id');
    }

    public function scopeByTipe($query, TipeSoal $tipe)
    {
        return $query->where('tipe_soal', $tipe->value);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('nomor_urut');
    }

    public function isPilihanGanda(): bool
    {
        return in_array($this->tipe_soal, ['listening', 'reading', 'structure', 'pilihan_ganda'], true);
    }

    public function isEssay(): bool
    {
        return $this->tipe_soal === 'writing';
    }

    public function hasAudio(): bool
    {
        return !empty($this->audio_path);
    }

    public function getAudioUrlAttribute(): ?string
    {
        if (!$this->audio_path) return null;
        return asset('storage/' . $this->audio_path);
    }

    public function getAudioDurationFormattedAttribute(): ?string
    {
        if (!$this->audio_duration) return null;
        $minutes = floor($this->audio_duration / 60);
        $seconds = $this->audio_duration % 60;
        return sprintf('%02d:%02d', $minutes, $seconds);
    }

    public function getTingkatKesulitanAttribute(): string
    {
        $totalJawaban = $this->jawaban()->count();
        if ($totalJawaban === 0) return 'Belum Ada Data';

        $jawabanBenar = $this->jawaban()->where('is_benar', true)->count();
        $persentase = ($jawabanBenar / $totalJawaban) * 100;

        return match(true) {
            $persentase >= 80 => 'Mudah',
            $persentase >= 50 => 'Sedang',
            default => 'Sulit',
        };
    }
}