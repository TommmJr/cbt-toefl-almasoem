<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NilaiSection extends Model
{
    protected $table = 'nilai_sections';

    protected $fillable = [
        'sesi_ujian_id',
        'ujian_section_id',
        'total_soal',
        'total_pg',
        'benar',
        'salah',
    ];

    public function sesiUjian(): BelongsTo
    {
        return $this->belongsTo(SesiUjian::class);
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(UjianSection::class, 'ujian_section_id');
    }
}
