<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TokenUjianController;
use App\Http\Controllers\Admin\UjianController as AdminUjianController;
use App\Http\Controllers\Siswa\UjianController as SiswaUjianController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::post('/login', [AuthController::class, 'login']);

/*
|--------------------------------------------------------------------------
| Route Test / Development
|--------------------------------------------------------------------------
*/

Route::post('/test/ujian/section', [AdminUjianController::class, 'storeSection']);

/*
|--------------------------------------------------------------------------
| Protected Routes (Sanctum)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:sanctum'])->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    Route::post('/token/validate', [TokenUjianController::class, 'validateToken']);
    Route::post('/token/generate', [TokenUjianController::class, 'generate']);

});
