<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ScoreConversion; // Pastikan ini di-import

class ExamSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_id',
        'student_id',
        'start_time',
        'end_time',
        'status',      // 'ongoing', 'completed'
        'score',       // Nilai akhir
        'completed_at'
        // Tambahin kolom detail kalau ada di database abang, misal:
        // 'listening_score', 'structure_score', 'reading_score'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function answers()
    {
        return $this->hasMany(Answer::class);
    }

    /**
     * LOGIC BARU: Hitung Nilai & Tutup Ujian (Dipindah dari Controller)
     * Biar bisa dipanggil dari Dashboard kalau ada ujian basi.
     */
    public function calculateAndFinish()
    {
        // 1. Ambil semua jawaban user beserta info section-nya
        $answers = $this->answers()->with(['question.section'])->get();

        $correctCounts = [
            'listening' => 0,
            'structure' => 0,
            'reading'   => 0,
        ];

        // 2. Hitung jumlah jawaban benar per section
        foreach ($answers as $answer) {
            // Pastikan question dan section ada datanya biar gak error
            if ($answer->is_correct && $answer->question && $answer->question->section) {
                $type = $answer->question->section->section_type;
                if (isset($correctCounts[$type])) {
                    $correctCounts[$type]++;
                }
            }
        }

        // 3. Konversi ke Skor TOEFL (Pake Logic Database ScoreConversion)
        $listeningScore = $this->getConvertedScore($correctCounts['listening'], 'listening');
        $structureScore = $this->getConvertedScore($correctCounts['structure'], 'structure');
        $readingScore   = $this->getConvertedScore($correctCounts['reading'], 'reading');

        // 4. Hitung Skor Akhir (Rumus TOEFL ITP)
        $totalScore = (($listeningScore + $structureScore + $readingScore) * 10) / 3;

        // 5. Update Database
        $this->update([
            'status' => 'completed',
            'score'  => round($totalScore),
            'completed_at' => now(),
        ]);
        
        // Return data skor kalau-kalau butuh dipake langsung
        return [
            'listening' => $listeningScore,
            'structure' => $structureScore,
            'reading'   => $readingScore,
            'total'     => round($totalScore)
        ];
    }

    /**
     * Helper Private: Ambil nilai konversi dari tabel score_conversions
     */
    private function getConvertedScore($correctCount, $sectionType)
    {
        $conversion = ScoreConversion::where('section_type', $sectionType)
            ->where('correct_count', $correctCount)
            ->first();

        // Kalau gak ketemu (misal bener 0), kasih nilai minimal (biasanya 24-30 tergantung tabel, kita set default aman 30)
        return $conversion ? $conversion->converted_score : 30; 
    }
}