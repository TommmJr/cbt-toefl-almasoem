<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat Ujian Baru</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Poppins', sans-serif; }</style>
</head>
<body class="bg-gray-100 text-slate-800">

    <div class="flex min-h-screen">
        {{-- Sidebar --}}
        <x-dashboard-sidebar role="guru" active="ujian" />

        <main class="flex-1 ml-20 p-8 transition-all duration-300">
            
            {{-- Header --}}
            <div class="mb-6 flex items-center gap-3">
                <a href="{{ route('guru.ujian.index') }}" class="p-2 bg-white rounded-lg shadow-sm hover:bg-gray-50 text-gray-600 transition">
                    <i data-lucide="arrow-left" size="20"></i>
                </a>
                <h1 class="text-2xl font-bold text-gray-800">Buat Ujian Baru</h1>
            </div>

            {{-- Form Card --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 max-w-3xl">
                
                <form action="{{ route('guru.ujian.store') }}" method="POST">
                    @csrf

                    {{-- 1. Judul Ujian --}}
                    <div class="mb-6">
                        <label class="block mb-2 text-sm font-medium text-gray-900">Judul Ujian</label>
                        <input type="text" name="judul" value="{{ old('judul') }}" 
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" 
                            placeholder="Contoh: Penilaian Harian Bahasa Inggris Chapter 1" required>
                        @error('judul')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- 2. Deskripsi (Opsional) --}}
                    <div class="mb-6">
                        <label class="block mb-2 text-sm font-medium text-gray-900">Deskripsi (Opsional)</label>
                        <textarea name="deskripsi" rows="3" 
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" 
                            placeholder="Instruksi pengerjaan...">{{ old('deskripsi') }}</textarea>
                    </div>

                    {{-- Grid Layout untuk Waktu --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        
                        {{-- 3. Waktu Mulai --}}
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900">Waktu Mulai</label>
                            <input type="datetime-local" name="waktu_mulai" value="{{ old('waktu_mulai') }}"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
                            @error('waktu_mulai')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- 4. Waktu Selesai --}}
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900">Waktu Selesai</label>
                            <input type="datetime-local" name="waktu_selesai" value="{{ old('waktu_selesai') }}"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
                            @error('waktu_selesai')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- 5. Durasi --}}
                    <div class="mb-8">
                        <label class="block mb-2 text-sm font-medium text-gray-900">Durasi Pengerjaan (Menit)</label>
                        <input type="number" name="durasi_menit" value="{{ old('durasi_menit', 60) }}" min="1"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
                        <p class="mt-1 text-xs text-gray-500">Berapa lama siswa boleh mengerjakan soal setelah menekan tombol mulai.</p>
                        @error('durasi_menit')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Tombol Submit --}}
                    <div class="flex justify-end gap-3">
                        <a href="{{ route('guru.ujian.index') }}" class="px-5 py-2.5 text-sm font-medium text-gray-900 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 transition">
                            Batal
                        </a>
                        <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-blue-700 rounded-lg hover:bg-blue-800 transition shadow-md">
                            Simpan Ujian
                        </button>
                    </div>

                </form>
            </div>

        </main>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>