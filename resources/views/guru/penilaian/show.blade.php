<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Analisis Nilai - {{ $ujian->judul }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Poppins', sans-serif; }</style>
</head>
<body class="bg-gray-100 text-slate-800">

    <div class="flex min-h-screen">
        <x-dashboard-sidebar role="guru" active="analisis" />

        <main class="flex-1 ml-20 p-8">
            <div class="mb-6">
                <a href="{{ route('guru.analisis.index') }}" class="inline-flex items-center gap-2 text-gray-500 hover:text-blue-600 mb-2 transition">
                    <i data-lucide="arrow-left" size="16"></i> Kembali
                </a>
                <h1 class="text-2xl font-bold text-gray-900">Hasil: {{ $ujian->judul }}</h1>
            </div>

            {{-- Alert Success dari simpan nilai --}}
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <table class="w-full text-sm text-left text-gray-600">
                    <thead class="bg-gray-50 uppercase text-xs font-bold text-gray-700">
                        <tr>
                            <th class="px-6 py-4">Nama Siswa</th>
                            <th class="px-6 py-4 text-center">Listening</th>
                            <th class="px-6 py-4 text-center">Structure</th>
                            <th class="px-6 py-4 text-center">Reading</th>
                            <th class="px-6 py-4 text-center">Writing</th>
                            <th class="px-6 py-4 text-center">Total</th>
                            <th class="px-6 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                       @forelse($siswas as $siswa)
                            @php 
                                $nilai = $siswa->nilai->first(); 
                                
                                // Kasih default 0 kalau data nilai belum ada (null safe)
                                $skorListening = $nilai->skor_listening ?? 0;
                                $skorStructure = $nilai->skor_structure ?? 0;
                                $skorReading   = $nilai->skor_reading ?? 0;
                                $skorWriting   = $nilai->skor_writing ?? 0;
                                $total         = $nilai->skor_total ?? 0;

                                $butuhKoreksi = ($skorWriting == 0); 
                            @endphp
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 font-medium text-gray-900">
                                    {{ $siswa->nama_lengkap }}
                                    @if(!$nilai)
                                        <span class="block text-[10px] text-red-500 italic">(Data Nilai Belum Ada)</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">{{ $skorListening }}</td>
                                <td class="px-6 py-4 text-center">{{ $skorStructure }}</td>
                                <td class="px-6 py-4 text-center">{{ $skorReading }}</td>
                                <td class="px-6 py-4 text-center">
                                    @if($butuhKoreksi)
                                        <span class="text-orange-500 font-bold text-xs flex items-center justify-center gap-1">
                                            <i data-lucide="alert-circle" size="12"></i> Belum Dinilai
                                        </span>
                                    @else
                                        <span class="text-green-600 font-bold">{{ $skorWriting }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center font-bold text-blue-700 text-lg">
                                    {{ $total }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    {{-- Tombol cuma muncul kalau nilai ada ATAU kita mau paksa koreksi --}}
                                    <a href="{{ route('guru.analisis.koreksi', [$ujian->id, $siswa->id]) }}" 
                                       class="inline-flex items-center gap-1 px-3 py-1.5 rounded-md text-xs font-bold transition
                                       {{ $butuhKoreksi ? 'bg-orange-100 text-orange-700 hover:bg-orange-200 animate-pulse' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                                        <i data-lucide="pen-tool" size="14"></i> 
                                        {{ $butuhKoreksi ? 'Koreksi Sekarang' : 'Edit Nilai' }}
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="px-6 py-8 text-center text-gray-400">Belum ada siswa yang mengerjakan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </main>
    </div>
    <script>lucide.createIcons();</script>
</body>
</html>