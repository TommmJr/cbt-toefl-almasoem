<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ujian;
use App\Models\SesiUjian;
use App\Services\TokenUjianService;
use App\Enums\StatusUjian;
use App\Models\Jawaban;


class UjianController extends Controller
{
    // LIST UJIAN
    public function index()
    {
        $ujians = Ujian::published()->with('sections')->get();
        return view('siswa.ujian.index', compact('ujians'));
    }

    // DETAIL UJIAN
    public function detail(Ujian $ujian)
    {
        return view('siswa.ujian.show', compact('ujian'));
    }

    // FORM TOKEN
    public function formToken()
    {
        return view('siswa.ujian.akses');
    }

    // SUBMIT TOKEN
    public function aksesUjian(Request $request, TokenUjianService $tokenService)
    {
        $request->validate([
            'kode_token' => 'required|string|size:6|uppercase',
        ]);

        $siswa = auth()->user()->siswa;

        $token = $tokenService->validasiTokenDenganCache(
            $request->kode_token,
            $siswa
        );

        $sesi = $token->gunakanToken(
            $siswa,
            $request->ip(),
            $request->userAgent()
        );

        return redirect()->route('siswa.ujian.mulai', $sesi->id);
    }

    // MULAI UJIAN
public function show($sesiId)
{
    $sesi = SesiUjian::with('ujian.sections.soal')
        ->findOrFail($sesiId);

    // 1. Validasi akses & status
    $sesi->pastikanBisaDiaksesOleh(
        auth()->user()->siswa->id
    );

    // 2. Ambil section aktif
    $section = $sesi->sectionAktif();

    // Kalau semua section sudah habis → selesai ujian
    if (! $section) {
        $sesi->update([
            'status' => StatusUjian::SELESAI,
            'waktu_selesai' => now(),
        ]);

        return redirect()
            ->route('siswa.dashboard')
            ->with('success', 'Ujian selesai');
    }

    // 3. Set waktu mulai section (HANYA SEKALI)
    if (! $sesi->section_mulai_at) {
        $sesi->update([
            'section_mulai_at' => now(),
        ]);
    }

    // 4. Cek sisa waktu section (SERVER-SIDE)
    if ($sesi->sisaWaktuSection() <= 0) {

        // waktu habis → lanjut section berikutnya
        $sesi->lanjutKeSectionBerikutnya();

        // reset timer untuk section baru
        $sesi->update([
            'section_mulai_at' => now(),
        ]);

        return redirect()
            ->route('siswa.ujian.mulai', $sesi->id);
    }

    // 5. Render halaman ngerjain
    return view('siswa.ujian.kerjakan', [
        'sesi'      => $sesi,
        'ujian'     => $sesi->ujian,
        'section'   => $section,
        'sisaDetik' => $sesi->sisaWaktuSection(), // display only
    ]);
}

public function simpanJawaban(Request $request)
{
    $request->validate([
        'sesi_id' => 'required|integer|exists:sesi_ujians,id',
        'soal_id' => 'required|integer|exists:soal,id',
        'jawaban' => 'nullable|string',
    ]);

    $sesi = SesiUjian::with('ujian.sections.soal')
        ->findOrFail($request->sesi_id);

    // Validasi akses
    $sesi->pastikanBisaDiaksesOleh(
        auth()->user()->siswa->id
    );

    //  Tolak jika waktu habis
    if ($sesi->sisaWaktuSection() <= 0) {
        abort(403, 'Waktu sudah habis');
    }

    // Pastikan soal ada di section aktif
    $section = $sesi->sectionAktif();
    $soal = $section?->soal()->where('id', $request->soal_id)->first();

    if (! $soal) {
        abort(403, 'Soal tidak valid untuk section ini');
    }

    // Tentukan kolom jawaban
    $dataJawaban = [
        'sesi_ujian_id' => $sesi->id,
        'soal_id' => $soal->id,
        'waktu_jawab' => now(),
    ];

    if ($soal->isPilihanGanda()) {
        $dataJawaban['jawaban_pilihan'] = $request->jawaban;
    } else {
        $dataJawaban['jawaban_essay'] = $request->jawaban;
    }

    $jawaban = Jawaban::updateOrCreate(
        [
            'sesi_ujian_id' => $sesi->id,
            'soal_id' => $soal->id,
        ],
        $dataJawaban
    );

    // Auto koreksi PG
    $jawaban->cekJawabanPilihanGanda();

    return response()->json([
        'status' => 'ok',
    ]);
}


}