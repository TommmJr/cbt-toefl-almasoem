<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manajemen Ujian</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Poppins', sans-serif; }</style>
</head>
<body class="bg-gray-100 text-slate-800">

    <div class="flex min-h-screen">
        {{-- Sidebar --}}
        <x-dashboard-sidebar role="guru" active="ujian" />

        <main class="flex-1 ml-20 p-8">
            {{-- Header --}}
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold">Daftar Ujian Anda</h1>
                <a href="{{ route('guru.ujian.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition">
                    <i data-lucide="plus"></i> Tambah Ujian
                </a>
            </div>

            {{-- Alert Success --}}
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Table --}}
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <table class="w-full text-sm text-left text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                        <tr>
                            <th class="px-6 py-3">Judul</th>
                            <th class="px-6 py-3">Kode</th>
                            <th class="px-6 py-3">Waktu</th>
                            <th class="px-6 py-3">Durasi</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($ujian as $item)
                            <tr class="bg-white hover:bg-gray-50 transition">
                                <td class="px-6 py-4 font-medium text-gray-900">
                                    <a href="{{ route('guru.ujian.show', $item->id) }}" class="hover:text-blue-600 hover:underline">
                                        {{ $item->judul }}
                                    </a>
                                </td>
                                <td class="px-6 py-4 font-mono">
                                    <span class="bg-gray-100 px-2 py-1 rounded text-gray-600 border border-gray-200">{{ $item->kode_ujian }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    {{ $item->waktu_mulai->format('d M Y, H:i') }}
                                </td>
                                <td class="px-6 py-4">{{ $item->durasi_menit }} Menit</td>
                                <td class="px-6 py-4">
                                    @if($item->is_published)
                                        <span class="bg-green-100 text-green-800 text-xs px-2.5 py-0.5 rounded-full font-medium">Published</span>
                                    @else
                                        <span class="bg-gray-100 text-gray-800 text-xs px-2.5 py-0.5 rounded-full font-medium">Draft</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right space-x-2">
                                    <a href="{{ route('guru.ujian.show', $item->id) }}"
                                    class="text-blue-600 hover:text-blue-900 font-medium">
                                        Kelola
                                    </a>

                                    @if($item->started_at === null)
                                        <form action="{{ route('guru.ujian.start', $item->id) }}"
                                            method="POST"
                                            class="inline">
                                            @csrf
                                            @method('PUT')
                                            <button
                                                class="px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white rounded text-xs font-semibold">
                                                START
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-green-600 text-xs font-bold">
                                            SUDAH DIMULAI
                                        </span>
                                    @endif
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-400 bg-gray-50">
                                    <div class="flex flex-col items-center justify-center">
                                        <i data-lucide="clipboard-list" size="40" class="mb-2 opacity-50"></i>
                                        <p>Belum ada ujian. Klik tombol Tambah Ujian di atas!</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            {{-- Pagination --}}
            <div class="mt-4">
                {{ $ujian->links() }}
            </div>
        </main>
    </div>

    <script>lucide.createIcons();</script>
</body>
</html>