<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use App\Enums\TipeSection;

class UjianSection extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Nama tabel (biar eksplisit, walau Laravel bisa nebak)
     */
    protected $table = 'ujian_sections';

    /**
     * Kolom yang boleh diisi mass assignment
     */
    protected $fillable = [
        'ujian_id',
        'judul_section',
        'tipe_section',
        'urutan',
        'durasi_menit',
        'instruksi',
        'audio_path',
        'passage',
        'waktu_mulai',
        'waktu_selesai',
    ];

    /**
     * Casting attribute
     */
    protected $casts = [
        'tipe_section' => TipeSection::class,
        'waktu_mulai'  => 'datetime',
        'waktu_selesai'=> 'datetime',
    ];

    /**
     * Relasi ke Ujian
     */
    public function ujian(): BelongsTo
    {
        return $this->belongsTo(Ujian::class);
    }
}
