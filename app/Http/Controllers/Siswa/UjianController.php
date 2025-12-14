<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ujian;
use App\Models\SesiUjian;
use App\Services\TokenUjianService;
use App\Models\Jawaban;
use App\Actions\Ujian\AutoSubmitSectionAction;

class UjianController extends Controller
{
    /**
     * LIST UJIAN
     */
    public function index()
    {
        $ujians = Ujian::published()->with('sections')->get();
        return view('siswa.ujian.index', compact('ujians'));
    }

    /**
     * DETAIL UJIAN
     */
    public function detail(Ujian $ujian)
    {
        return view('siswa.ujian.show', compact('ujian'));
    }

    /**
     * FORM TOKEN
     */
    public function formToken()
    {
        return view('siswa.ujian.akses');
    }

    /**
     * PROSES TOKEN → BUAT SESI
     */
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

    /**
     * START / LANJUTKAN UJIAN (CEK SESI AKTIF)
     */
    public function mulai(Ujian $ujian)
    {
        $siswa = auth()->user()->siswa;

        $sesiAktif = SesiUjian::where('ujian_id', $ujian->id)
            ->where('siswa_id', $siswa->id)
            ->where('status', 'sedang_mengerjakan')
            ->latest()
            ->first();

        if ($sesiAktif) {
            return redirect()->route('siswa.ujian.mulai', $sesiAktif->id);
        }

        return redirect()->route('siswa.ujian.akses');
    }

    /**
     * HALAMAN PENGERJAAN UJIAN
     */
    public function show($sesiId, AutoSubmitSectionAction $autoSubmit)
    {
        $sesi = SesiUjian::with('ujian.sections.soal')->findOrFail($sesiId);

        $sesi->pastikanBisaDiaksesOleh(auth()->user()->siswa->id);

        // PALU OTOMATIS SECTION SEBELUMNYA
        $autoSubmit->handle($sesi);

        $section = $sesi->sectionAktif();

        if (! $section) {
            return redirect()
                ->route('siswa.dashboard')
                ->with('success', 'Ujian selesai');
        }

        if (! $sesi->section_mulai_at) {
            $sesi->update(['section_mulai_at' => now()]);
        }

        return view('siswa.ujian.kerjakan', [
            'sesi'      => $sesi,
            'ujian'     => $sesi->ujian,
            'section'   => $section,
            'sisaDetik' => $sesi->sisaWaktuSection(),
        ]);
    }

    /**
     * SIMPAN JAWABAN (REALTIME)
     */
    public function simpanJawaban(Request $request, AutoSubmitSectionAction $autoSubmit)
    {
        $request->validate([
            'sesi_id' => 'required|integer|exists:sesi_ujians,id',
            'soal_id' => 'required|integer|exists:soal,id',
            'jawaban' => 'nullable|string',
        ]);

        $sesi = SesiUjian::with('ujian.sections.soal')
            ->findOrFail($request->sesi_id);

        $sesi->pastikanBisaDiaksesOleh(auth()->user()->siswa->id);

        // PALU LAGI SEBELUM SIMPAN
        $autoSubmit->handle($sesi);

        if ($sesi->isSectionExpired()) {
            abort(403, 'Waktu section sudah habis');
        }

        $section = $sesi->sectionAktif();
        $soal = $section?->soal()->where('id', $request->soal_id)->first();

        if (! $soal) {
            abort(403, 'Soal tidak valid');
        }

        $data = [
            'sesi_ujian_id' => $sesi->id,
            'soal_id' => $soal->id,
            'waktu_jawab' => now(),
        ];

        if ($soal->isPilihanGanda()) {
            $data['jawaban_pilihan'] = $request->jawaban;
        } else {
            $data['jawaban_essay'] = $request->jawaban;
        }

        Jawaban::updateOrCreate(
            [
                'sesi_ujian_id' => $sesi->id,
                'soal_id' => $soal->id,
            ],
            $data
        );

        return response()->json(['status' => 'ok']);
    }
}
