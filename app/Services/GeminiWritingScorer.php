<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiWritingScorer
{
    protected $apiKey;
    protected $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key');
        $model = config('services.gemini.model', 'gemini-1.5-flash');
        $this->baseUrl = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent";
    }

public function score(array $data)
    {
        // 1. Validasi input
        $question = $data['question'] ?? 'No question provided';
        $answer = $data['answer'] ?? '';
        
        if (empty($answer)) {
            throw new \Exception("Jawaban siswa kosong.");
        }

        // 2. Prompt
        $prompt = <<<EOT
You are an expert English teacher. Grade the following student essay.

Context/Question: "{$question}"
Student Answer: "{$answer}"

Task:
1. Give a score from 0 to 60 based on Grammar, Vocabulary, and Coherence.
2. Provide specific feedback (strengths and weaknesses).
3. Suggest a corrected version of one grammatically incorrect sentence.

OUTPUT FORMAT (JSON ONLY, NO MARKDOWN):
{
    "score": (integer 0-60),
    "feedback": {
        "strengths": ["point 1", "point 2"],
        "improvements": ["point 1", "point 2"],
        "suggested_revision": "Rewrite of a specific sentence..."
    }
}
EOT;

        try {
            // 3. Tembak ke Google Gemini API 
            $response = Http::withOptions([
                'verify' => false, 
            ])->withHeaders([
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
                    'temperature' => 0.3,
                    'responseMimeType' => 'application/json'
                ]
            ]);

            if ($response->failed()) {
                throw new \Exception("Ditolak Google: " . $response->body());
            }

            // 4. Ambil isinya
            $result = $response->json();
            $textResponse = $result['candidates'][0]['content']['parts'][0]['text'];
            
            return json_decode($textResponse, true);

        } catch (\Exception $e) {
            // Tangkap error koneksi (misal internet mati)
            throw new \Exception("Koneksi Error: " . $e->getMessage());
        }
    }
}