<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SoalController extends Controller
{
    public function index()
    {
        return "Halaman Soal Guru";
    }

    public function create()
    {
        return "Form Tambah Soal";
    }

    public function store(Request $request)
    {
        // Logic simpan soal
    }

    public function destroy($id)
    {
        // Logic hapus soal
    }
}