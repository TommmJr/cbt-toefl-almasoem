<?php

declare(strict_types=1);

return [
    /**
     * Konfigurasi Section TOEFL ITP (Standard)
     * Source: Official TOEFL ITP Test Structure
     */
    'sections' => [
        'listening' => [
            'label' => 'Listening Comprehension',
            'durasi_menit' => 35,
            'jumlah_soal' => 50,
            'bobot_nilai' => 1.0,
            'icon' => 'headphones',
            'deskripsi' => 'Section 1: Listening Comprehension measures the ability to understand English as it is spoken in North America.',
            'instruksi' => 'Dalam section ini, kamu akan mendengarkan percakapan dan monolog pendek. Jawab pertanyaan berdasarkan apa yang kamu dengar.',
            'skor_range' => [
                'min' => 31,
                'max' => 68,
            ],
        ],
        'structure' => [
            'label' => 'Structure and Written Expression',
            'durasi_menit' => 25,
            'jumlah_soal' => 40,
            'bobot_nilai' => 1.0,
            'icon' => 'file-text',
            'deskripsi' => 'Section 2: Structure and Written Expression measures recognition of selected structural and grammatical points.',
            'instruksi' => 'Section ini terdiri dari 2 bagian: (1) Lengkapi kalimat dengan grammar yang benar, (2) Identifikasi kesalahan grammar.',
            'skor_range' => [
                'min' => 31,
                'max' => 68,
            ],
        ],
        'reading' => [
            'label' => 'Reading Comprehension',
            'durasi_menit' => 55,
            'jumlah_soal' => 50,
            'bobot_nilai' => 1.0,
            'icon' => 'book-open',
            'deskripsi' => 'Section 3: Reading Comprehension measures the ability to read and understand academic texts.',
            'instruksi' => 'Baca setiap passage dengan seksama dan jawab pertanyaan berdasarkan informasi yang tersedia.',
            'skor_range' => [
                'min' => 31,
                'max' => 67,
            ],
        ],
    ],

    /**
     * Total durasi ujian (dalam menit)
     */
    'total_durasi' => 115, // 35 + 25 + 55

    /**
     * Skor TOEFL ITP range
     */
    'skor_total' => [
        'min' => 310,
        'max' => 677,
    ],

    /**
     * Predikat berdasarkan skor
     */
    'predikat' => [
        'excellent' => ['min' => 600, 'label' => 'Excellent', 'color' => 'green'],
        'good' => ['min' => 500, 'label' => 'Good', 'color' => 'blue'],
        'fair' => ['min' => 400, 'label' => 'Fair', 'color' => 'yellow'],
        'poor' => ['min' => 0, 'label' => 'Poor', 'color' => 'red'],
    ],

    /**
     * Passing score default (bisa di-override per ujian)
     */
    'passing_score_default' => 500,

    /**
     * Maksimal tab switch sebelum diskualifikasi
     */
    'max_tab_switch' => 3,

    /**
     * Maksimal concurrent sessions per ujian
     */
    'max_concurrent_sessions' => 3000,
];