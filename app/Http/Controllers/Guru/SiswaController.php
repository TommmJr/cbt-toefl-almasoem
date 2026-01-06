<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use Illuminate\Support\Facades\Auth;

class SiswaController extends Controller
{
    /**
     * Tampilkan daftar siswa
     * Dipakai guru untuk:
     * - lihat akun siswa
     * - nanti dipakai generate token per siswa
     */
    public function index()
    {
        // pastikan yang akses guru
        if (! Auth::user()->guru) {
            abort(403, 'Bukan guru');
        }

        // ambil siswa + user
        $siswa = Siswa::with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('guru.siswa.index', compact('siswa'));
    }
}
