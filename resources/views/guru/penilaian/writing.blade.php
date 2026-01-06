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

    <div class="flex min-h-screen">
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

            {{-- Form Penilaian --}}
            <form action="{{ route('guru.analisis.simpan', [$ujian->id, $siswa->id]) }}" method="POST">
                @csrf
                
                <div class="space-y-8">
                    @forelse($jawabanWriting as $index => $jawaban)
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                            {{-- Header Soal --}}
                            <div class="bg-blue-50 px-6 py-4 border-b border-blue-100 flex justify-between items-center">
                                <h3 class="font-bold text-blue-800">Question #{{ $index + 1 }}</h3>
                                <span class="text-xs bg-blue-200 text-blue-800 px-2 py-1 rounded font-mono">
                                    Max Score: 100
                                </span>
                            </div>

                            <div class="p-6 grid grid-cols-1 lg:grid-cols-2 gap-8">
                                {{-- Kolom Soal --}}
                                <div class="space-y-4">
                                    <div class="text-xs font-bold text-gray-400 uppercase tracking-wide">Pertanyaan:</div>
                                    <div class="prose max-w-none text-gray-800 bg-gray-50 p-4 rounded-lg border border-gray-100">
                                        {!! $jawaban->soal->pertanyaan !!}
                                    </div>
                                </div>

                               {{-- Kolom Jawaban Siswa & Nilai --}}
                                <div class="space-y-4">
                                    <div class="text-xs font-bold text-gray-400 uppercase tracking-wide">Jawaban Siswa:</div>
                                    <div class="prose max-w-none bg-yellow-50 p-4 rounded-lg border border-yellow-100 text-gray-800 min-h-[150px]">
                                        
                                        @php
                                            // LOGIC DETEKTIF (FIXED): Masukin 'jawaban_essay' paling depan!
                                            $isiJawaban = $jawaban->jawaban_essay 
                                                       ?? $jawaban->jawaban_text 
                                                       ?? $jawaban->jawaban 
                                                       ?? $jawaban->esai 
                                                       ?? null;
                                        @endphp

                                        @if(!empty($isiJawaban))
                                            {{-- Tampilkan jawaban --}}
                                            {!! nl2br(e($isiJawaban)) !!}
                                        @else
                                            {{-- Debugging Text --}}
                                            <div class="flex flex-col gap-2">
                                                <span class="text-red-400 italic flex items-center gap-2">
                                                    <i data-lucide="x-circle" size="16"></i> Siswa terdeteksi tidak menjawab.
                                                </span>
                                                <span class="text-[10px] text-gray-400 border-t pt-2 mt-2">
                                                    Debug Data: JSON Raw <br>
                                                    {{ json_encode($jawaban->toArray()) }}
                                                </span>
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Input Score --}}
                                    <div class="mt-4 pt-4 border-t border-gray-100">
                                        <label class="block text-sm font-bold text-gray-700 mb-2">Berikan Nilai (0-100):</label>
                                        <div class="flex items-center gap-4">
                                            <input type="number" 
                                                   name="nilai[{{ $jawaban->soal_id }}]" 
                                                   value="{{ old('nilai.'.$jawaban->soal_id, $jawaban->teacher_score ?? $jawaban->skor ?? 0) }}" 
                                                   min="0" max="100" 
                                                   class="w-24 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-center font-bold text-lg"
                                                   required>
                                            
                                            @if($jawaban->is_reviewed_by_teacher)
                                                <span class="text-xs text-green-600 flex items-center gap-1 font-medium bg-green-50 px-2 py-1 rounded">
                                                    <i data-lucide="check-circle" size="12"></i> Sudah Dinilai
                                                </span>
                                            @else
                                                <span class="text-xs text-orange-500 flex items-center gap-1 font-medium bg-orange-50 px-2 py-1 rounded">
                                                    <i data-lucide="clock" size="12"></i> Menunggu Penilaian
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="bg-white p-8 rounded-xl shadow text-center">
                            <img src="https://illustrations.popsy.co/gray/surr-list-is-empty.svg" alt="Empty" class="h-48 mx-auto mb-4 opacity-50">
                            <h3 class="text-xl font-bold text-gray-800">Tidak ada soal Writing</h3>
                            <p class="text-gray-500">Ujian ini mungkin tidak memiliki section writing, atau data soal belum diset.</p>
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
    <script>lucide.createIcons();</script>
</body>
</html>