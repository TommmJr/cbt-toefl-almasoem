<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Ujian - {{ $sesi->ujian->judul }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; }
        .animate-fade-in { animation: fadeIn 0.5s ease-in-out; }
        .animate-scale-up { animation: scaleUp 0.5s ease-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes scaleUp { from { transform: scale(0.8); opacity: 0; } to { transform: scale(1); opacity: 1; } }
        
        /* Print Style */
        @media print {
            .sidebar, .no-print { display: none !important; }
            main { margin-left: 0 !important; padding: 0 !important; }
            .shadow-sm, .shadow-xl { box-shadow: none !important; border: 1px solid #ddd; }
        }
    </style>
</head>

<body class="bg-gray-50 font-sans flex text-slate-800">

    {{-- SIDEBAR --}}
    <div class="sidebar w-20 bg-[#004e92] h-screen fixed left-0 top-0 flex flex-col items-center py-6 gap-8 z-50 shadow-xl">
        <div class="text-white mb-4">
            <i data-lucide="award" size="28"></i>
        </div>

        <a href="{{ route('siswa.dashboard') }}?page=home" 
           class="p-3 rounded-xl cursor-pointer transition text-white/70 hover:text-white hover:bg-white/10"
           title="Dashboard">
            <i data-lucide="home" size="28"></i>
        </a>

        <a href="{{ route('siswa.ujian.index') }}" 
           class="p-3 rounded-xl cursor-pointer transition bg-white/20 shadow-lg ring-1 ring-white/30 text-white"
           title="Ujian">
            <i data-lucide="edit-3" size="28"></i>
        </a>

        {{-- USER PROFILE --}}
        <div class="mt-auto flex flex-col gap-6 mb-4">
            <div class="relative group">
                <div class="w-10 h-10 bg-purple-200 rounded-full flex items-center justify-center cursor-pointer border-2 border-transparent group-hover:border-white transition shadow-md">
                    <span class="text-purple-700 font-bold uppercase">
                        {{ substr(Auth::user()->username ?? 'S', 0, 1) }}
                    </span>
                </div>
                {{-- Menu Logout --}}
                <div class="hidden group-hover:block absolute left-14 bottom-0 bg-white shadow-xl rounded-xl p-2 border border-gray-200 w-32 z-50 animate-fade-in">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full text-left text-sm font-medium text-red-600 hover:bg-red-50 px-3 py-2 rounded-lg transition flex items-center gap-2">
                            <i data-lucide="log-out" size="14"></i> Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- MAIN CONTENT --}}
    <main class="flex-1 ml-20 p-8 min-h-screen">

        {{-- HEADER & BREADCRUMB --}}
        <div class="flex justify-between items-center mb-8 no-print">
            <div>
                <a href="{{ route('siswa.dashboard') }}" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-[#004e92] mb-2 transition">
                    <i data-lucide="arrow-left" size="16"></i> Kembali ke Dashboard
                </a>
                <h1 class="text-2xl font-bold text-gray-800">Laporan Hasil Ujian</h1>
            </div>
            
            <button onclick="window.print()" class="flex items-center gap-2 bg-white border border-gray-200 text-gray-700 px-4 py-2 rounded-xl shadow-sm hover:bg-gray-50 transition font-medium text-sm">
                <i data-lucide="printer" size="18"></i> Cetak Hasil
            </button>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 animate-fade-in">

            {{-- KOLOM KIRI: RINGKASAN SKOR --}}
            <div class="lg:col-span-1 space-y-6">
                
                {{-- HERO CARD: TOTAL SCORE --}}
                <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100 text-center relative overflow-hidden">
                    <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-blue-500 via-purple-500 to-pink-500"></div>
                    
                    <h3 class="text-gray-500 font-medium mb-4 uppercase tracking-wider text-xs">Total Skor TOEFL</h3>
                    
                    <div class="animate-scale-up inline-block">
                        <span class="text-7xl font-extrabold text-[#004e92] tracking-tighter">
                            {{ $totalSkor }}
                        </span>
                    </div>

                    {{-- Logic Predikat Sederhana --}}
                    @php
                        $predikat = match(true) {
                            $totalSkor >= 550 => ['label' => 'EXCELLENT', 'color' => 'bg-green-100 text-green-700 border-green-200'],
                            $totalSkor >= 450 => ['label' => 'GOOD', 'color' => 'bg-blue-100 text-blue-700 border-blue-200'],
                            default           => ['label' => 'FAIR', 'color' => 'bg-yellow-100 text-yellow-700 border-yellow-200'],
                        };
                    @endphp

                    <div class="mt-6 flex justify-center">
                        <span class="px-4 py-1.5 rounded-full text-xs font-bold border {{ $predikat['color'] }}">
                            {{ $predikat['label'] }}
                        </span>
                    </div>

                    <div class="mt-8 pt-6 border-t border-gray-100 grid grid-cols-2 gap-4 text-left">
                        <div>
                            <p class="text-xs text-gray-400">Nama Peserta</p>
                            <p class="font-semibold text-gray-800 truncate">{{ Auth::user()->username }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Tanggal Ujian</p>
                            <p class="font-semibold text-gray-800">{{ now()->format('d M Y') }}</p>
                        </div>
                    </div>
                </div>

                {{-- DETAIL SKOR PER SECTION --}}
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100">
                    <h3 class="font-bold text-gray-800 mb-6 flex items-center gap-2">
                        <i data-lucide="bar-chart-2" size="20" class="text-[#004e92]"></i>
                        Rincian Skor
                    </h3>

                    <div class="space-y-4">
                        @foreach ($sesi->ujian->sections as $section)
                            @php
                                // Hitung skor kasar per section (Logic Sederhana)
                                $sectionScore = 0;
                                foreach($section->soal as $soal) {
                                    $jawaban = $sesi->jawaban->firstWhere('soal_id', $soal->id);
                                    if($jawaban && $jawaban->is_benar) {
                                        $sectionScore += $soal->bobot_nilai;
                                    }
                                }
                                // Konversi dummy: Skor asli * 10 (biar keliatan gede ala TOEFL)
                                // Nanti bisa diganti logic konversi TOEFL beneran
                                $convertedScore = $sectionScore * 10; 
                                
                                $icon = match($section->tipe_section->value ?? 'default') {
                                    'listening' => 'headphones',
                                    'reading'   => 'book-open',
                                    default     => 'pen-tool'
                                };
                                $color = match($section->tipe_section->value ?? 'default') {
                                    'listening' => 'bg-blue-50 text-blue-600',
                                    'reading'   => 'bg-orange-50 text-orange-600',
                                    default     => 'bg-purple-50 text-purple-600'
                                };
                            @endphp

                            <div class="flex items-center justify-between p-3 rounded-xl hover:bg-gray-50 transition border border-transparent hover:border-gray-100">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-lg {{ $color }} flex items-center justify-center">
                                        <i data-lucide="{{ $icon }}" size="20"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-gray-700">{{ $section->judul_section }}</p>
                                        <p class="text-xs text-gray-400 capitalize">{{ $section->tipe_section->value ?? '-' }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="block font-bold text-gray-800 text-lg">{{ $convertedScore }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>

            {{-- KOLOM KANAN: ANALISIS JAWABAN --}}
            <div class="lg:col-span-2">
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                        <h3 class="font-bold text-gray-800 flex items-center gap-2">
                            <i data-lucide="list-checks" size="20" class="text-[#004e92]"></i>
                            Analisis Jawaban
                        </h3>
                        <span class="text-xs font-medium text-gray-500 bg-white px-3 py-1 rounded-full border border-gray-200 shadow-sm">
                            Total Soal: {{ $sesi->jawaban->count() }}
                        </span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="text-xs text-gray-500 uppercase bg-gray-50 border-b border-gray-100">
                                <tr>
                                    <th class="px-6 py-4 font-medium">No</th>
                                    <th class="px-6 py-4 font-medium w-1/2">Pertanyaan (Cuplikan)</th>
                                    <th class="px-6 py-4 font-medium text-center">Jawabanmu</th>
                                    <th class="px-6 py-4 font-medium text-center">Kunci</th>
                                    <th class="px-6 py-4 font-medium text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach ($sesi->ujian->sections as $section)
                                    {{-- Section Header di dalam Tabel --}}
                                    <tr class="bg-gray-50/80">
                                        <td colspan="5" class="px-6 py-2 text-xs font-bold text-gray-600 uppercase tracking-wide">
                                            {{ $section->judul_section }}
                                        </td>
                                    </tr>

                                    @foreach ($section->soal as $soal)
                                        @php
                                            $jawaban = $sesi->jawaban->firstWhere('soal_id', $soal->id);
                                            $isCorrect = $jawaban && $jawaban->is_benar;
                                            $userAns = $jawaban->jawaban_pilihan ?? $jawaban->jawaban_essay ?? '-';
                                        @endphp
                                        <tr class="hover:bg-gray-50/50 transition">
                                            <td class="px-6 py-4 font-medium text-gray-900 text-center">
                                                {{ $soal->nomor_urut }}
                                            </td>
                                            
                                            {{-- [FIXED] Pake strip_tags biar tag HTML <p> ilang --}}
                                            <td class="px-6 py-4 text-gray-600 truncate max-w-xs" title="{{ strip_tags($soal->pertanyaan) }}">
                                                {{ Str::limit(strip_tags($soal->pertanyaan), 60) }}
                                            </td>

                                            <td class="px-6 py-4 text-center font-bold {{ $isCorrect ? 'text-green-600' : 'text-red-500' }}">
                                                {{ Str::limit(strip_tags($userAns), 20) }}
                                            </td>
                                            <td class="px-6 py-4 text-center text-gray-500">
                                                {{ $soal->jawaban_benar ?? 'Essay' }}
                                            </td>
                                            <td class="px-6 py-4 text-center">
                                                @if($isCorrect)
                                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        <i data-lucide="check" size="12"></i> Benar
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                        <i data-lucide="x" size="12"></i> Salah
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>

        <div class="mt-8 text-center text-xs text-gray-400 no-print">
            &copy; {{ date('Y') }} CBT Al-Ma'soem. Hasil ini bersifat sementara untuk simulasi.
        </div>

    </main>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>