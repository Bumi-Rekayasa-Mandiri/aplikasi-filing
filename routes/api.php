<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Filing\SuratController;

Route::prefix('surat')->group(function () {
    Route::get('/', [SuratController::class, 'index']);
    Route::post('/', [SuratController::class, 'store']);              // create surat
    Route::get('/{surat}', [SuratController::class, 'show']);
    Route::put('/{surat}', [SuratController::class, 'update']);
    Route::post('/{surat}/upload', [SuratController::class, 'uploadFile']);
});