<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Filing\SuratController;
use App\Http\Controllers\Filing\SuratApprovalController;
use App\Http\Controllers\Filing\ArsipSuratController;
use App\Http\Controllers\Filing\ArsipSertifikatController;
use App\Http\Controllers\Filing\DashboardController;
use App\Http\Controllers\RolePermissionController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Dashboard', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

// Route::get('/dashboard', function () {
//     return Inertia::render('Dashboard');
//     })->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

//Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth'])->prefix('filing')->name('filing.')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('surat', SuratController::class)->except(['create']);
    Route::get('/surat/create/{jenis}', [SuratController::class, 'create'])->name('surat.create');
    Route::post('/surat/store/{jenis}', [SuratController::class, 'store'])->name('surat.store');
    Route::get('/surat/{surat}/edit/{jenis}', [SuratController::class, 'edit'])->name('surat.edit');    
    Route::put('/surat/{surat}/update/{jenis}', [SuratController::class, 'update'])->name('surat.update');
    Route::delete('/surat/{surat}/destroy', [SuratController::class, 'destroy'])->name('surat.destroy');
    Route::post('/surat/{surat}/generate-nomor-surat', [SuratController::class, 'generateNomorSurat'])->name('surat.generate-nomor-surat');
    Route::post('/surat/{surat}/generate-pdf', [SuratController::class, 'generatePdf'])->name('surat.generate-pdf');
    Route::get('/surat/{surat}/preview', [SuratController::class, 'preview'])->name('surat.preview');


    Route::post('/surat/{surat}/upload-cap', [SuratController::class, 'uploadCap'])->name('surat.upload-cap');
    Route::post('/surat/{surat}/upload-ttd', [SuratController::class, 'uploadTtd'])->name('surat.upload-ttd');
    Route::delete('/surat/ttd/{ttd}', [SuratController::class, 'deleteTtd'])->name('surat.delete-ttd');

    Route::post('/surat/{surat}/upload-pdf', [SuratController::class, 'uploadPdf'])->name('surat.upload-pdf');

    Route::post('{surat}/submit', [SuratApprovalController::class, 'submit'])->name('filing.surat.submit');
    Route::post('{surat}/approve', [SuratApprovalController::class, 'approve'])->name('filing.surat.approve');
    Route::post('{surat}/reject', [SuratApprovalController::class, 'reject'])->name('filing.surat.reject');

    Route::resource('arsip', ArsipSuratController::class);
    Route::post('arsip/{arsip}/upload', [ArsipSuratController::class, 'uploadFile'])->name('arsip.upload');

    Route::get('arsip/{arsip}/download', [ArsipSuratController::class, 'download'])->name('arsip.download');

    Route::resource('sertifikat', ArsipSertifikatController::class);
    Route::post('sertifikat/{sertifikat}/upload', [ArsipSertifikatController::class, 'uploadFile'])->name('sertifikat.upload');

    Route::get('sertifikat/{sertifikat}/download', [ArsipSertifikatController::class, 'download'])->name('sertifikat.download');

    Route::get('filing/surat/{surat}/download', [\App\Http\Controllers\Filing\SuratDownloadController::class, 'download'])->name('filing.surat.download');
});

Route::middleware(['auth'])->prefix('roles')->group(function () {
    Route::get('/', [RolePermissionController::class, 'index'])->name('roles.index');
    Route::get('/create', [RolePermissionController::class, 'create'])->name('roles.create');
    Route::post('/', [RolePermissionController::class, 'store'])->name('roles.store');
});

require __DIR__.'/auth.php';
