<?php

declare(strict_types=1);

return [
    /**
     * Konfigurasi Section TOEFL ITP (Standar)
     * Sumber: Struktur Tes TOEFL ITP Resmi
     */
    'sections' => [
        'listening' => [
            'label' => 'Listening',
            'durasi_menit' => 35,
            'jumlah_soal' => 50,
            'bobot_nilai' => 1.0,
            'icon' => 'headphones',
            'deskripsi' => 'Bagian 1: Listening mengukur kemampuan memahami bahasa Inggris sebagaimana diucapkan di Amerika Utara.',
            'instruksi' => 'Dalam bagian ini, kamu akan mendengarkan percakapan dan monolog pendek. Jawab pertanyaan berdasarkan apa yang kamu dengar.',
            'skor_range' => [
                'min' => 31,
                'max' => 68,
            ],
        ],
        'structure' => [
            'label' => 'Written',
            'durasi_menit' => 25,
            'jumlah_soal' => 40,
            'bobot_nilai' => 1.0,
            'icon' => 'file-text',
            'deskripsi' => 'Bagian 2: Written mengukur pengenalan terhadap poin-poin struktur dan tata bahasa tertentu.',
            'instruksi' => 'Bagian ini terdiri dari 2 bagian: (1) Lengkapi kalimat dengan tata bahasa yang benar, (2) Identifikasi kesalahan tata bahasa.',
            'skor_range' => [
                'min' => 31,
                'max' => 68,
            ],
        ],
        'reading' => [
            'label' => 'Reading',
            'durasi_menit' => 55,
            'jumlah_soal' => 50,
            'bobot_nilai' => 1.0,
            'icon' => 'book-open',
            'deskripsi' => 'Bagian 3: Reading mengukur kemampuan membaca dan memahami teks akademik.',
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
     * Rentang skor TOEFL ITP
     */
    'skor_total' => [
        'min' => 310,
        'max' => 677,
    ],

    /**
     * Predikat berdasarkan skor
     */
    'predikat' => [
        'excellent' => ['min' => 600, 'label' => 'Sangat Baik', 'color' => 'green'],
        'good' => ['min' => 500, 'label' => 'Baik', 'color' => 'blue'],
        'fair' => ['min' => 400, 'label' => 'Cukup', 'color' => 'yellow'],
        'poor' => ['min' => 0, 'label' => 'Kurang', 'color' => 'red'],
    ],

    /**
     * Passing score default (bisa di-override per ujian)
     */
    'passing_score_default' => 500,

    /**
     * Maksimal pergantian tab sebelum diskualifikasi
     */
    'max_tab_switch' => 3,

    /**
     * Maksimal sesi bersamaan per ujian
     */
    'max_concurrent_sessions' => 3000,
];
