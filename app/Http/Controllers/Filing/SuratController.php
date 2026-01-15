<?php

namespace App\Http\Controllers\Filing;

use App\Http\Controllers\Controller;
use App\Models\Surat;
use App\Models\SuratCap;
use App\Models\SuratTtd;
use App\Models\FileUpload;
use App\Models\NomorSuratLog;
use App\Services\NomorSuratGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class SuratController extends Controller
{
    public function index(Request $request)
    {

    //dd(auth()->check(), auth()->user());

    $this->authorize('viewAny', Surat::class);

    return Inertia::render('filing/surat/Index', [
        'surat' => Surat::paginate(10),
    ]);


    $query = Surat::query();

    if ($request->filled('search')) {
        $query->where(function ($q) use ($request) {
            $q->where('judul', 'like', "%{$request->search}%")
              ->orWhere('nomor_surat', 'like', "%{$request->search}%");
        });
    }

    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    $surat = $query
        ->orderByDesc('tanggal_surat')
        ->paginate(10)
        ->withQueryString();

    return Inertia::render('filing/surat/Index', [
        'surat'   => $surat,
        'filters' => $request->only(['search', 'status']),
    ]);
    }

    public function show(Surat $surat)
    {

        $this->authorize('view', $surat);

    $surat->load([
        'nomorSuratLogs' => fn ($q) => $q->latest(),
        'cap.file',
        'ttds.file',
        'files'
    ]);

    return Inertia::render('filing/surat/Show', [
        'surat' => [...$surat->toArray(), 'pdf_url' => $surat->getFirstMediaUrl('pdf')],
    ]);
    }

    /**
     * CREATE SURAT + GENERATE NOMOR (OPS 1)
     */
    public function store(Request $request)
    {

        $this->authorize('create', Surat::class);

        $validated = $request->validate([
            'judul'      => 'required|string|max:255',
            'perihal'    => 'required|string|max:255',
            'tujuan'     => 'required|string|max:255',
            'isi_surat'  => 'required|string',
            'kode_jenis' => 'required|string|max:10',
            'tanggal_surat' => 'required|date',
        ]);

        return DB::transaction(function () use ($validated) {

            $generator = app(NomorSuratGenerator::class);
            $nomor = $generator->generate($validated['kode_jenis']);

            $surat = Surat::create([
                'judul'         => $validated['judul'],
                'perihal'       => $validated['perihal'],
                'tujuan'        => $validated['tujuan'],
                'isi_surat'     => $validated['isi_surat'],
                'tanggal_surat' => $validated['tanggal_surat'],
                'nomor_surat'   => $nomor['nomor_surat'],
                'status'        => 'draft',
            ]);

            NomorSuratLog::create([
                'surat_id'       => $surat->id,
                'running_number' => $nomor['running_number'],
                'nomor_surat'    => $nomor['nomor_surat'],
                'tahun'          => $nomor['tahun'],
                'bulan'          => $nomor['bulan'],
                'kode_jenis'     => $nomor['kode_jenis'],
            ]);

            return redirect ()->route('filing.surat.show', $surat->id)
                ->with('success', 'Surat berhasil dibuat dengan nomor surat: ' . $nomor['nomor_surat']);
        });
    }

    public function create()
    {
        $this->authorize('create', Surat::class);

        return Inertia::render('filing/surat/Create', [
            'kodeJenis' => [
                'SPK-BRM' => 'Surat Pemberitahuan atau PHK',
                'GRS-BRM' => 'Surat Pengajuan Garansi Material',
                'SPD-BRM' => 'Surat Pengembalian Dana',
                'SK-BRM' => 'Surat Permohonan Keringanan Denda',
                'IEI-BRM' => 'Surat Garansi Pemasangan',
                'BRM' => 'Surat Pelepasan Hak / Surat Izin Kerja dan LK3',
            ],
        ]);
    }

    public function update(Request $request, Surat $surat)
    {
  
        $this->authorize('update', $surat);

    $validated = $request->validate([
        'judul'      => 'required|string|max:255',
        'perihal'    => 'required|string|max:255',
        'tujuan'     => 'required|string|max:255',
        'isi_surat'  => 'required|string',
        'kode_jenis' => 'required|string|max:10',
        'tanggal_surat' => 'required|date',
    ]);

        $surat->update($validated);

    DB::transaction(function () use ($surat, $validated) {
        // logic nomor surat tetap
    });

    return redirect()
        ->route('filing.surat.show', $surat->id)
        ->with('success', 'Surat berhasil diperbarui');
    }

    public function edit(Surat $surat)
{
    $this->authorize('update', $surat);

    return Inertia::render('filing/surat/Edit', [
        'surat' => $surat,
        'kodeJenis' => [
            'SPK-BRM' => 'Surat Pemberitahuan atau PHK',
                'GRS-BRM' => 'Surat Pengajuan Garansi Material',
                'SPD-BRM' => 'Surat Pengembalian Dana',
                'SK-BRM' => 'Surat Permohonan Keringanan Denda',
                'IEI-BRM' => 'Surat Garansi Pemasangan',
                'BRM' => 'Surat Pelepasan Hak / Surat Izin Kerja dan LK3',
        ],
    ]);
}


    /**
     * UPLOAD CAP / TTD
     */
    public function uploadFile(Request $request, Surat $surat)
    {

    $request->validate([
        'file' => 'required|file|mimes:png,jpg,jpeg,pdf|max:10240',
        'type' => 'required|in:cap,ttd',
    ]);

    DB::transaction(function () use ($request, $surat) {
        // upload & relasi file
    });

    return redirect()
        ->back()
        ->with('success', 'File berhasil diupload');
    }

    /**
     * UPLOAD PDF SURAT
     */

    public function uploadPdf(Request $request, Surat $surat)
{

    $this->authorize('uploadPdf', $surat);

    $request->validate([
        'pdf' => ['required', 'file', 'mimes:pdf', 'max:10240'],
    ]);

    // Hapus PDF lama (jika ada)
    $surat->clearMediaCollection('pdf');

    // Simpan PDF baru
    $surat
        ->addMediaFromRequest('pdf')
        ->usingName('surat-' . $surat->id)
        ->toMediaCollection('pdf');

    return redirect()->back()->with('success', 'PDF surat berhasil diupload.');
}

}