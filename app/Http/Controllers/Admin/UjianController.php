<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUjianSectionRequest;
use App\Models\UjianSection;

class UjianController extends Controller
{
    /**
     * Simpan section ujian
     */
    public function storeSection(StoreUjianSectionRequest $request)
    {
        UjianSection::create($request->validated());

        return back()->with('success', 'Section berhasil ditambahkan');
    }
}
