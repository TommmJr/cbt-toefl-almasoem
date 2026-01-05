<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ujian;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\Section;
use App\Models\Soal;

class ManajemenUjianController extends Controller
{
    // 1. Halaman Daftar Ujian
    public function index()
    {
        // Ambil ujian milik guru yang sedang login
        $ujian = Ujian::where('guru_id', Auth::user()->guru->id)
            ->latest()
            ->paginate(10); // Kita kasih pagination biar rapi kalau datanya banyak

        return view('guru.ujian.index', compact('ujian'));
    }

    // 2. Halaman Form Tambah Ujian
    public function create()
    {
        return view('guru.ujian.create');
    }

    // 3. Proses Simpan Ujian Baru ke Database
    public function store(Request $request)
    {
        // Validasi input biar ga asal-asalan
        $request->validate([
            'judul' => 'required|max:150',
            'durasi_menit' => 'required|integer|min:1',
            'waktu_mulai' => 'required|date',
            'waktu_selesai' => 'required|date|after:waktu_mulai', // Selesai wajib setelah Mulai
        ]);

        // Bikin Kode Unik Otomatis (Misal: UJN-X7Z9)
        $kodeUnik = strtoupper(Str::random(8));

        // Simpan ke DB
        Ujian::create([
            'guru_id' => Auth::user()->guru->id,
            'kode_ujian' => $kodeUnik,
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'durasi_menit' => $request->durasi_menit,
            'waktu_mulai' => $request->waktu_mulai,
            'waktu_selesai' => $request->waktu_selesai,
            'tipe_ujian' => 'exam', // Default
            'is_published' => false, // Default draft dulu
        ]);

        return redirect()->route('guru.ujian.index')
            ->with('success', 'Ujian berhasil dibuat! Kode: ' . $kodeUnik);
    }

    // 4: Halaman Detail Ujian (Buat Manage Soal)
    public function show($id)
    {
        // 1. Ambil data ujian + section + soal
        $ujian = Ujian::with(['sections.soal'])->findOrFail($id);

        // 2. Security Check: Pastikan yang akses adalah pemilik ujian
        if ($ujian->guru_id !== Auth::user()->guru->id) {
            abort(403, 'Akses Ditolak: Ini bukan ujian punya lu bang!');
        }
        // 3. Lempar ke view detail
        return view('guru.ujian.show', compact('ujian'));
    }

    // 5. Simpan Section Baru
    public function storeSection(Request $request, $id)
    {
        $request->validate([
            'judul' => 'required|string|max:100',
            'durasi_menit' => 'required|integer|min:1',
        ]);

        $ujian = \App\Models\Ujian::findOrFail($id);

        // Security Check
        if ($ujian->guru_id !== \Illuminate\Support\Facades\Auth::user()->guru->id) {
            abort(403);
        }

        // Urutan otomatis
        $urutan = $ujian->sections()->count() + 1;

        $ujian->sections()->create([
            'judul' => $request->judul,
            'durasi_menit' => $request->durasi_menit,
            'urutan' => $urutan,
            'tipe_section' => 'standard', 
        ]);

        return redirect()->back()->with('success', 'Section berhasil ditambahkan!');
    }

    // 6. Tampilkan Form Input Soal
    public function createSoal($sectionId)
    {
        $section = Section::with('ujian')->findOrFail($sectionId);

        // Security Check: Punya guru ini ga?
        if ($section->ujian->guru_id !== Auth::user()->guru->id) {
            abort(403);
        }

        return view('guru.ujian.soal-create', compact('section'));
    }

    // 7. Simpan Soal ke Database
    public function storeSoal(Request $request, $sectionId)
    {
        $request->validate([
            'pertanyaan' => 'required',
            'pilihan_a' => 'required',
            'pilihan_b' => 'required',
            'pilihan_c' => 'required',
            'pilihan_d' => 'required',
            // Pilihan E opsional, siapa tau cuma sampai D
            'kunci_jawaban' => 'required|in:a,b,c,d,e',
            'bobot' => 'required|integer|min:1',
        ]);

        $section = Section::findOrFail($sectionId);

        // Hitung urutan soal otomatis
        $urutan = $section->soal()->count() + 1;

        Soal::create([
            'section_id' => $sectionId,
            'pertanyaan' => $request->pertanyaan,
            'pilihan_a' => $request->pilihan_a,
            'pilihan_b' => $request->pilihan_b,
            'pilihan_c' => $request->pilihan_c,
            'pilihan_d' => $request->pilihan_d,
            'pilihan_e' => $request->pilihan_e,
            'kunci_jawaban' => $request->kunci_jawaban,
            'bobot' => $request->bobot,
            'urutan' => $urutan,
            'tipe_soal' => 'pilihan_ganda',
        ]);

        // Redirect balik ke halaman Detail Ujian (Show)
        return redirect()->route('guru.ujian.show', $section->ujian_id)
            ->with('success', 'Soal berhasil ditambahkan ke section ' . $section->judul);
    }
}