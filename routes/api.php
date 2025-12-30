<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SuratController;

Route::get('/__debug', function(){
    return response()->json([
        'app' => 'laravel-ok',
        'time' => now()->toDateTimeString(),
    ]);
});
Route::get('/surat', [SuratController::class, 'index']);
Route::get('/surat/{id}', [SuratController::class, 'show']);
Route::post('/surat', [SuratController::class, 'store']);
Route::post('/surat/{id}/upload', [SuratController::class, 'store']);
Route::get('/ping', function () {
    return response()->json(['status' => 'ok']);
});