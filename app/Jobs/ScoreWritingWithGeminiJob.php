<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiWritingScorer
{
    protected $apiKey;
    protected $baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent';

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key'); // Pastikan udah set di .env & config/services.php
    }

    public function score(array $data)
    {
        // 1. Validasi input dikit
        $question = $data['question'] ?? 'No question provided';
        $answer = $data['answer'] ?? '';
        
        if (empty($answer)) {
            throw new \Exception("Jawaban siswa kosong.");
        }

        // 2. Racik Prompt buat AI (Biar dia berlagak jadi Guru Bahasa Inggris)
        $prompt = <<<EOT
You are an expert English teacher. Grade the following student essay based on these criteria:
1. Grammar & Vocabulary (Accuracy and range)
2. Coherence & Cohesion (Structure and flow)
3. Task Achievement (Did they answer the prompt?)

Question: "{$question}"
Student Answer: "{$answer}"

OUTPUT FORMAT (JSON ONLY):
{
    "score": (integer 0-60),
    "feedback": {
        "strengths": ["point 1", "point 2"],
        "improvements": ["point 1", "point 2"],
        "suggested_revision": "A short rewritten version of the worst sentence..."
    }
}
Do not output markdown code blocks, just raw JSON.
EOT;

        // 3. Tembak ke Google Gemini API
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post("{$this->baseUrl}?key={$this->apiKey}", [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ],
            'generationConfig' => [
                'temperature' => 0.3, // Biar nilainya konsisten, gak halu
                'responseMimeType' => 'application/json' // Maksa balikan JSON
            ]
        ]);

        if ($response->failed()) {
            Log::error('Gemini API Error: ' . $response->body());
            throw new \Exception("Gagal menghubungi AI. Cek log.");
        }

        // 4. Ambil isinya
        $result = $response->json();
        
        try {
            $textResponse = $result['candidates'][0]['content']['parts'][0]['text'];
            return json_decode($textResponse, true);
        } catch (\Exception $e) {
            throw new \Exception("Format jawaban AI aneh, coba lagi.");
        }
    }
}