{{-- resources/views/guru/ujian/index.blade.php --}}
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
                            <tr class="bg-white hover:bg-gray-50">
                                <td class="px-6 py-4 font-medium text-gray-900">{{ $item->judul }}</td>
                                <td class="px-6 py-4 font-mono">{{ $item->kode_ujian }}</td>
                                <td class="px-6 py-4">
                                    {{ $item->waktu_mulai->format('d M Y H:i') }}
                                </td>
                                <td class="px-6 py-4">{{ $item->durasi_menit }} Menit</td>
                                <td class="px-6 py-4">
                                    @if($item->is_published)
                                        <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full">Published</span>
                                    @else
                                        <span class="bg-gray-100 text-gray-800 text-xs px-2 py-1 rounded-full">Draft</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="#" class="text-blue-600 hover:underline mr-3">Edit</a>
                                    <a href="#" class="text-red-600 hover:underline">Hapus</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-gray-400">
                                    Belum ada ujian. Klik tombol Tambah Ujian di atas!
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