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
            
        return Inertia::render('filing/surat/Index', [
            'surat' => $query

            ->orderByDesc('tanggal_surat')
            ->paginate(10)
            ->withQueryString(),
        
        ]);
    }

    public function show(Surat $surat)
    {

        $this->authorize('view', $surat);

            $surat->load([
            'nomorSuratLogs' => fn ($q) => $q->latest(),
            'ttds.media',
            'cap.media',
        ]);

        return Inertia::render('filing/surat/Show', [
        'surat' => [
                ...$surat->toArray(),
                'pdf_url' => $surat->getFirstMediaUrl('pdf'),
                'cap_url' => $surat->getFirstMediaUrl('cap'),
                'ttds' => $surat->ttds->map(fn ($t) => [
                    'id' => $t->id,
                    'nama' => $t->nama_penandatangan,
                    'jabatan' => $t->jabatan,
                    'url' => $t->getFirstMediaUrl('ttd'),
                ]),
            ],
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

            $surat = app(\App\Services\Filing\SuratService::class)->create($validated);

            return redirect ()->route('filing.surat.show', $surat->id)
                ->with('success', 'Surat berhasil dibuat dengan nomor surat');
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
                'IEI-BRM' => 'Surat Garansi Pekerjaan',
                'SPI-BRM' => 'Surat Permohonan Investasi',
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
                    'IEI-BRM' => 'Surat Garansi Pekerjaan',
                    'SPI-BRM' => 'Surat Permohonan Investasi',
                    'BRM' => 'Surat Pelepasan Hak / Surat Izin Kerja dan LK3',
            ],
        ]);
    }

    /**
     * UPLOAD CAP / TTD
     */
    public function uploadCap(Request $request, Surat $surat)
    {
        $this->authorize('update', $surat);

        $request->validate([
            'cap' => ['required', 'image', 'mimes:png,jpg,jpeg', 'max:2048'],
        ]);

        $cap = $surat->cap ?: $surat->cap()->create();

        $cap->clearMediaCollection('cap');
        $cap->addMediaFromRequest('cap')->toMediaCollection('cap');

        return back()->with('success', 'Cap perusahaan berhasil diupload');
    }

    public function uploadTtd(Request $request, Surat $surat)
    {
        $this->authorize('update', $surat);

        $request->validate([
            'ttd' => ['required', 'image', 'mimes:png,jpg,jpeg', 'max:2048'],
            'nama_penandatangan' => ['required', 'string'],
            'jabatan' => ['required', 'string'],
        ]);

        $ttd = $surat->ttds()->create([
            'nama_penandatangan' => $request->nama_penandatangan,
            'jabatan' => $request->jabatan,
        ]);

        $ttd->addMediaFromRequest('ttd')->toMediaCollection('ttd');

        return back()->with('success', 'Tanda tangan berhasil ditambahkan');
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

    public function destroy(Surat $surat)
    {
        $this->authorize('delete', $surat);

        $surat->delete();

        return Inertia::render('filing/surat/Index')->with('success', 'Surat berhasil dihapus');
    }
}