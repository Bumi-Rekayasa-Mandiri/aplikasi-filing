<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Filing\SuratController;
use App\Http\Controllers\Filing\SuratDocxController;
use App\Http\Controllers\Filing\SuratApprovalController;
use App\Http\Controllers\Filing\ArsipSuratController;
use App\Http\Controllers\Filing\ArsipSertifikatController;
use App\Http\Controllers\Filing\DashboardController;
use App\Http\Controllers\RolePermissionController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// ─── PUBLIC ───────────────────────────────────────────────
Route::get('/', function () {
    return Inertia::render('Dashboard', [
        'canLogin'       => Route::has('login'),
        'canRegister'    => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion'     => PHP_VERSION,
    ]);
});

// ─── DASHBOARD ────────────────────────────────────────────
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// ─── PROFILE ──────────────────────────────────────────────
Route::middleware(['auth'])->group(function () {
    Route::get('/profile',    [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',  [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ─── FILING ───────────────────────────────────────────────
Route::middleware(['auth'])->prefix('filing')->name('filing.')->group(function () {

    // CRUD Surat
    Route::get('/surat/create/{jenis}',          [SuratController::class, 'create'])->name('surat.create');
    Route::post('/surat/store/{jenis}',           [SuratController::class, 'store'])->name('surat.store');
    Route::get('/surat/{surat}/edit/{jenis}',     [SuratController::class, 'edit'])->name('surat.edit');
    Route::put('/surat/{surat}/update/{jenis}',   [SuratController::class, 'update'])->name('surat.update');
    Route::delete('/surat/{surat}/destroy',       [SuratController::class, 'destroy'])->name('surat.destroy');
    Route::post('/surat/{surat}/generate-nomor-surat', [SuratController::class, 'generateNomorSurat'])->name('surat.generate-nomor-surat');

    // Generate & Preview PDF
    Route::post('/surat/{surat}/generateSKP-pdf',  [SuratController::class, 'generateSKPPdf'])->name('surat.generateSKP-pdf');
    Route::get('/surat/{surat}/preview/skp',        [SuratController::class, 'previewSKP'])->name('surat.previewSKP')->whereNumber('surat');

    Route::post('/surat/{surat}/generateSK-pdf',   [SuratController::class, 'generateSKPdf'])->name('surat.generateSK-pdf');
    Route::get('/surat/{surat}/preview/sk',         [SuratController::class, 'previewSK'])->name('surat.previewSK')->whereNumber('surat');

    Route::post('/surat/{surat}/generateSPI-pdf',  [SuratController::class, 'generateSPIPdf'])->name('surat.generateSPI-pdf');
    Route::get('/surat/{surat}/previewSPI',         [SuratController::class, 'previewSPI'])->name('surat.previewSPI')->whereNumber('surat');

    Route::post('/surat/{surat}/generateSPD-pdf',  [SuratController::class, 'generateSPDPdf'])->name('surat.generateSPD-pdf');
    Route::get('/surat/{surat}/preview/spd',        [SuratController::class, 'previewSPD'])->name('surat.previewSPD')->whereNumber('surat');

    Route::post('/surat/{surat}/generateIEI-pdf',  [SuratController::class, 'generateIEIPdf'])->name('surat.generateIEI-pdf');
    Route::get('/surat/{surat}/preview/iei',        [SuratController::class, 'previewIEI'])->name('surat.previewIEI')->whereNumber('surat');

    Route::post('/surat/{surat}/generateGRS-pdf',  [SuratController::class, 'generateGRSPdf'])->name('surat.generateGRS-pdf');
    Route::get('/surat/{surat}/preview/grs',        [SuratController::class, 'previewGRS'])->name('surat.previewGRS')->whereNumber('surat');

    Route::post('/surat/{surat}/generateBRM1-pdf', [SuratController::class, 'generateBRM1Pdf'])->name('surat.generateBRM1-pdf');
    Route::get('/surat/{surat}/preview/brm1',       [SuratController::class, 'previewBRM1'])->name('surat.previewBRM1')->whereNumber('surat');

    Route::post('/surat/{surat}/generateBRM2-pdf', [SuratController::class, 'generateBRM2Pdf'])->name('surat.generateBRM2-pdf');
    Route::get('/surat/{surat}/preview/brm2',       [SuratController::class, 'previewBRM2'])->name('surat.previewBRM2')->whereNumber('surat');

    // Resource Surat
    Route::resource('surat', SuratController::class)
        ->except(['create'])
        ->where(['surat' => '[0-9]+']);

    // Upload Media
    Route::post('/surat/{surat}/upload-cap', [SuratController::class, 'uploadCap'])->name('surat.upload-cap');
    Route::post('/surat/{surat}/upload-ttd', [SuratController::class, 'uploadTtd'])->name('surat.upload-ttd');
    Route::delete('/surat/ttd/{ttd}',        [SuratController::class, 'deleteTtd'])->name('surat.delete-ttd');
    Route::post('/surat/{surat}/upload-pdf', [SuratController::class, 'uploadPdf'])->name('surat.upload-pdf');

    // Download Surat
    Route::get('/surat/{surat}/download', [\App\Http\Controllers\Filing\SuratDownloadController::class, 'download'])->name('surat.download');

    // Arsip Surat
    Route::post('/arsip/{arsip}/upload', [ArsipSuratController::class, 'uploadFile'])->name('arsip.upload');
    Route::get('/arsip/{arsip}/download', [ArsipSuratController::class, 'download'])->name('arsip.download');
    Route::resource('arsip', ArsipSuratController::class);

    // Sertifikat
    Route::post('/sertifikat/{sertifikat}/upload',    [ArsipSertifikatController::class, 'uploadFile'])->name('sertifikat.upload');
    Route::get('/sertifikat/{sertifikat}/download',   [ArsipSertifikatController::class, 'download'])->name('sertifikat.download');
    Route::resource('sertifikat', ArsipSertifikatController::class);
});

// ── DOCX Downloads ──────────────────────────────────────────
Route::middleware(['auth'])->prefix('filing')->name('filing.')->group(function () {
    Route::prefix('surat')->name('surat.')->group(function () {
        Route::get('/{surat}/download/skp',  [SuratDocxController::class, 'downloadSKP'])->name('downloadSKP-docx');
        Route::get('/{surat}/download/iei',  [SuratDocxController::class, 'downloadIEI'])->name('downloadIEI-docx');
        Route::get('/{surat}/download/grs',  [SuratDocxController::class, 'downloadGRS'])->name('downloadGRS-docx');
        Route::get('/{surat}/download/spi',  [SuratDocxController::class, 'downloadSPI'])->name('downloadSPI-docx');
        Route::get('/{surat}/download/spd',  [SuratDocxController::class, 'downloadSPD'])->name('downloadSPD-docx');
        Route::get('/{surat}/download/brm1', [SuratDocxController::class, 'downloadBRM1'])->name('downloadBRM1-docx');
        Route::get('/{surat}/download/brm2', [SuratDocxController::class, 'downloadBRM2'])->name('downloadBRM2-docx');
        Route::get('/{surat}/download/sk',   [SuratDocxController::class, 'downloadSK'])->name('downloadSK-docx');
    });
});

// ─── ROLES ────────────────────────────────────────────────
Route::middleware(['auth'])->prefix('roles')->name('roles.')->group(function () {
    Route::get('/',        [RolePermissionController::class, 'index'])->name('index');
    Route::get('/create',  [RolePermissionController::class, 'create'])->name('create');
    Route::post('/',       [RolePermissionController::class, 'store'])->name('store');
});

require __DIR__.'/auth.php';