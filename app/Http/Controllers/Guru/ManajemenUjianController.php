<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ujian;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\UjianSection; 
use App\Models\Soal;

class ManajemenUjianController extends Controller
{
    // 1. Halaman Daftar Ujian
    public function index()
    {
        $ujian = Ujian::where('guru_id', Auth::user()->guru->id)
            ->latest()
            ->paginate(10);

        return view('guru.ujian.index', compact('ujian'));
    }

    // 2. Halaman Form Tambah Ujian
    public function create()
    {
        return view('guru.ujian.create');
    }

    // 3. Proses Simpan Ujian
    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|max:150',
            'durasi_menit' => 'required|integer|min:1',
            'waktu_mulai' => 'required|date',
            'waktu_selesai' => 'required|date|after:waktu_mulai',
        ]);

        $kodeUnik = strtoupper(Str::random(8));

        Ujian::create([
            'guru_id' => Auth::user()->guru->id,
            'kode_ujian' => $kodeUnik,
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'durasi_menit' => $request->durasi_menit,
            'waktu_mulai' => $request->waktu_mulai,
            'waktu_selesai' => $request->waktu_selesai,
            'tipe_ujian' => 'exam',
            'is_published' => false,
        ]);

        return redirect()->route('guru.ujian.index')
            ->with('success', 'Ujian berhasil dibuat! Kode: ' . $kodeUnik);
    }

    // 4. Halaman Detail Ujian
    public function show($id)
    {
        $ujian = Ujian::with(['sections.soal'])->findOrFail($id);

        if ($ujian->guru_id !== Auth::user()->guru->id) {
            abort(403, 'Akses Ditolak: Ini bukan ujian punya lu bang!');
        }

        return view('guru.ujian.show', compact('ujian'));
    }

    // 5. Simpan Section Baru
    public function storeSection(Request $request, $id)
    {
        $request->validate([
            'judul' => 'required|string|max:100',
            'durasi_menit' => 'required|integer|min:1',
            'tipe_section' => 'required',
        ]);

        $ujian = Ujian::findOrFail($id);

        if ($ujian->guru_id !== Auth::user()->guru->id) {
            abort(403);
        }

        // Hitung urutan section
        $urutan = $ujian->sections()->count() + 1;

        $ujian->sections()->create([
            'judul_section' => $request->judul, 
            'durasi_menit' => $request->durasi_menit,
            'urutan' => $urutan, 
            'tipe_section' => $request->tipe_section, 
        ]);

        return redirect()->back()->with('success', 'Section berhasil ditambahkan!');
    }

    // 6. Tampilkan Form Input Soal
    public function createSoal($sectionId)
    {
        $section = UjianSection::with('ujian')->findOrFail($sectionId);

        if ($section->ujian->guru_id !== Auth::user()->guru->id) {
            abort(403);
        }

        return view('guru.ujian.soal.create', compact('section'));
    }

    // 7. Simpan Soal ke Database (UPDATED: Support Listening & Writing)
    public function storeSoal(Request $request, $sectionId)
    {
        $section = UjianSection::findOrFail($sectionId);
        
        // Ambil tipe section yang aman (string)
        $tipeSection = $section->tipe_section instanceof \UnitEnum 
            ? $section->tipe_section->value 
            : $section->tipe_section;

        // 1. VALIDASI DINAMIS
        $rules = [
            'pertanyaan' => 'required',
            'bobot'      => 'nullable|integer|min:1',
        ];

        // Validasi khusus Pilihan Ganda (Listening, Reading, Structure)
        if ($tipeSection !== 'writing') {
            $rules['pilihan_a'] = 'required';
            $rules['pilihan_b'] = 'required';
            $rules['pilihan_c'] = 'required';
            $rules['pilihan_d'] = 'required';
            $rules['kunci_jawaban'] = 'required|in:a,b,c,d,e';
        }

        // Validasi khusus Listening (Wajib ada audio jika belum ada di section)
        // Opsional: Bisa dibuat nullable kalau 1 audio dipakai rame-rame via Section
        if ($tipeSection === 'listening') {
            $rules['audio'] = 'nullable|file|mimes:mp3,wav,ogg|max:10240'; // Max 10MB
        }

        $request->validate($rules);

        // 2. HANDLE UPLOAD AUDIO
        $audioPath = null;
        if ($request->hasFile('audio')) {
            // Simpan ke folder: storage/app/public/audio
            $audioPath = $request->file('audio')->store('audio', 'public');
        }

        // 3. HITUNG URUTAN
        $urutan = $section->soal()->count() + 1;

        // 4. SIMPAN KE DATABASE
        Soal::create([
            'ujian_section_id' => $sectionId,
            'tipe_soal' => $tipeSection,
            'nomor_urut' => $urutan,
            'pertanyaan' => $request->pertanyaan,
            
            // Kolom Khusus
            'audio_path' => $audioPath,
            'passage' => $request->passage, // Untuk Reading
            
            // Pilihan Ganda (Null jika writing)
            'pilihan_a' => $tipeSection === 'writing' ? null : $request->pilihan_a,
            'pilihan_b' => $tipeSection === 'writing' ? null : $request->pilihan_b,
            'pilihan_c' => $tipeSection === 'writing' ? null : $request->pilihan_c,
            'pilihan_d' => $tipeSection === 'writing' ? null : $request->pilihan_d,
            'pilihan_e' => $tipeSection === 'writing' ? null : $request->pilihan_e,
            'kunci_jawaban' => $tipeSection === 'writing' ? null : $request->kunci_jawaban,
            
            // Writing Settings
            'min_kata' => $request->min_kata,
            'max_kata' => $request->max_kata,
            
            'bobot_nilai' => $request->bobot ?? 1,
        ]);

        return redirect()->route('guru.ujian.show', $section->ujian_id)
            ->with('success', 'Soal berhasil ditambahkan!');
    }
    // 8. Publish Ujian
    public function publish($id)
    {
        $ujian = Ujian::findOrFail($id);
        
        // Security Check
        if ($ujian->guru_id !== Auth::user()->guru->id) {
            abort(403);
        }

        // Cek minimal ada 1 section sebelum publish
        if ($ujian->sections()->count() == 0) {
            return back()->with('error', 'Gagal publish! Ujian belum punya section.');
        }

        // Toggle status 
        $ujian->update([
            'is_published' => !$ujian->is_published
        ]);

        $status = $ujian->is_published ? 'dipublish' : 'disembunyikan (draft)';
        return back()->with('success', "Ujian berhasil $status!");
    }

    // 9. Tampilkan Form Edit Soal
    public function editSoal($id)
    {
        $soal = Soal::with('ujianSection.ujian')->findOrFail($id);

        // Security Check
        if ($soal->ujianSection->ujian->guru_id !== Auth::user()->guru->id) {
            abort(403);
        }

        return view('guru.ujian.soal.edit', compact('soal'));
    }

    // 10. Proses Update Soal
    public function updateSoal(Request $request, $id)
    {
        $request->validate([
            'pertanyaan' => 'required',
            'pilihan_a' => 'required',
            'pilihan_b' => 'required',
            'pilihan_c' => 'required',
            'pilihan_d' => 'required',
            'kunci_jawaban' => 'required|in:a,b,c,d,e',
            'bobot' => 'required|integer|min:1',
        ]);

        $soal = Soal::findOrFail($id);

        // Security Check
        if ($soal->ujianSection->ujian->guru_id !== Auth::user()->guru->id) {
            abort(403);
        }

        // Update data (Magic Mutators di Model Soal akan handle JSON-nya)
        $soal->update([
            'pertanyaan' => $request->pertanyaan,
            'pilihan_a' => $request->pilihan_a,
            'pilihan_b' => $request->pilihan_b,
            'pilihan_c' => $request->pilihan_c,
            'pilihan_d' => $request->pilihan_d,
            'pilihan_e' => $request->pilihan_e,
            'kunci_jawaban' => $request->kunci_jawaban,
            'bobot' => $request->bobot,
        ]);

        return redirect()->route('guru.ujian.show', $soal->ujianSection->ujian_id)
            ->with('success', 'Soal berhasil diperbarui!');
    }

    // 11. Hapus Soal
    public function destroySoal($id)
    {
        $soal = Soal::findOrFail($id);

        // Security Check
        if ($soal->ujianSection->ujian->guru_id !== Auth::user()->guru->id) {
            abort(403, 'Bukan soal punya lu bang!');
        }

        $soal->delete();

        return back()->with('success', 'Soal berhasil dihapus bersih!');
    }
}