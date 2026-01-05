<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Guru - CBT TOEFL</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Poppins', sans-serif; } </style>
</head>
<body class="bg-gray-100 text-slate-800">

    <div class="flex min-h-screen">
        {{-- 1. Panggil Sidebar --}}
        <x-dashboard-sidebar role="guru" active="dashboard" />

        {{-- 2. Konten Utama --}}
        <main class="flex-1 ml-20 p-8 transition-all duration-300">
            
            {{-- Header --}}
            <header class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">Dashboard Guru</h1>
                    <p class="text-gray-500 text-sm mt-1">Selamat datang, <span class="text-[#004e92] font-semibold">{{ $guru->nama_lengkap }}</span>!</p>
                </div>
                
                {{-- Tombol Aksi Cepat (Sudah diperbaiki jadi Link) --}}
                <a href="{{ route('guru.ujian.create') }}" class="bg-[#004e92] hover:bg-[#003d73] text-white px-5 py-2.5 rounded-xl shadow-md flex items-center gap-2 transition transform active:scale-95">
                    <i data-lucide="plus-circle" size="18"></i> Buat Ujian Baru
                </a>
            </header>

            {{-- Stats Cards --}}
            <section class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                {{-- Card 1 --}}
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition">
                    <div class="flex items-center gap-4">
                        <div class="p-3 bg-blue-50 text-blue-600 rounded-xl">
                            <i data-lucide="file-text" size="24"></i>
                        </div>
                        <div>
                            <p class="text-gray-500 text-xs font-medium uppercase">Total Ujian</p>
                            <h3 class="text-2xl font-bold text-gray-800">{{ $totalUjian }}</h3>
                        </div>
                    </div>
                </div>

                {{-- Card 2 --}}
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition">
                    <div class="flex items-center gap-4">
                        <div class="p-3 bg-green-50 text-green-600 rounded-xl">
                            <i data-lucide="radio" size="24"></i>
                        </div>
                        <div>
                            <p class="text-gray-500 text-xs font-medium uppercase">Ujian Aktif</p>
                            <h3 class="text-2xl font-bold text-gray-800">{{ $ujianAktif }}</h3>
                        </div>
                    </div>
                </div>

                {{-- Card 3 --}}
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition">
                    <div class="flex items-center gap-4">
                        <div class="p-3 bg-purple-50 text-purple-600 rounded-xl">
                            <i data-lucide="users" size="24"></i>
                        </div>
                        <div>
                            <p class="text-gray-500 text-xs font-medium uppercase">Total Peserta</p>
                            <h3 class="text-2xl font-bold text-gray-800">{{ $totalPeserta }}</h3>
                        </div>
                    </div>
                </div>
            </section>

            {{-- Tabel Ujian Terbaru --}}
            <section class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="font-bold text-gray-800 flex items-center gap-2">
                        <i data-lucide="clock" size="20" class="text-gray-400"></i> Ujian Terbaru Anda
                    </h3>
                    <a href="{{ route('guru.ujian.index') }}" class="text-sm text-[#004e92] hover:underline">Lihat Semua</a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                            <tr>
                                <th class="px-6 py-3">Judul Ujian</th>
                                <th class="px-6 py-3">Kode</th>
                                <th class="px-6 py-3">Waktu Mulai</th>
                                <th class="px-6 py-3 text-center">Status</th>
                                <th class="px-6 py-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($ujianTerbaru as $ujian)
                                <tr class="bg-white hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 font-medium text-gray-900">
                                        {{ $ujian->judul }}
                                        <div class="text-xs text-gray-400 font-normal mt-0.5">
                                            {{-- Handle Enum Display --}}
                                            {{ ucfirst($ujian->tipe_ujian->value ?? $ujian->tipe_ujian) }} • {{ $ujian->durasi_menit }} Menit
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 font-mono text-xs">
                                        <span class="bg-gray-100 px-2 py-1 rounded text-gray-600">{{ $ujian->kode_ujian }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ $ujian->waktu_mulai->format('d M Y, H:i') }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if($ujian->is_published)
                                            <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full">Published</span>
                                        @else
                                            <span class="bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded-full">Draft</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        {{-- Link ke Halaman Detail/Kelola Soal --}}
                                        <a href="{{ route('guru.ujian.show', $ujian->id) }}" class="text-blue-600 hover:text-blue-900 font-medium hover:underline">
                                            Kelola
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center text-gray-400">
                                        Belum ada ujian yang dibuat. Yuk bikin sekarang!
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

        </main>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>