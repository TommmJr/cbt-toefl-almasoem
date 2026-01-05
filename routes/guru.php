<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Guru\DashboardController;
use App\Http\Controllers\Guru\ManajemenUjianController;
use App\Http\Controllers\Guru\SoalController;

Route::prefix('guru')
    ->middleware(['auth', 'role:guru'])
    ->name('guru.') 
    ->group(function () {
        
        // 1. Dashboard Utama (Jadi: guru.dashboard)
        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');

        // 2. CRUD Ujian (Jadi: guru.ujian.index, guru.ujian.create, dll)
        Route::resource('ujian', ManajemenUjianController::class);

        // 3. Route Khusus: Simpan Section (FIX: Jangan pakai guru. lagi depannya)
        Route::post('/ujian/{id}/section', [ManajemenUjianController::class, 'storeSection'])
            ->name('ujian.section.store'); // Hasil akhir: guru.ujian.section.store

        // 4. Route Khusus: Soal (FIX: Jangan pakai guru. lagi depannya)
        Route::get('/section/{section}/soal/create', [ManajemenUjianController::class, 'createSoal'])
            ->name('ujian.soal.create'); // Hasil akhir: guru.ujian.soal.create
            
        Route::post('/section/{section}/soal', [ManajemenUjianController::class, 'storeSoal'])
            ->name('ujian.soal.store'); // Hasil akhir: guru.ujian.soal.store

        // Resource Soal
        Route::resource('soal', SoalController::class);

    });