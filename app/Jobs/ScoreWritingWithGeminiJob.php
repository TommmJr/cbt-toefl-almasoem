<?php

namespace App\Jobs;

use App\Models\Jawaban;
use App\Services\GeminiWritingScorer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ScoreWritingWithGeminiJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public int $jawabanId) {}

    public function handle(GeminiWritingScorer $scorer): void
    {
        $jawaban = Jawaban::with('soal')->find($this->jawabanId);
        if (!$jawaban || !$jawaban->soal) return;

        // skip kalau sudah ada skor AI atau sudah direview guru
        if ($jawaban->ai_score !== null || $jawaban->is_reviewed_by_teacher) return;

        $jawaban->hitungJumlahKata();

        $result = $scorer->score([
            'question'   => $jawaban->soal->pertanyaan,
            'passage'    => $jawaban->soal->passage,
            'answer'     => (string) $jawaban->jawaban_essay,
            'min_words'  => $jawaban->soal->min_kata,
            'max_words'  => $jawaban->soal->max_kata,
        ]);

        $jawaban->setSkorAI(
            (float) $result['score'],
            $result // simpan full JSON supaya ada breakdown+flags
        );
    }
}
