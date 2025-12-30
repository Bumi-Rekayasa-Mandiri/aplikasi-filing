<?php

namespace App\Http\Controllers;

use App\Models\Surat;
use App\Models\SuratCap;
use App\Models\SuratTtd;
use App\Models\FileUpload;
use App\Models\NomorSuratLog;
use App\Services\NomorSuratGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use illuminate\Support\Facades\Storage;

class SuratController extends Controller
{
public function index(Request $request)
{
    $query = Surat::query()
        ->with([
            'nomorSuratLogs' => function ($q) {
                $q->latest()->limit(1);
            },
            'cap.file',
            'ttds.file'
        ]);

    // 🔎 Search: judul / nomor surat
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->where('judul', 'like', "%{$search}%")
              ->orWhere('nomor_surat', 'like', "%{$search}%");
        });
    }

    // 🔖 Filter status
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    $surat = $query
        ->orderByDesc('tanggal_surat')
        ->paginate(10);

    return response()->json([
        'message' => 'List surat',
        'data'    => $surat->items(),
        'meta'    => [
            'current_page' => $surat->currentPage(),
            'per_page'     => $surat->perPage(),
            'total'        => $surat->total(),
            'last_page'    => $surat->lastPage(),
        ]
    ]);
}
public function show($id)
{
    $surat = Surat::with([
        'nomorSuratLogs' => function ($q) {
            $q->latest();
        },
        'cap.file',
        'ttds.file',
        'files'
    ])->findOrFail($id);

    return response()->json([
        'message' => 'Detail surat',
        'data'    => $surat,
    ]);
}
public function update(Request $request, $id)
{
    $surat = Surat::findOrFail($id);

    $validated = $request->validate([
        'judul'      => 'required|string|max:255',
        'perihal'    => 'required|string|max:255',
        'tujuan'     => 'required|string|max:255',
        'isi_surat'  => 'required|string',
        'kode_jenis' => 'nullable|string|max:10',
    ]);

    return DB::transaction(function () use ($surat, $validated) {

        $kodeJenisLama = $surat->nomorSuratLogs()
            ->latest()
            ->value('kode_jenis');

        $kodeJenisBaru = $validated['kode_jenis'] ?? null;

        // Default: nomor tidak berubah
        $nomorBaru = null;

        // Jika kode jenis berubah → generate nomor baru
        if ($kodeJenisBaru !== $kodeJenisLama) {
            $generator = app(NomorSuratGenerator::class);
            $nomorBaru = $generator->generate($kodeJenisBaru);

            $surat->nomor_surat = $nomorBaru['nomor_surat'];
        }

        // Update field surat
        $surat->update([
            'judul'     => $validated['judul'],
            'perihal'   => $validated['perihal'],
            'tujuan'    => $validated['tujuan'],
            'isi_surat' => $validated['isi_surat'],
        ]);

        // Jika nomor baru dibuat → simpan log
        if ($nomorBaru) {
            NomorSuratLog::create([
                'surat_id'       => $surat->id,
                'running_number' => $nomorBaru['running_number'],
                'nomor_surat'    => $nomorBaru['nomor_surat'],
                'tahun'          => $nomorBaru['tahun'],
                'bulan'          => $nomorBaru['bulan'],
                'kode_jenis'     => $nomorBaru['kode_jenis'],
            ]);
        }

        return response()->json([
            'message' => 'Surat berhasil diperbarui',
            'data'    => $surat->fresh([
                'nomorSuratLogs' => fn ($q) => $q->latest(),
            ]),
        ]);
    });
}
public function upload (Request $request, $id)
{
    $request->validate([
        'file' => 'required|file|mimes:png,jpg,jpeg,pdf|max:10240',
        'type' => 'required|in:cap,ttd',
    ]);

    $surat = Surat::findOrFail($id);

    $file = $request->file('file');

    $storedPath = $file->store('surat_files', 'public');

    $fileUpload = FileUpload::create([
        'original_name' => $file->getClientOriginalName(),
        'stored_name'   => basename($storedPath),
        'file_path'     => $storedPath,
        'path'          => dirname($storedPath),
        'extension'     => $file->getClientOriginalExtension(),
        'mime_type'     => $file->getMimeType(),
        'size'          => $file->getSize(),
        'source'        => 'surat',
    ]);

    if ($request->type === 'cap') {
        SuratCap::updateOrCreate(
            ['surat_id' => $surat->id],
            ['file_upload_id' => $fileUpload->id]
        );
    }

    if ($request->type === 'ttd') {
        SuratTtd::create([
            'surat_id' => $surat->id,
            'file_upload_id' => $fileUpload->id,
        ]);
    }

    return response()->json([
        'message' => 'File berhasil diupload',
        'data' => $fileUpload,
    ], 201);
 }
}


