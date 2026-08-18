<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Ujian Berlangsung - {{ $ujian->judul }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; }
        .timer-warning { color: #ef4444; animation: pulse 1s infinite; }
        @keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.5; } }
        
        /* Custom Scrollbar for Passage */
        .passage-content::-webkit-scrollbar { width: 6px; }
        .passage-content::-webkit-scrollbar-track { background: #f1f1f1; }
        .passage-content::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        .passage-content::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    </style>
</head>

<body class="bg-gray-100 font-sans flex text-slate-800 selection:bg-blue-100">

    {{-- SIDEBAR MINI --}}
    <div class="w-20 bg-[#004e92] h-screen fixed left-0 top-0 flex flex-col items-center py-6 gap-8 z-50 shadow-xl">
        <div class="text-white mb-4">
            <i data-lucide="monitor" size="28"></i>
        </div>
        
        <div class="mt-auto mb-6 text-center">
            <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center mx-auto text-white font-bold mb-2">
                {{ substr(Auth::user()->username ?? 'S', 0, 1) }}
            </div>
            <span class="text-white/50 text-[10px]">SISWA</span>
        </div>
    </div>

    {{-- MAIN CONTENT --}}
    <main class="flex-1 ml-20 p-6 min-h-screen">

        {{-- HEADER: JUDUL & TIMER --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 mb-6 sticky top-4 z-40 flex justify-between items-center">
            <div>
                <h1 class="font-bold text-lg text-gray-800">{{ $ujian->judul }}</h1>
                <p class="text-gray-500 text-sm flex items-center gap-2">
                    <span class="bg-blue-100 text-[#004e92] px-2 py-0.5 rounded text-xs font-bold uppercase">{{ $section->tipe_section }}</span>
                    <span>Section {{ $section->urutan }}</span>
                </p>
            </div>

            <div class="text-right">
                <p class="text-xs text-gray-400 uppercase tracking-wider font-semibold">Sisa Waktu</p>
                <div class="text-3xl font-mono font-bold text-gray-800" id="timer-display">
                    00:00:00
                </div>
            </div>
        </div>

        {{-- CONTENT AREA --}}
        <div class="max-w-[1600px] mx-auto pb-20">
            @php 
                $isExpired = $sisaDetik <= 0;
                // Ambil materi dari soal pertama di section ini (asumsi 1 section = 1 konteks bacaan/audio)
                // Kalau lu mau per soal beda bacaan, logicnya harus diubah dikit jadi per-card.
                $firstSoal = $section->soal->first();
                $hasMaterial = $firstSoal && ($firstSoal->audio_path || $firstSoal->passage);
            @endphp

            {{-- GRID SYSTEM: Kalau ada materi split 2 kolom, kalau gak ada full width --}}
            <div class="{{ $hasMaterial ? 'grid grid-cols-1 lg:grid-cols-2 gap-8 items-start' : 'max-w-4xl mx-auto' }}">
                
                {{-- KOLOM KIRI: MATERI (Sticky) --}}
                @if ($hasMaterial)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 lg:sticky lg:top-28 h-fit max-h-[calc(100vh-8rem)] flex flex-col">
                    <h3 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-4 border-b pb-2 flex items-center gap-2">
                        <i data-lucide="book-open" size="16"></i> Materials
                    </h3>

                    {{-- AUDIO PLAYER --}}
                    @if($firstSoal->audio_path)
                    <div class="mb-6 bg-blue-50 p-4 rounded-xl border border-blue-100">
                        <div class="flex items-center gap-3 mb-2 text-[#004e92] font-semibold">
                            <i data-lucide="headphones" size="20"></i>
                            <span>Listening Section</span>
                        </div>
                        <audio controls controlsList="nodownload" class="w-full h-10 mt-2 rounded-lg">
                            <source src="{{ asset('storage/' . $firstSoal->audio_path) }}" type="audio/mpeg">
                            Browser Anda tidak mendukung audio.
                        </audio>
                    </div>
                    @endif

                    {{-- PASSAGE / BACAAN --}}
                    @if($firstSoal->passage)
                    <div class="passage-content overflow-y-auto pr-2 text-gray-700 leading-relaxed text-lg font-serif">
                        {!! $firstSoal->passage !!}
                    </div>
                    @endif
                </div>
                @endif

                {{-- KOLOM KANAN: DAFTAR SOAL --}}
                <div class="space-y-6">
                    @forelse ($section->soal as $index => $soal)
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 transition hover:shadow-md">
                            
                            {{-- Header Soal --}}
                            <div class="flex gap-4 mb-4">
                                <div class="flex-shrink-0 w-10 h-10 bg-[#004e92] text-white rounded-lg flex items-center justify-center font-bold text-lg shadow-sm">
                                    {{ $soal->nomor_urut }}
                                </div>
                                <div class="flex-1">
                                    <div class="text-lg text-gray-800 leading-relaxed font-medium">
                                        {!! $soal->pertanyaan !!}
                                    </div>
                                </div>
                            </div>

                            {{-- INPUT JAWABAN --}}
                            <div class="ml-14">
                                {{-- PILIHAN GANDA --}}
                                @if ($soal->tipe_soal !== 'writing')
                                    <div class="space-y-3">
                                        @foreach (['A','B','C','D'] as $opsi)
                                            @php 
                                                // Decode JSON opsi (handle kalau null)
                                                $opsiLabel = json_decode($soal->opsi_jawaban)->$opsi ?? '-';
                                                $isChecked = isset($jawabanSiswa[$soal->id]) && $jawabanSiswa[$soal->id]->jawaban_pilihan === $opsi;
                                            @endphp
                                            <label class="cursor-pointer block group">
                                                <input type="radio" 
                                                       name="jawaban_{{ $soal->id }}" 
                                                       value="{{ $opsi }}" 
                                                       class="peer sr-only"
                                                       @checked($isChecked)
                                                       @disabled($isExpired)
                                                       onchange="simpanJawaban({{ $soal->id }}, '{{ $opsi }}', 'pilihan')">
                                                
                                                <div class="flex items-center p-3 bg-gray-50 border-2 border-transparent rounded-xl transition-all hover:bg-gray-100 peer-focus:ring-2 peer-focus:ring-blue-200 peer-checked:bg-blue-50 peer-checked:border-[#004e92]">
                                                    <div class="w-8 h-8 rounded-full border-2 border-gray-300 flex items-center justify-center font-bold text-gray-400 mr-4 group-hover:border-[#004e92] group-hover:text-[#004e92] peer-checked:bg-[#004e92] peer-checked:border-[#004e92] peer-checked:text-white transition">
                                                        {{ $opsi }}
                                                    </div>
                                                    <span class="text-gray-700 font-medium">{{ $opsiLabel }}</span>
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>

                                {{-- ESSAY (WRITING) --}}
                                @else
                                    <div class="relative">
                                        <textarea 
                                            class="w-full bg-gray-50 border border-gray-200 rounded-xl p-4 focus:outline-none focus:border-[#004e92] focus:ring-4 focus:ring-blue-500/10 transition font-mono text-sm leading-relaxed"
                                            rows="15"
                                            placeholder="{{ $isExpired ? 'Waktu habis...' : 'Ketik essay Anda di sini...' }}"
                                            @disabled($isExpired)
                                            oninput="handleEssayInput(this, {{ $soal->id }})"
                                        >{{ $jawabanSiswa[$soal->id]->jawaban_essay ?? '' }}</textarea>
                                        
                                        {{-- Word Count Badge --}}
                                        <div class="absolute bottom-4 right-4 bg-white/80 backdrop-blur px-3 py-1 rounded-full text-xs font-semibold text-gray-500 border shadow-sm">
                                            <span id="word-count-{{ $soal->id }}">0</span> Words
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-20 bg-white rounded-2xl border border-dashed border-gray-300">
                            <i data-lucide="file-question" class="mx-auto text-gray-300 mb-4" size="48"></i>
                            <p class="text-gray-500">Tidak ada soal di section ini.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- FOOTER NAVIGATION --}}
        <div class="fixed bottom-0 left-20 right-0 bg-white border-t border-gray-200 p-4 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)] flex justify-between items-center z-50">
            <div class="text-sm text-gray-500 hidden sm:block">
                Pastikan semua jawaban terisi sebelum lanjut.
            </div>

            <div class="flex gap-4 ml-auto">
                {{-- Tombol Previous (Opsional) --}}
                {{-- <button class="px-6 py-3 bg-gray-100 text-gray-600 font-bold rounded-xl hover:bg-gray-200 transition">Kembali</button> --}}

                @if (! $isExpired)
                    <button type="button"
                            id="btn-submit-manual"
                            data-submit-url="{{ route('siswa.ujian.submitSection') }}"
                            data-sesi-id="{{ $sesi->id }}"
                            onclick="autoSubmitSection()"
                            class="flex items-center gap-2 bg-[#004e92] hover:bg-[#003d73] text-white font-bold py-3 px-8 rounded-xl shadow-lg hover:shadow-xl transition transform active:scale-95">
                        Lanjut Section Berikutnya
                        <i data-lucide="arrow-right" size="20"></i>
                    </button>
                @else
                    <button disabled class="bg-gray-300 text-gray-500 font-bold py-3 px-8 rounded-xl cursor-not-allowed">
                        Waktu Habis
                    </button>
                @endif
            </div>
        </div>

    </main>

    {{-- SCRIPT LOGIC --}}
    <script>
        lucide.createIcons();

        /*  STATE  */
        let sisaDetik = {{ $sisaDetik }};
        const timerDisplay = document.getElementById('timer-display');
        let debounceTimer = {};
        let sudahSubmit = false;

        /*  FORMAT TIME  */
        function formatTime(seconds) {
            const h = Math.floor(seconds / 3600);
            const m = Math.floor((seconds % 3600) / 60);
            const s = seconds % 60;
            return `${h.toString().padStart(2, '0')}:${m.toString().padStart(2, '0')}:${s.toString().padStart(2, '0')}`;
        }

        /*  AUTOSAVE JAWABAN  */
        // Gw update parameternya biar nerima tipe (pilihan/essay)
        function simpanJawaban(soalId, jawaban, tipe = 'pilihan') {
            const payload = {
                sesi_id: {{ $sesi->id }},
                soal_id: soalId
            };

            if(tipe === 'essay') {
                payload.jawaban_essay = jawaban;
            } else {
                payload.jawaban_pilihan = jawaban;
            }

            fetch("{{ route('siswa.ujian.simpanJawaban') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "Accept": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(payload)
            }).then(res => {
                if(res.ok) {
                    console.log('Saved');
                    // Bisa kasih indikator "Saved" kecil kalau mau
                }
            }).catch(err => console.error('Gagal save', err));
        }

        /*  HANDLER ESSAY (WORD COUNT + DEBOUNCE)  */
        function handleEssayInput(textarea, soalId) {
            const text = textarea.value;
            
            // 1. Update Word Count
            const words = text.trim() === '' ? 0 : text.trim().split(/\s+/).length;
            const counter = document.getElementById(`word-count-${soalId}`);
            if(counter) counter.innerText = words;

            // 2. Autosave (Debounce)
            clearTimeout(debounceTimer[soalId]);
            debounceTimer[soalId] = setTimeout(() => {
                simpanJawaban(soalId, text, 'essay');
            }, 1000); // 1 detik delay setelah ngetik
        }

        /* Init Word Count saat Load page */
        document.addEventListener("DOMContentLoaded", () => {
            document.querySelectorAll('textarea').forEach(el => {
                // Trigger input event manual biar hitungan kata muncul pas refresh
                el.dispatchEvent(new Event('input')); 
            });
        });

        /*  SUBMIT SECTION  */
        window.autoSubmitSection = function () {
            if (sudahSubmit) return;
            sudahSubmit = true;

            const btn = document.getElementById('btn-submit-manual');
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<i data-lucide="loader" class="animate-spin"></i> Memproses...';
                lucide.createIcons();
            }

            const url = btn.dataset.submitUrl;
            const sesiId = btn.dataset.sesiId;

            fetch(url, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "Accept": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ sesi_id: sesiId })
            })
            .then(res => res.json())
            .then(data => {
                if (data.redirect) {
                    window.location.href = data.redirect;
                }
            })
            .catch(err => {
                console.error(err);
                sudahSubmit = false;
                if (btn) {
                    btn.disabled = false;
                    btn.innerText = 'Coba Lagi';
                }
                alert('Gagal submit section. Cek koneksi internet.');
            });
        };

        /*  TIMER LOGIC  */
        if (timerDisplay) {
            timerDisplay.innerText = formatTime(sisaDetik);

            const interval = setInterval(() => {
                sisaDetik--;

                if (sisaDetik <= 0) {
                    sisaDetik = 0;
                    clearInterval(interval);
                    timerDisplay.innerText = "00:00:00";
                    timerDisplay.classList.add('text-red-600');
                    document.querySelectorAll('input, textarea').forEach(el => el.disabled = true);
                    autoSubmitSection();
                } else {
                    timerDisplay.innerText = formatTime(sisaDetik);
                    if (sisaDetik < 300) {
                        timerDisplay.classList.add('timer-warning');
                    }
                }
            }, 1000);
        }
    </script>
</body>
</html>