<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Analisis & Penilaian</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Poppins', sans-serif; }</style>
</head>
<body class="bg-gray-100 text-slate-800">

    <div class="flex min-h-screen">
        <x-dashboard-sidebar role="guru" active="analisis" />

        <main class="flex-1 ml-20 p-8">
            <h1 class="text-2xl font-bold mb-6 flex items-center gap-2">
                <i data-lucide="bar-chart-2" class="text-blue-600"></i> Analisis & Penilaian
            </h1>

            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <table class="w-full text-sm text-left text-gray-600">
                    <thead class="bg-gray-50 uppercase text-xs font-bold text-gray-700">
                        <tr>
                            <th class="px-6 py-4">Judul Ujian</th>
                            <th class="px-6 py-4">Waktu</th>
                            <th class="px-6 py-4 text-center">Peserta</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($ujian as $item)
                            <tr class="hover:bg-blue-50 transition">
                                <td class="px-6 py-4 font-medium text-gray-900">{{ $item->judul }}</td>
                                <td class="px-6 py-4">{{ $item->waktu_mulai->format('d M Y') }}</td>
                                <td class="px-6 py-4 text-center">
                                    <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full font-bold">
                                        {{ $item->total_peserta }} Siswa
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('guru.analisis.show', $item->id) }}" 
                                       class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-xs font-bold transition shadow-sm">
                                        <i data-lucide="search" size="14"></i> Analisis Nilai
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-6 py-8 text-center text-gray-400">Belum ada ujian.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $ujian->links() }}</div>
        </main>
    </div>
    <script>lucide.createIcons();</script>
</body>
</html>