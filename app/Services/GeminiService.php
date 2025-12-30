<?php

namespace App\Services;

use GuzzleHttp\Client;

class GeminiService
{
    protected $client;
    protected $apiKey;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'https://generativelanguage.googleapis.com/',
            'timeout' => 30,
        ]);
        $this->apiKey = env('GEMINI_API_KEY'); // ambil dari .env
    }

    public function generate(string $prompt): string
    {
        $url = "v1beta/models/gemini-pro:generateContent?key={$this->apiKey}";

        $payload = [
            "input" => [
                "text" => $prompt
            ],
            // jika butuh opsi lain (temperature, max_output_tokens dll) tambahkan sesuai kebutuhan
        ];

        $response = $this->client->post($url, [
            'json' => $payload
        ]);

        $json = json_decode($response->getBody()->getContents(), true);

        // struktur response mungkin beda tergantung versi API — ini contoh defensif
        if (isset($json['candidates'][0]['content']['parts'][0]['text'])) {
            return $json['candidates'][0]['content']['parts'][0]['text'];
        }

        // fallback: dump whole response untuk debug
        return json_encode($json, JSON_PRETTY_PRINT);
    }
}