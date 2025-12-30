<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class GeminiWritingScorer
{
    public function score(array $payload): array
    {
        $apiKey = config('services.gemini.api_key');
        $model  = config('services.gemini.model', 'gemini-2.5-flash');

        if (!$apiKey) {
            throw new \RuntimeException('GEMINI_API_KEY belum di-set');
        }

        $endpoint = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";

        $schema = [
            "type" => "object",
            "additionalProperties" => false,
            "properties" => [
                "score" => ["type" => "number", "minimum" => 0, "maximum" => 60],
                "breakdown" => [
                    "type" => "object",
                    "additionalProperties" => false,
                    "properties" => [
                        "task_response" => ["type" => "number", "minimum" => 0, "maximum" => 12],
                        "coherence"     => ["type" => "number", "minimum" => 0, "maximum" => 12],
                        "grammar"       => ["type" => "number", "minimum" => 0, "maximum" => 12],
                        "vocabulary"    => ["type" => "number", "minimum" => 0, "maximum" => 12],
                        "mechanics"     => ["type" => "number", "minimum" => 0, "maximum" => 12],
                    ],
                    "required" => ["task_response","coherence","grammar","vocabulary","mechanics"]
                ],
                "feedback" => [
                    "type" => "object",
                    "additionalProperties" => false,
                    "properties" => [
                        "strengths" => ["type" => "array", "items" => ["type" => "string"]],
                        "improvements" => ["type" => "array", "items" => ["type" => "string"]],
                        "top_mistakes" => ["type" => "array", "items" => ["type" => "string"]],
                        "suggested_revision" => ["type" => "string"],
                    ],
                    "required" => ["strengths","improvements","top_mistakes","suggested_revision"]
                ],
                "flags" => [
                    "type" => "object",
                    "additionalProperties" => false,
                    "properties" => [
                        "off_topic" => ["type" => "boolean"],
                        "too_short" => ["type" => "boolean"],
                        "suspected_copy_paste" => ["type" => "boolean"],
                    ],
                    "required" => ["off_topic","too_short","suspected_copy_paste"]
                ]
            ],
            "required" => ["score","breakdown","feedback","flags"]
        ];

        // Prompt penilai (pakai rubric 5 aspek x 12 = 60)
        $prompt = $this->buildPrompt($payload);

        $reqBody = [
            "contents" => [
                [
                    "role" => "user",
                    "parts" => [
                        ["text" => $prompt]
                    ]
                ]
            ],
            "generationConfig" => [
                "temperature" => 0.2,
                "response_mime_type" => "application/json",
                "response_json_schema" => $schema,
            ],
        ];

        $resp = Http::timeout(60)
            ->retry(2, 500)
            ->post($endpoint, $reqBody);

        if (!$resp->successful()) {
            throw new \RuntimeException("Gemini error: {$resp->status()} - {$resp->body()}");
        }

        // Gemini structured output akan ngasih JSON string di text
        $text = data_get($resp->json(), 'candidates.0.content.parts.0.text');
        if (!is_string($text) || trim($text) === '') {
            throw new \RuntimeException('Response Gemini kosong / tidak valid');
        }

        $data = json_decode($text, true);
        if (!is_array($data)) {
            throw new \RuntimeException('Gagal parse JSON dari Gemini');
        }

        // Safety clamp: pastiin range skor
        $data['score'] = max(0, min(60, (float) ($data['score'] ?? 0)));

        return $data;
    }

    private function buildPrompt(array $p): string
    {
        $question = $p['question'] ?? '';
        $passage  = $p['passage'] ?? null;
        $answer   = $p['answer'] ?? '';
        $minWords = $p['min_words'] ?? null;
        $maxWords = $p['max_words'] ?? null;

        $wc = str_word_count(strip_tags($answer));
        $limitText = "Word count: {$wc}.";
        if ($minWords || $maxWords) {
            $limitText .= " Limits: min={$minWords}, max={$maxWords}.";
        }

        $passageBlock = $passage ? "\n\nPASSAGE (context):\n{$passage}" : "";

        return
"Anda adalah penilai ujian TOEFL Writing (skala 0–60).
Nilai menggunakan rubric 5 aspek (masing-masing 0–12):
1) task_response (menjawab prompt & relevansi)
2) coherence (alur, paragraf, cohesion)
3) grammar (tenses, agreement, sentence structure)
4) vocabulary (ketepatan & variasi kata)
5) mechanics (spelling, punctuation, capitalization)

Aturan:
- Beri skor yang adil, konsisten, dan bisa dijelaskan.
- Jika jawaban off-topic, terlalu pendek, atau terlihat copy-paste, nyalakan flag yang sesuai.
- Beri feedback yang actionable + revisi versi yang lebih baik (1 paragraf) di suggested_revision.
- Output HARUS sesuai schema JSON.

PROMPT:
{$question}
{$passageBlock}

STUDENT ANSWER:
{$answer}

{$limitText}
";
    }
}
