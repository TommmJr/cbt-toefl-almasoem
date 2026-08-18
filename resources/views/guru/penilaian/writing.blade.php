<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Koreksi Writing - {{ $siswa->nama_lengkap }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; }
        .editor-content { all: revert; } 
    </style>
</head>
<body class="bg-gray-100 text-slate-800">
{{-- DEBUG ALERT --}}
@if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative m-4" role="alert">
        <strong class="font-bold">Berhasil!</strong>
        <span class="block sm:inline">{{ session('success') }}</span>
    </div>
@endif

@if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative m-4" role="alert">
        <strong class="font-bold">Error Bang!</strong>
        <span class="block sm:inline">{{ session('error') }}</span>
    </div>
@endif
    <div class="flex min-h-screen">
        {{-- Sidebar Component --}}
        <x-dashboard-sidebar role="guru" active="analisis" />

        <main class="flex-1 ml-20 p-8">
            {{-- Header --}}
            <div class="flex justify-between items-center mb-6">
                <div>
                    <a href="{{ route('guru.analisis.show', $ujian->id) }}" class="inline-flex items-center gap-2 text-gray-500 hover:text-blue-600 mb-2 transition">
                        <i data-lucide="arrow-left" size="16"></i> Kembali ke Daftar Siswa
                    </a>
                    <h1 class="text-2xl font-bold text-gray-900">Koreksi Writing</h1>
                    <p class="text-sm text-gray-500">Siswa: <span class="font-semibold text-blue-600">{{ $siswa->nama_lengkap }}</span></p>
                </div>
            </div>

            {{-- 
                TRICK: Form Khusus buat Trigger AI 
                (Ditaruh diluar form utama biar gak error nested form)
            --}}
            <form id="ai-trigger-form" action="{{ route('guru.analisis.ai_grade', [$ujian->id, $siswa->id]) }}" method="POST" class="hidden">
                @csrf
            </form>

            {{-- Form Penilaian Utama (Manual) --}}
            <form action="{{ route('guru.analisis.simpan', [$ujian->id, $siswa->id]) }}" method="POST">
                @csrf
                
                <div class="space-y-8">
                    @forelse($jawabanWriting as $index => $jawaban)
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                            {{-- Header Soal --}}
                            <div class="bg-blue-50 px-6 py-4 border-b border-blue-100 flex justify-between items-center">
                                <h3 class="font-bold text-blue-800">Question #{{ $index + 1 }}</h3>
                                <span class="text-xs bg-blue-200 text-blue-800 px-2 py-1 rounded font-mono">
                                    Max Score: {{ $jawaban->soal->max_score ?? 'N/A' }}
                                </span>
                            </div>

                            <div class="p-6 grid grid-cols-1 lg:grid-cols-2 gap-8">
                                {{-- KIRI: Soal --}}
                                <div class="space-y-4">
                                    <div class="text-xs font-bold text-gray-400 uppercase tracking-wide">Pertanyaan:</div>
                                    <div class="prose max-w-none text-gray-800 bg-gray-50 p-4 rounded-lg border border-gray-100">
                                        {!! $jawaban->soal->pertanyaan !!}
                                    </div>
                                    
                                    @if($jawaban->soal->passage)
                                        <div class="text-xs font-bold text-gray-400 uppercase tracking-wide mt-4">Bacaan Pendukung:</div>
                                        <div class="prose max-w-none text-gray-600 text-sm bg-gray-50 p-4 rounded-lg border border-gray-100 max-h-60 overflow-y-auto">
                                            {!! $jawaban->soal->passage !!}
                                        </div>
                                    @endif
                                </div>

                               {{-- KANAN: Jawaban Siswa & AI & Nilai --}}
                                <div class="space-y-4">
                                    <div class="flex justify-between items-end">
                                        <div class="text-xs font-bold text-gray-400 uppercase tracking-wide">Jawaban Siswa:</div>
                                        {{-- Tombol Trigger AI --}}
                                        <button type="button" 
                                            onclick="document.getElementById('ai-trigger-form').submit();"
                                            class="text-xs bg-purple-600 hover:bg-purple-700 text-white px-3 py-1.5 rounded-md font-bold shadow transition flex items-center gap-1">
                                            <i data-lucide="sparkles" size="14"></i> Minta AI Koreksi
                                        </button>
                                    </div>

                                    {{-- Box Jawaban Siswa --}}
                                    <div class="prose max-w-none bg-yellow-50 p-4 rounded-lg border border-yellow-100 text-gray-800 min-h-[150px]">
                                        @php
                                            $isiJawaban = $jawaban->jawaban_essay 
                                                       ?? $jawaban->jawaban_text 
                                                       ?? $jawaban->jawaban 
                                                       ?? $jawaban->esai 
                                                       ?? null;
                                        @endphp

                                        @if(!empty($isiJawaban))
                                            {!! nl2br(e($isiJawaban)) !!}
                                        @else
                                            <div class="flex flex-col gap-2">
                                                <span class="text-red-400 italic flex items-center gap-2">
                                                    <i data-lucide="x-circle" size="16"></i> Siswa terdeteksi tidak menjawab.
                                                </span>
                                            </div>
                                        @endif
                                    </div>

                                    {{-- ============= --}}
                                    {{-- 🔥 FITUR AI INTEGRATED HERE 🔥 --}}
                                    {{-- ============= --}}
                                    @if($jawaban->ai_feedback)
                                        @php
                                            // Decode JSON aman
                                            $aiData = is_string($jawaban->ai_feedback) ? json_decode($jawaban->ai_feedback, true) : $jawaban->ai_feedback;
                                            $aiScore = $aiData['score'] ?? 0;
                                        @endphp
                                        
                                        <div class="mt-4 bg-white border-2 border-purple-100 rounded-xl overflow-hidden shadow-sm relative">
                                            <div class="bg-gradient-to-r from-purple-600 to-indigo-600 px-4 py-2 text-white flex justify-between items-center">
                                                <span class="font-bold flex items-center gap-2 text-sm">
                                                    <i data-lucide="bot" size="16"></i> Analisis AI (Gemini)
                                                </span>
                                                <span class="text-xl font-extrabold">{{ $aiScore }}<span class="text-xs font-normal text-purple-200">/60</span></span>
                                            </div>
                                            
                                            <div class="p-4 text-sm text-gray-700 space-y-3">
                                                {{-- Saran Revisi --}}
                                                @if(isset($aiData['feedback']['suggested_revision']))
                                                    <div>
                                                        <strong class="text-purple-700 block text-xs mb-1">Saran Revisi Kalimat:</strong>
                                                        <div class="bg-gray-50 p-2 rounded text-gray-600 italic border-l-4 border-purple-300 text-xs">
                                                            "{{ $aiData['feedback']['suggested_revision'] }}"
                                                        </div>
                                                    </div>
                                                @endif

                                                <div class="grid grid-cols-2 gap-4">
                                                    <div>
                                                        <strong class="text-green-600 block text-xs mb-1">👍 Kelebihan:</strong>
                                                        <ul class="list-disc pl-4 text-xs space-y-1 text-gray-600">
                                                            @foreach(array_slice($aiData['feedback']['strengths'] ?? [], 0, 3) as $point)
                                                                <li>{{ $point }}</li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                    <div>
                                                        <strong class="text-red-500 block text-xs mb-1">⚠️ Perbaikan:</strong>
                                                        <ul class="list-disc pl-4 text-xs space-y-1 text-gray-600">
                                                            @foreach(array_slice($aiData['feedback']['improvements'] ?? [], 0, 3) as $point)
                                                                <li>{{ $point }}</li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                </div>

                                                {{-- Tombol Copy Nilai --}}
                                                <button type="button" 
                                                    onclick="document.getElementById('score-input-{{ $jawaban->id }}').value = '{{ $aiScore }}'; this.innerText = 'Tersalin! ✅'; setTimeout(() => this.innerText = 'Gunakan Skor AI Ini', 2000);"
                                                    class="w-full mt-2 bg-gray-100 hover:bg-purple-50 hover:text-purple-700 text-gray-600 py-2 rounded-lg text-xs font-bold border border-gray-300 transition dashed">
                                                    Gunakan Skor AI Ini
                                                </button>
                                            </div>
                                        </div>
                                    @endif

                                    {{-- Input Score Manual --}}
                                    <div class="mt-6 pt-4 border-t border-gray-100">
                                        <label class="block text-sm font-bold text-gray-700 mb-2">Nilai Akhir (Guru):</label>
                                        <div class="flex items-center gap-4">
                                            <input type="number" 
                                                   id="score-input-{{ $jawaban->id }}"
                                                   name="nilai[{{ $jawaban->soal_id }}]" 
                                                   value="{{ old('nilai.'.$jawaban->soal_id, $jawaban->teacher_score ?? $jawaban->skor ?? 0) }}" 
                                                   min="0" max="100" 
                                                   class="w-24 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-center font-bold text-lg"
                                                   required>
                                            
                                            @if($jawaban->is_reviewed_by_teacher)
                                                <span class="text-xs text-green-600 flex items-center gap-1 font-medium bg-green-50 px-2 py-1 rounded">
                                                    <i data-lucide="check-circle" size="12"></i> Saved
                                                </span>
                                            @else
                                                <span class="text-xs text-orange-500 flex items-center gap-1 font-medium bg-orange-50 px-2 py-1 rounded">
                                                    <i data-lucide="clock" size="12"></i> Belum Disimpan
                                                </span>
                                            @endif
                                        </div>
                                        <p class="text-[10px] text-gray-400 mt-1">
                                            *Nilai ini yang akan masuk ke raport siswa. Anda bisa menggunakan rekomendasi AI atau menilai sendiri.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="bg-white p-8 rounded-xl shadow text-center">
                            <img src="https://illustrations.popsy.co/gray/surr-list-is-empty.svg" alt="Empty" class="h-48 mx-auto mb-4 opacity-50">
                            <h3 class="text-xl font-bold text-gray-800">Tidak ada soal Writing</h3>
                            <p class="text-gray-500">Ujian ini mungkin tidak memiliki section writing.</p>
                        </div>
                    @endforelse
                </div>

                {{-- Sticky Footer Button --}}
                <div class="fixed bottom-0 left-20 right-0 bg-white border-t border-gray-200 p-4 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.1)] flex justify-end items-center gap-4 z-40">
                    <span class="text-sm text-gray-500 italic mr-auto pl-4">
                        *Pastikan semua soal sudah dinilai sebelum disimpan.
                    </span>
                    <a href="{{ route('guru.analisis.show', $ujian->id) }}" class="px-6 py-2.5 rounded-lg text-gray-600 font-medium hover:bg-gray-100 transition">
                        Batal
                    </a>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-2.5 rounded-lg font-bold shadow-lg shadow-blue-200 transition flex items-center gap-2">
                        <i data-lucide="save"></i> Simpan Penilaian
                    </button>
                </div>
                {{-- Spacer buat footer --}}
                <div class="h-24"></div> 
            </form>

        </main>
    </div>
    <script>
        lucide.createIcons();
    </script>
</body>
</html>