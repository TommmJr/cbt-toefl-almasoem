<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Soal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Poppins', sans-serif; }</style>
</head>
<body class="bg-gray-100 text-slate-800">

    <div class="flex min-h-screen">
        <x-dashboard-sidebar role="guru" active="ujian" />

        <main class="flex-1 ml-20 p-8">
            <div class="mb-6">
                <a href="{{ route('guru.ujian.show', $soal->ujianSection->ujian_id) }}" class="inline-flex items-center gap-2 text-gray-500 hover:text-blue-600 mb-2 transition">
                    <i data-lucide="arrow-left" size="16"></i> Batal Edit
                </a>
                <h1 class="text-3xl font-bold text-gray-900">Edit Soal</h1>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-6 border-b border-gray-100 bg-yellow-50">
                    <h3 class="font-bold text-yellow-800 flex items-center gap-2">
                        <i data-lucide="edit-3" size="18"></i> Mode Edit Soal No. {{ $soal->nomor_urut }}
                    </h3>
                </div>

                <form action="{{ route('guru.ujian.soal.update', $soal->id) }}" method="POST" class="p-8 space-y-6">
                    @csrf
                    @method('PUT') 

                    {{-- Pertanyaan --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Pertanyaan</label>
                        <textarea name="pertanyaan" rows="4" class="w-full border border-gray-300 rounded-lg p-3 outline-none focus:border-yellow-500" required>{{ old('pertanyaan', $soal->pertanyaan) }}</textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @foreach(['a', 'b', 'c', 'd'] as $opsi)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Pilihan {{ strtoupper($opsi) }}</label>
                            <div class="flex">
                                <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 font-bold">{{ strtoupper($opsi) }}</span>
                                <input type="text" name="pilihan_{{ $opsi }}" value="{{ old('pilihan_'.$opsi, $soal->{'pilihan_'.$opsi}) }}" class="w-full border border-gray-300 rounded-r-lg px-3 py-2 outline-none focus:border-yellow-500" required>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Kunci Jawaban</label>
                            <select name="kunci_jawaban" class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-white outline-none focus:border-yellow-500" required>
                                @foreach(['a', 'b', 'c', 'd', 'e'] as $key)
                                    <option value="{{ $key }}" {{ (old('kunci_jawaban', $soal->kunci_jawaban) == $key) ? 'selected' : '' }}>
                                        {{ strtoupper($key) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Bobot Nilai</label>
                            <input type="number" name="bobot" value="{{ old('bobot', $soal->bobot) }}" min="1" class="w-full border border-gray-300 rounded-lg px-3 py-2 outline-none focus:border-yellow-500" required>
                        </div>
                    </div>

                    <div class="flex justify-end pt-4">
                        <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-white px-8 py-3 rounded-lg font-semibold shadow-md transition transform hover:scale-105 flex items-center gap-2">
                            <i data-lucide="save"></i> Update Soal
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>
    <script>lucide.createIcons();</script>
</body>
</html>