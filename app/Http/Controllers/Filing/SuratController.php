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
use Barryvdh\DomPDF\Facade\Pdf;
use App\Services\Surat\SPK\GenerateSpkPdf;
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
    // public function store(Request $request)
    // {
            // $this->authorize('create', Surat::class);

            // $validated = $request->validate([
            // 'judul'      => 'required|string|max:255',
            // 'perihal'    => 'required|string|max:255',
            // 'tujuan'     => 'required|string|max:255',
            // 'isi_surat'  => 'required|string',
            // 'tanggal_surat' => 'required|date',
            // 'kode_jenis' => 'required|string|max:10',
        // ]);

            // $surat = app(\App\Services\Filing\SuratService::class)->create($validated);

            // return redirect ()->route('filing.surat.show', $surat->id)
                // ->with('success', 'Surat berhasil dibuat dengan nomor surat');
    // }
    
    public function create(string $jenis)
    {
        $this->authorize('create', Surat::class);

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

        DB::beginTransaction();

        try {
            $surat = Surat::create([
                'jenis' => $jenis,
                'status' => 'draft',
                'created_by' => auth()->id(),
            ]);

            DB::commit();

            return Inertia::render("filing/surat/create/{$jenis}", [
                'surat' => $surat->load('ttds'),
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
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
     * DELETE SURAT
     */
    public function destroy(Surat $surat)
    {
        $this->authorize('delete', $surat);

        $surat->delete();

        return Inertia::render('filing/surat/Index')->with('success', 'Surat berhasil dihapus');
    }

    /**
     * UPLOAD CAP / TTD
     */
    public function uploadCap(Request $request, Surat $surat)
    {
        $this->authorize('update', $surat);

        $request->validate([
            'cap' => 'required|image|mimes:png,jpg,jpeg|max:10240',
        ]);

        $surat->clearMediaCollection('cap');
         
        $surat->addMediaFromRequest('cap')->toMediaCollection('cap');

        $this->regeneratePdf($surat);

        return redirect()
            ->back()
            ->with('success', 'Cap perusahaan berhasil diupload');
    }

    public function uploadTtd(Request $request, Surat $surat)
    {
        $this->authorize('update', $surat);

        $validated = $request->validate([
            'ttd' => 'required|image|mimes:png,jpg,jpeg|max:2048',
            'nama_penandatangan' => 'required|string',
            'jabatan' => 'required|string',
        ]);

        $ttd = $surat->ttds()->create([
            'nama_penandatangan' => $validated['nama_penandatangan'],
            'jabatan' => $validated['jabatan'],
        ]);

        $ttd->addMediaFromRequest('ttd')->toMediaCollection('ttd');

        $this->regeneratePdf($surat);

        return redirect()
            ->back()
            ->with('success', 'Tanda tangan berhasil ditambahkan');
    }

    public function deleteTtd(SuratTtd $ttd)
    {
        $surat = $ttd->surat;

        $this->authorize('update', $surat);

        $ttd->clearMediaCollection('ttd');
        $ttd->delete();

        $this->regeneratePdf($surat);

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

    private function regeneratePdf(Surat $surat): void
    {
        $surat->fresh()->load([
                'ttds.media',
                'media',
        ]);
        
        app(\App\Services\Surat\SPK\GenerateSpkPdf::class)
        ->handle($surat);
    }

    public function generatePdf(Request $request, Surat $surat)
    {
        $this->authorize('update', $surat);

        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'perihal' => 'required|string|max:255',
            'tujuan' => 'required|string|max:255',
            'isi_surat' => 'required|string',
            'tanggal_surat' => 'required|date',
            'nama' => 'required|string|max:255',
            'jabatan_terakhir' => 'required|string|max:255',
            'departemen' => 'required|string|max:255',
        ]);

        DB::transaction(function () use ($surat, $validated) {
            $surat->update($validated);

            if (!$surat->nomor_surat) {
                $result = app(NomorSuratGenerator::class)
                    ->generate($surat->jenis);

                // $surat->update([
                //     'nomor_surat' => $result['nomor_surat'],
                //     'status' => Surat::STATUS_APPROVED,
                // ]);

                \App\Models\NomorSuratLog::where('id', $result['log_id'])
                    ->update(['surat_id' => $surat->id]);
            }

            $this->regeneratePdf($surat);
        });

        return redirect()->route('filing.surat.preview', $surat);
    }

    public function preview(Surat $surat)
    {
        $this->authorize('view', $surat);

        abort_if(!$surat->hasMedia('pdf'), 404);

        return inertia('filing/surat/preview/SPK-BRM', [
            'surat' => [
                'id' => $surat->id,
                'nomor_surat' => $surat->nomor_surat,
                'status' => $surat->status,
                'pdf_url' => $surat->getFirstMediaUrl('pdf'),
            ],
        ]);
    }

    public function approve(Surat $surat)
    {
        $this->authorize('approve', $surat);

        abort_if(!$surat->hasMedia('pdf'), 409, 'Surat belum memiliki PDF.');
        abort_if($surat->ttds()->count() === 0, 409, 'Surat belum memiliki tanda tangan.');

        $surat->update(['status' => Surat::STATUS_APPROVED]);

        return redirect()->back()->with('success', 'Surat berhasil disetujui');
    }
}