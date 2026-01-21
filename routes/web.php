<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Filing\SuratController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth'])->prefix('filing')->name('filing.')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('surat', SuratController::class);

    Route::post('/surat/{surat}/upload-cap', [SuratController::class, 'uploadCap'])->name('surat.upload-cap');
    Route::post('/surat/{surat}/upload-ttd', [SuratController::class, 'uploadTtd'])->name('surat.upload-ttd');

    Route::post('/surat/{surat}/upload-pdf', [SuratController::class, 'uploadPdf'])->name('surat.upload-pdf');

    Route::post('{surat}/submit', [SuratApprovalController::class, 'submit'])->name('filing.surat.submit');
    Route::post('{surat}/approve', [SuratApprovalController::class, 'approve'])->name('filing.surat.approve');
    Route::post('{surat}/reject', [SuratApprovalController::class, 'reject'])->name('filing.surat.reject');

    Route::resource('arsip', SuratArsipController::class);
    Route::post('arsip/{arsip}/upload', [SuratArsipController::class, 'uploadFile'])->name('arsip.upload');
});

require __DIR__.'/auth.php';
