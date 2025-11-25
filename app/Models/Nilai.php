<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Nilai extends Model
{
    use HasFactory;

    /**
     * Nama tabel
     */
    protected $table = 'nilai';

    /**
     * Mass assignable attributes
     */
    protected $fillable = [
        'sesi_ujian_id',
        'siswa_id',
        'ujian_id',
        'skor_listening',
        'skor_reading',
        'skor_writing',
        'skor_total',
        'jumlah_benar',
        'jumlah_salah',
        'jumlah_kosong',
        'is_lulus',
        'predikat',
        'durasi_pengerjaan_detik',
        'tanggal_penilaian',
    ];

    /**
     * Cast attributes
     */
    protected function casts(): array
    {
        return [
            'skor_listening' => 'decimal:2',
            'skor_reading' => 'decimal:2',
            'skor_writing' => 'decimal:2',
            'skor_total' => 'integer',
            'jumlah_benar' => 'integer',
            'jumlah_salah' => 'integer',
            'jumlah_kosong' => 'integer',
            'is_lulus' => 'boolean',
            'durasi_pengerjaan_detik' => 'integer',
            'tanggal_penilaian' => 'datetime',
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
     * Relasi ke siswa (many:1)
     */
    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class);
    }

    /**
     * Relasi ke ujian (many:1)
     */
    public function ujian(): BelongsTo
    {
        return $this->belongsTo(Ujian::class);
    }

    /**
     * Scope: Filter nilai yang lulus
     */
    public function scopeLulus($query)
    {
        return $query->where('is_lulus', true);
    }

    /**
     * Scope: Filter nilai yang tidak lulus
     */
    public function scopeTidakLulus($query)
    {
        return $query->where('is_lulus', false);
    }

    /**
     * : Hitung predikat berdasarkan skor TOEFL
     * Skala TOEFL ITP: 310-677
     */
    public function hitungPredikat(): void
    {
        $predikat = match(true) {
            $this->skor_total >= 600 => 'Excellent',
            $this->skor_total >= 500 => 'Good',
            $this->skor_total >= 400 => 'Fair',
            default => 'Poor',
        };

        $this->update(['predikat' => $predikat]);
    }

    /**
     * : Cek kelulusan berdasarkan passing score
     */
    public function cekKelulusan(): void
    {
        $isLulus = $this->skor_total >= $this->ujian->passing_score;
        
        $this->update(['is_lulus' => $isLulus]);
    }

    /**
     * : Hitung skor TOEFL total dari section scores
     * Formula konversi ke skala TOEFL ITP (simplified)
     */
    public function hitungSkorTotal(): void
    {
        // Formula sederhana: rata-rata section score dikali 10
        // Sesuaikan dengan formula TOEFL ITP yang sebenarnya jika perlu
        $rataRata = ($this->skor_listening + $this->skor_reading + $this->skor_writing) / 3;
        $skorTotal = (int) round($rataRata * 10);

        // Batasi dalam range TOEFL ITP (310-677)
        $skorTotal = max(310, min(677, $skorTotal));

        $this->update(['skor_total' => $skorTotal]);
    }

    /**
     * : Format durasi pengerjaan ke jam:menit:detik
     */
    public function getDurasiPengerjaanFormattedAttribute(): string
    {
        if (!$this->durasi_pengerjaan_detik) {
            return '00:00:00';
        }

        $hours = floor($this->durasi_pengerjaan_detik / 3600);
        $minutes = floor(($this->durasi_pengerjaan_detik % 3600) / 60);
        $seconds = $this->durasi_pengerjaan_detik % 60;

        return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
    }

    /**
     * : Ambil badge color berdasarkan predikat (Tailwind classes)
     */
    public function getPredikatBadgeColorAttribute(): string
    {
        return match($this->predikat) {
            'Excellent' => 'bg-green-100 text-green-800',
            'Good' => 'bg-blue-100 text-blue-800',
            'Fair' => 'bg-yellow-100 text-yellow-800',
            'Poor' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    /**
     * : Hitung persentase jawaban benar
     */
    public function getPersentaseBenarAttribute(): float
    {
        $total = $this->jumlah_benar + $this->jumlah_salah + $this->jumlah_kosong;
        
        if ($total === 0) {
            return 0;
        }

        return round(($this->jumlah_benar / $total) * 100, 2);
    }
}