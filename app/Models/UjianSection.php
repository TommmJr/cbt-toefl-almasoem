<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class UjianSection extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'ujian_sections';

    protected $fillable = [
    'ujian_id',
    'judul_section',
    'tipe_section',
    'urutan',
    'durasi_menit',
        ];


    public function ujian(): BelongsTo
    {
        return $this->belongsTo(Ujian::class);
    }

    public function soal(): HasMany
    {
        return $this->hasMany(Soal::class, 'ujian_section_id')
            ->orderBy('nomor_urut');
    }
}
