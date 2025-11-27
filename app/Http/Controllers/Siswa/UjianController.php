<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\TokenUjianService;

class UjianController extends Controller
{

public function aksesUjian(Request $request, TokenUjianService $tokenService)
{
    $request->validate([
        'kode_token' => 'required|string|size:6|uppercase',
    ]);

    try {
        $siswa = auth()->user()->siswa;
        $token = $tokenService->validasiTokenDenganCache(
            $request->kode_token,
            $siswa
        );

        $sesiUjian = $token->gunakanToken(
            $siswa,
            $request->ip(),
            $request->userAgent()
        );

        return redirect()->route('siswa.ujian.mulai', $sesiUjian->id);

    } catch (\Exception $e) {
        return back()->with('error', $e->getMessage());
    }
}
}
