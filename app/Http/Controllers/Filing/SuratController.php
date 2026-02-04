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
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class SuratController extends Controller
{
    public function index(Request $request)
{
    $this->authorize('viewAny', Surat::class);

    $sortField = $request->input('sortField', 'tanggal_surat');
    $sortDirection = $request->input('sortDirection', 'desc');

    $query = Surat::query();

    // SEARCH
    if ($request->filled('search')) {
        $query->where(function ($q) use ($request) {
            $q->where('judul', 'like', '%' . $request->search . '%')
              ->orWhere('nomor_surat', 'like', '%' . $request->search . '%');
        });
    }

    // FILTER STATUS
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    // FINAL EXECUTION (WAJIB)
    $surat = $query
        ->orderBy($sortField, $sortDirection)
        ->paginate(10)
        ->withQueryString();

    return Inertia::render('filing/surat/Index', [
        'surat'     => $surat,
        'sort'      => $sortField,
        'direction' => $sortDirection,
        'filters'   => $request->only(['search', 'status']),
    ]);
}

    public function show(Surat $surat)
    {
        $surat->load(['ttds.media']);

        return Inertia::render('filing/surat/Show', [
            'surat' => [
                'id' => $surat->id,
                'nomor_surat' => $surat->nomor_surat,
                'judul' => $surat->judul,
                'tujuan' => $surat->tujuan,
                'status' => $surat->status,

                'cap_url' => $surat->getFirstMediaUrl('cap'),

                'ttds' => $surat->ttds->map(fn ($ttd) => [
                    'id' => $ttd->id,
                    'nama' => $ttd->nama_penandatangan,
                    'jabatan' => $ttd->jabatan,
                    'url' => $ttd->getFirstMediaUrl('ttd'),
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
            'tanggal_surat' => 'required|date',
            'kode_jenis' => 'required|string|max:10',
        ]);

            $surat = app(\App\Services\Filing\SuratService::class)->create($validated);

            return redirect ()->route('filing.surat.show', $surat->id)
                ->with('success', 'Surat berhasil dibuat dengan nomor surat');
    }
    
    public function create(string $jenis, Surat $surat)
    {
        $this->authorize('create', Surat::class);

        $surat->load(['ttds.media']);

        $allowedJenis = [
            'SPK-BRM',
            'GRS-BRM',
            'SPD-BRM',
            'SK-BRM',
            'IEI-BRM',
            'SPI-BRM',
            'BRM1',
            'BRM2',
        ];

        abort_unless(in_array($jenis, $allowedJenis), 404);

        $surat = Surat::make();

        return Inertia::render("filing/surat/create/{$jenis}", [
            'jenis' => $jenis,

            'surat' => [
            'id' => null,
            'cap_url' => null,
            'ttds' => [],
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
        'tanggal_surat' => 'required|date',
        ]);

        $surat->update($validated);
        $surat->refresh();

        return redirect()
        ->route('filing.surat.show', $surat->id)
        ->with('success', 'Surat berhasil diperbarui');
    }

    public function edit(Surat $surat)
    {
        $this->authorize('update', $surat);

        return Inertia::render('filing/surat/Edit', [
            'surat' => $surat->loadMissing([]),
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
            'cap' => ['required', 'image', 'mimes:png,jpg,jpeg', 'max:10240'],
        ]);
         
        $surat->addMediaFromRequest('cap')->toMediaCollection('cap');

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

    public function deleteTtd(SuratTtd $ttd)
    {
        $surat = $ttd->surat;

        $this->authorize('update', $surat);

        $ttd->clearMediaCollection('ttd');
        $ttd->delete();

        return back()->with('success', 'TTD berhasil dihapus');
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

    public function generatePdf(Request $request, Surat $surat)
    {
        $this->authorize('update', $surat);

        $data = $request->validate([
            'judul' => 'required|string',
            'tanggal_surat' => 'required|date',
            'nama' => 'required|string',
            'jabatan_terakhir' => 'required|string',
            'departemen' => 'required|string',
            'isi_surat' => 'required|string',
        ]);

        $surat->update([
            ...$data,
            'status' => 'final',
        ]);

        // GENERATE PDF KHUSUS SPK
        app(GenerateSpkPdf::class)->handle($surat);

        return redirect()
            ->route('filing.surat.show', $surat->id)
            ->with('success', 'SPK berhasil digenerate');
    }
}