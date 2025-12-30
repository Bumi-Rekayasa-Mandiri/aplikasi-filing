<?php

use App\Http\Controllers\SuratController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::post('/surat', [SuratController::class, 'store'])
    ->name('surat.store');
Route::get('/', function () {
    return Inertia::render('Dashboard');
});
