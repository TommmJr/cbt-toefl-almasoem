<?php
use App\Http\Controllers\Siswa\UjianController;

Route::middleware(['auth', 'role:siswa'])->group(function () {

    Route::post('/ujian/akses', [UjianController::class, 'aksesUjian'])
        ->name('siswa.ujian.akses');

    Route::get('/ujian/{sesiUjian}', [UjianController::class, 'show'])
        ->name('siswa.ujian.mulai');

});
