<?php

namespace App\Http\Controllers\Filing;

use App\Http\Controllers\Controller;
use App\Models\Surat;
use App\Models\SuratCap;
use App\Models\SuratTtd;
use App\Models\FileUpload;
use App\Models\NomorSuratLog;
use App\Models\SuratRecentView;
use App\Services\NomorSuratGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Gate;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Services\Surat\SKP\GenerateSKPPdf;
use App\Services\Surat\SK\GenerateSKPdf;
use App\Services\Surat\SPD\GenerateSPDPdf;
use App\Services\Surat\SPI\GenerateSPIPdf;
use App\Services\Surat\IEI\GenerateIEIPdf;
use App\Services\Surat\GRS\GenerateGRSPdf;
use App\Services\Surat\BRM1\GenerateBRM1Pdf;
use App\Services\Surat\BRM2\GenerateBRM2Pdf;
use Inertia\Inertia;
use Carbon\Carbon;

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

    $perPage = (int) $request->input('per_page', 10);
    $perPage = in_array($perPage, [10, 25, 50]) ? $perPage : 10;

    // FINAL EXECUTION (WAJIB)
    $surat = $query
        ->orderBy($sortField, $sortDirection)
        ->paginate($perPage)
        ->withQueryString();

    return Inertia::render('filing/surat/Index', [
        'surat'     => $surat,
        'sort'      => $sortField,
        'direction' => $sortDirection,
        'filters'   => $request->only(['search', 'status']),
        'per_page'  => $perPage, 
    ]);
}

public function show(Surat $surat)
{
    return Inertia::render('filing/surat/Show', [
        'surat' => [
            // ── INFO UMUM ──────────────────────────
            'id'           => $surat->id,
            'jenis'        => $surat->jenis,
            'nomor_surat'  => $surat->nomor_surat,
            'judul'        => $surat->judul,
            'perihal'      => $surat->perihal,
            'tujuan'       => $surat->tujuan,
            'tanggal_surat'=> $surat->tanggal_surat,
            'status'       => $surat->status,
            'lampiran'     => $surat->lampiran,
            'isi_surat'    => $surat->isi_surat,

            // ── IDENTITAS ──────────────────────────
            'nama'             => $surat->nama,
            'alamat'           => $surat->alamat,
            'no_ktp'           => $surat->no_ktp,
            'jabatan'          => $surat->jabatan,
            'departemen'       => $surat->departemen,
            'jabatan_terakhir' => $surat->jabatan_terakhir,

            // ── FINANSIAL ──────────────────────────
            'nominal'           => $surat->nominal,
            'nominal_bagihasil' => $surat->nominal_bagihasil,
            'item_pembelian'    => $surat->item_pembelian,
            'hasil_denda'       => $surat->hasil_denda,
            'keringanan_denda'  => $surat->keringanan_denda,

            // ── PEKERJAAN ──────────────────────────
            'project'        => $surat->project,
            'material'       => $surat->material,
            'lokasi_kerja'   => $surat->lokasi_kerja,
            'jenis_pekerjaan'=> $surat->jenis_pekerjaan,
            'waktu'          => $surat->waktu,
            'jam_kerja'      => $surat->jam_kerja,
            'jumlah_pekerja' => $surat->jumlah_pekerja,
            'masa_garansi'   => $surat->masa_garansi,
            'apd'            => $surat->apd,
            'periode'        => $surat->periode,
            'no_pekerja'     => $surat->no_pekerja,

            // ── KENDARAAN ──────────────────────────
            'merk'   => $surat->merk,
            'warna'  => $surat->warna,
            'rangka' => $surat->rangka,

            // ── MEDIA ──────────────────────────────
            'cap_url' => $surat->getFirstMediaUrl('cap'),
            'ttds'    => $surat->ttds->map(fn($ttd) => [
                'id'      => $ttd->id,
                'nama'    => $ttd->nama_penandatangan,
                'jabatan' => $ttd->jabatan,
                'label'   => $ttd->label,
                'url'     => $ttd->getFirstMediaUrl('ttd'),
            ]),
        ],
    ]);
}
    
public function create(string $jenis)
{
        $this->authorize('create', Surat::class);

        $allowedJenis = [
            'SKP-BRM',
            'GRS-BRM',
            'SPD-BRM',
            'SK-BRM',
            'IEI-BRM',
            'SPI-BRM',
            'BRM-1',
            'BRM-2',
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

public function edit(Surat $surat, string $jenis)
{
    $this->authorize('update', $surat);

    return Inertia::render('filing/surat/Edit', [
        'surat' => $surat,
        'jenis' => $jenis,
        'can'   => [
            'approve'     => Gate::allows('approve', $surat),
            'revertDraft' => Gate::allows('revertDraft', $surat),
        ],
    ]);
}

public function update(Request $request, Surat $surat)
{
    $this->authorize('update', $surat);

    // Validasi field umum
    $rules = [
        'judul'         => 'required|string|max:255',
        'perihal'       => 'nullable|string|max:255',
        'tujuan'        => 'nullable|string|max:255',
        'isi_surat'     => 'nullable|string',
        'tanggal_surat' => 'required|date',
    ];

    // Validasi tambahan per jenis
    $extraRules = match($surat->jenis) {
        'BRM-1'   => [
            'nama'           => 'nullable|string',
            'departemen'     => 'nullable|string',
            'lokasi_kerja'   => 'nullable|string',
            'jenis_pekerjaan'=> 'nullable|string',
            'waktu'          => 'nullable|string',
            'jam_kerja'      => 'nullable|string',
            'jumlah_pekerja' => 'nullable|string',
            'apd'            => 'nullable|string',
            'periode'        => 'nullable|string',
            'no_pekerja'     => 'nullable|string',
        ],
        'BRM-2'   => [
            'merk'   => 'nullable|string',
            'warna'  => 'nullable|string',
            'rangka' => 'nullable|string',
        ],
        'IEI-BRM' => [
            'project'         => 'nullable|string',
            'lokasi_kerja'    => 'nullable|string',
            'jenis_pekerjaan' => 'nullable|string',
            'masa_garansi'    => 'nullable|string',
        ],
        'GRS-BRM' => [
            'project'      => 'nullable|string',
            'material'     => 'nullable|string',
            'alamat'       => 'nullable|string',
            'masa_garansi' => 'nullable|string',
        ],
        'SK-BRM'  => [
            'hasil_denda'      => 'nullable|string',
            'keringanan_denda' => 'nullable|string',
        ],
        'SKP-BRM' => [
            'nama'             => 'nullable|string',
            'jabatan_terakhir' => 'nullable|string',
            'departemen'       => 'nullable|string',
        ],
        'SPD-BRM' => [
            'lampiran'      => 'nullable|string',
            'alamat'        => 'nullable|string',
            'item_pembelian'=> 'nullable|string',
            'nominal'       => 'nullable|string',
            'nama'          => 'nullable|string',
            'no_ktp'        => 'nullable|string',
        ],
        'SPI-BRM' => [
            'nama'              => 'nullable|string',
            'alamat'            => 'nullable|string',
            'no_ktp'            => 'nullable|string',
            'nominal'           => 'nullable|string',
            'nominal_bagihasil' => 'nullable|string',
        ],
        default => [],
    };

    $validated = $request->validate(array_merge($rules, $extraRules));

    $surat->update($validated);

    return redirect()
        ->route('filing.surat.show', $surat->id)
        ->with('success', 'Surat berhasil diperbarui');
}

public function destroy(Surat $surat)
{
    $this->authorize('delete', $surat);

    // Hapus semua media surat (cap, pdf, dll)
    $surat->deleteAllMedia();

    // Hapus semua TTD berelasi beserta media-nya
    foreach ($surat->ttds as $ttd) {
        $ttd->deleteAllMedia();
        $ttd->delete();
    }

    // Hapus record surat
    $surat->delete();

    $query = array_filter([
            'status'        => request('status'),
            'search'        => request('search'),
            'sortField'     => request('sortField'),
            'sortDirection' => request('sortDirection'),
            'page'          => request('page'),
        ]);

        $redirectUrl = route('filing.surat.index');

        if (!empty($query)) {
            $redirectUrl .= '?' . http_build_query($query);
        }

        return Inertia::location($redirectUrl);
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

private function regenerateSKPPdf(Surat $surat): void
    {
        $surat->fresh()->load([
                'ttds.media',
                'media',
        ]);
        
        app(\App\Services\Surat\SKP\GenerateSKPPdf::class)
        ->handle($surat);
    }

public function generateSKPPdf(Request $request, Surat $surat)
    {
        $data = $request->validate([
            'judul' => 'required|string|max:255',
            'perihal' => 'required|string|max:255',
            'tujuan' => 'required|string|max:255',
            'isi_surat' => 'required|string',
            'tanggal_surat' => 'required|date',
            'nama' => 'required|string|max:255',
            'jabatan_terakhir' => 'required|string|max:255',
            'departemen' => 'required|string|max:255',
            'cap' => 'nullable|image',
            'ttds'                        => 'nullable|array',
            'ttds.*.nama_penandatangan'   => 'required_with:ttds|string|max:255',
            'ttds.*.jabatan'              => 'nullable|string|max:255',
            'ttds.*.urutan'               => 'nullable|integer',
            'ttds.*.label'                => 'nullable|string|max:100',
            'ttds.*.file'                 => 'nullable|image|max:2048',
        ]);

        // 🔥 SIMPAN KE DATABASE (PENTING)
        $surat->update([
            'judul' => $data['judul'],
            'perihal' => $data['perihal'],
            'tujuan' => $data['tujuan'],
            'isi_surat' => $data['isi_surat'],
            'tanggal_surat' => $data['tanggal_surat'],
            'nama' => $data['nama'],
            'jabatan_terakhir' => $data['jabatan_terakhir'],
            'departemen' => $data['departemen'],
        ]);

        // 🔥 SIMPAN MEDIA
        if ($request->hasFile('cap')) {
            $surat->clearMediaCollection('cap');
            $surat->addMediaFromRequest('cap')->toMediaCollection('cap');
        }

        $surat->ttds()->delete();

        foreach ($request->input('ttds', []) as $index => $ttdData) {
            $ttd = $surat->ttds()->create([
                'nama_penandatangan' => $ttdData['nama_penandatangan'],
                'jabatan'            => $ttdData['jabatan'] ?? '',
                'urutan'             => $ttdData['urutan'] ?? ($index + 1),
                'label'              => $ttdData['label'] ?? '',
            ]);

            if ($request->hasFile("ttds.{$index}.file")) {
                $ttd->clearMediaCollection('ttd');
                $ttd->addMediaFromRequest("ttds.{$index}.file")
                    ->toMediaCollection('ttd');
            }
        }

        // 🔥 GENERATE NOMOR + PDF
        app(GenerateSKPPdf::class)->handle($surat);

        return redirect()->route('filing.surat.previewSKP', $surat->id);
    }

public function previewSKP(Surat $surat)
    {
        $this->authorize('view', $surat);

        SuratRecentView::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'surat_id' => $surat->id,
            ],
            [
                'last_viewed_at' => Carbon::now(),
            ]
        );

        $pdfUrl = $surat->getFirstMediaUrl('pdf');

        abort_if(empty($pdfUrl), 404);

        return inertia('filing/surat/preview/SKP-BRM', [
            'surat' => [
                'id' => $surat->id,
                'nomor_surat' => $surat->nomor_surat,
                'status' => $surat->status,
                'pdf_url' => $pdfUrl,
            ],
        ]);
    }

public function generateSKPdf(Request $request, Surat $surat)
    {
        $data = $request->validate([
            'judul' => 'required|string|max:255',
            'perihal' => 'required|string|max:255',
            'tujuan' => 'required|string|max:255',
            'isi_surat' => 'required|string',
            'tanggal_surat' => 'required|date',
            'hasil_denda' => 'required|string|max:255',
            'keringanan_denda' => 'required|string|max:255',
            'cap' => 'nullable|image',
            'ttds'                        => 'nullable|array',
            'ttds.*.nama_penandatangan'   => 'required_with:ttds|string|max:255',
            'ttds.*.jabatan'              => 'nullable|string|max:255',
            'ttds.*.urutan'               => 'nullable|integer',
            'ttds.*.label'                => 'nullable|string|max:100',
            'ttds.*.file'                 => 'nullable|image|max:2048',
        ]);

        // 🔥 SIMPAN KE DATABASE (PENTING)
        $surat->update([
            'judul' => $data['judul'],
            'perihal' => $data['perihal'],
            'tujuan' => $data['tujuan'],
            'isi_surat' => $data['isi_surat'],
            'tanggal_surat' => $data['tanggal_surat'],
            'hasil_denda' => $data['hasil_denda'],
            'keringanan_denda' => $data['keringanan_denda'],
            'nama_penandatangan' => $data['nama_penandatangan'] ?? null,
            'jabatan' => $data['jabatan'] ?? null,
        ]);

        // 🔥 SIMPAN MEDIA
        if ($request->hasFile('cap')) {
            $surat->clearMediaCollection('cap');
            $surat->addMediaFromRequest('cap')->toMediaCollection('cap');
        }

        $surat->ttds()->delete();

        foreach ($request->input('ttds', []) as $index => $ttdData) {
            $ttd = $surat->ttds()->create([
                'nama_penandatangan' => $ttdData['nama_penandatangan'],
                'jabatan'            => $ttdData['jabatan'] ?? '',
                'urutan'             => $ttdData['urutan'] ?? ($index + 1),
                'label'              => $ttdData['label'] ?? '',
            ]);

            if ($request->hasFile("ttds.{$index}.file")) {
                $ttd->clearMediaCollection('ttd');
                $ttd->addMediaFromRequest("ttds.{$index}.file")
                    ->toMediaCollection('ttd');
            }
        }

        // 🔥 GENERATE NOMOR + PDF
        app(GenerateSKPdf::class)->handle($surat);

        return redirect()->route('filing.surat.previewSK', $surat->id);
    }

public function previewSK(Surat $surat)
    {
        $this->authorize('view', $surat);

        SuratRecentView::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'surat_id' => $surat->id,
            ],
            [
                'last_viewed_at' => Carbon::now(),
            ]
        );

        $pdfUrl = $surat->getFirstMediaUrl('pdf');

        abort_if(empty($pdfUrl), 404);

        return inertia('filing/surat/preview/SK-BRM', [
            'surat' => [
                'id' => $surat->id,
                'nomor_surat' => $surat->nomor_surat,
                'status' => $surat->status,
                'pdf_url' => $pdfUrl,
            ],
        ]);
    }

public function generateSPIPdf(Request $request, Surat $surat)
    {
        $data = $request->validate([
            'judul' => 'required|string|max:255',
            'perihal' => 'required|string|max:255',
            'tujuan' => 'required|string|max:255',
            'isi_surat' => 'required|string',
            'tanggal_surat' => 'required|date',
            'nama' => 'required|string|max:255',
            'alamat' => 'required|string|max:255',
            'no_ktp' => 'required|string|max:255',
            'nominal' => 'required|string|max:255',
            'nominal_bagihasil' => 'required|string|max:255',
            'cap' => 'nullable|image',
            'ttds'                            => 'nullable|array',
            'ttds.*.nama_penandatangan'       => 'required_with:ttds|string|max:255',
            'ttds.*.jabatan'                  => 'required_with:ttds|string|max:255',
            'ttds.*.urutan'                   => 'nullable|integer',
            'ttds.*.file'                     => 'nullable|image|max:2048',
        ]);

        // 🔥 SIMPAN KE DATABASE (PENTING)
        $surat->update([
            'judul' => $data['judul'],
            'perihal' => $data['perihal'],
            'tujuan' => $data['tujuan'],
            'isi_surat' => $data['isi_surat'],
            'tanggal_surat' => $data['tanggal_surat'],
            'nama' => $data['nama'],
            'alamat' => $data['alamat'],
            'no_ktp' => $data['no_ktp'],
            'nominal' => $data['nominal'],
            'nominal_bagihasil' => $data['nominal_bagihasil'],
        ]);

        // 🔥 SIMPAN MEDIA
        if ($request->hasFile('cap')) {
            $surat->clearMediaCollection('cap');
            $surat->addMediaFromRequest('cap')->toMediaCollection('cap');
        }

        $surat->ttds()->delete();

        foreach ($request->input('ttds', []) as $index => $ttdData) {
            $ttd = $surat->ttds()->create([
                'nama_penandatangan'    => $ttdData['nama_penandatangan'],
                'jabatan'               => $ttdData['jabatan'],
                'urutan'                => $ttdData['urutan'] ?? ($index + 1),
                'label'                 => $ttdData['label'] ?? '', 
            ]);

            // Upload file TTD jika ada
            if ($request->hasFile("ttds.{$index}.file")) {
                $ttd->clearMediaCollection('ttd');
                $ttd->addMediaFromRequest("ttds.{$index}.file")
                    ->toMediaCollection('ttd');
            }
        }

        try {
            app(GenerateSPIPdf::class)->handle($surat);
        } catch (\Throwable $e) {
            return back()->withErrors(['generate' => 'Gagal: ' . $e->getMessage()]);
        }

        // Hapus dd(), ganti Inertia::location()
        $surat->refresh();
        $pdfUrl = $surat->getFirstMediaUrl('spi_pdf');

        if (empty($pdfUrl)) {
            return back()->withErrors([
                'generate' => 'PDF tidak ditemukan di media library.'
            ]);
        }

        return Inertia::location(
            route('filing.surat.previewSPI', ['surat' => $surat->id])  // ← fix di sini
        );
    }

public function previewSPI(Surat $surat)
    {
        $this->authorize('view', $surat);

        SuratRecentView::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'surat_id' => $surat->id,
            ],
            [
                'last_viewed_at' => Carbon::now(),
            ]
        );

        $pdfUrl = $surat->getFirstMediaUrl('spi_pdf');

        // ✅ Tambahkan ini sementara

        abort_if(empty($pdfUrl), 404);

        return inertia('filing/surat/preview/SPI-BRM', [
            'surat' => [
                'id' => $surat->id,
                'nomor_surat' => $surat->nomor_surat,
                'status' => $surat->status,
                'pdf_url' => $pdfUrl,
            ],
        ]);
    }

public function generateSPDPdf(Request $request, Surat $surat)
    {
        $data = $request->validate([
            'judul' => 'required|string|max:255',
            'perihal' => 'required|string|max:255',
            'lampiran' => 'required|string|max:255',
            'tujuan' => 'required|string|max:255',
            'isi_surat' => 'required|string',
            'tanggal_surat' => 'required|date',
            'alamat' => 'required|string|max:255',
            'item_pembelian' => 'required|string|max:255',
            'nominal' => 'required|string|max:255',
            'no_ktp' => 'required|string|max:255',
            'nama' => 'required|string|max:255',
            'cap' => 'nullable|image',
            'ttds'                        => 'nullable|array',
            'ttds.*.nama_penandatangan'   => 'required_with:ttds|string|max:255',
            'ttds.*.jabatan'              => 'nullable|string|max:255',
            'ttds.*.urutan'               => 'nullable|integer',
            'ttds.*.label'                => 'nullable|string|max:100',
            'ttds.*.file'                 => 'nullable|image|max:2048',
        ]);

        // 🔥 SIMPAN KE DATABASE (PENTING)
        $surat->update([
            'judul' => $data['judul'],
            'perihal' => $data['perihal'],
            'lampiran' => $data['lampiran'],
            'tujuan' => $data['tujuan'],
            'isi_surat' => $data['isi_surat'],
            'tanggal_surat' => $data['tanggal_surat'],
            'alamat' => $data['alamat'],
            'item_pembelian' => $data['item_pembelian'],
            'nominal' => $data['nominal'],
            'no_ktp' => $data['no_ktp'],
            'nama' => $data['nama'],
        ]);

        // 🔥 SIMPAN MEDIA
        if ($request->hasFile('cap')) {
            $surat->clearMediaCollection('cap');
            $surat->addMediaFromRequest('cap')->toMediaCollection('cap');
        }

        $surat->ttds()->delete();

        foreach ($request->input('ttds', []) as $index => $ttdData) {
            $ttd = $surat->ttds()->create([
                'nama_penandatangan' => $ttdData['nama_penandatangan'],
                'jabatan'            => $ttdData['jabatan'] ?? '',
                'urutan'             => $ttdData['urutan'] ?? ($index + 1),
                'label'              => $ttdData['label'] ?? '',
            ]);

            if ($request->hasFile("ttds.{$index}.file")) {
                $ttd->clearMediaCollection('ttd');
                $ttd->addMediaFromRequest("ttds.{$index}.file")
                    ->toMediaCollection('ttd');
            }
        }

        // 🔥 GENERATE NOMOR + PDF
        app(GenerateSPDPdf::class)->handle($surat);

        return redirect()->route('filing.surat.previewSPD', $surat->id);
    }

public function previewSPD(Surat $surat)
    {
        $this->authorize('view', $surat);

        SuratRecentView::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'surat_id' => $surat->id,
            ],
            [
                'last_viewed_at' => Carbon::now(),
            ]
        );

        $pdfUrl = $surat->getFirstMediaUrl('pdf');

        abort_if(empty($pdfUrl), 404);

        return inertia('filing/surat/preview/SPD-BRM', [
            'surat' => [
                'id' => $surat->id,
                'nomor_surat' => $surat->nomor_surat,
                'status' => $surat->status,
                'pdf_url' => $pdfUrl,
            ],
        ]);
    }

public function generateIEIPdf(Request $request, Surat $surat)
    {
        $data = $request->validate([
            'judul' => 'required|string|max:255',
            'perihal' => 'required|string|max:255',
            'tujuan' => 'required|string|max:255',
            'isi_surat' => 'required|string|max:255',
            'tanggal_surat' => 'required|date',
            'project' => 'required|string|max:255',
            'lokasi_kerja' => 'required|string|max:255',
            'jenis_pekerjaan'            => 'nullable|array|max:6',
            'jenis_pekerjaan.*.deskripsi' => 'required_with:jenis_pekerjaan|string|max:500',
            'lampiran' => 'nullable|string|max:1000',
            'cap' => 'nullable|image',
            'ttds'                        => 'nullable|array',
            'ttds.*.nama_penandatangan'   => 'required_with:ttds|string|max:255',
            'ttds.*.jabatan'              => 'nullable|string|max:255',
            'ttds.*.urutan'               => 'nullable|integer',
            'ttds.*.label'                => 'nullable|string|max:100',
            'ttds.*.file'                 => 'nullable|image|max:2048',
            'dokumen'                     => 'nullable|array',
            'dokumen.*'                   => 'nullable|string|max:255',
        ]);

        // 🔥 SIMPAN KE DATABASE (PENTING)
        $surat->update([
            'judul' => $data['judul'],
            'perihal' => $data['perihal'],
            'tujuan' => $data['tujuan'],
            'isi_surat' => $data['isi_surat'],
            'tanggal_surat' => $data['tanggal_surat'],
            'project' => $data['project'],
            'lokasi_kerja' => $data['lokasi_kerja'],
            'jenis_pekerjaan' => json_encode(
                            collect($data['jenis_pekerjaan'] ?? [])
                                ->pluck('deskripsi')
                                ->filter()
                                ->values()
                                ->toArray()
                        ),
            'lampiran' => $data['lampiran'],
        ]);

        $surat->update([
            'meta' => [
                'dokumen' => array_filter($request->input('dokumen', [])),
            ],
        ]);

        // 🔥 SIMPAN MEDIA
        if ($request->hasFile('cap')) {
            $surat->clearMediaCollection('cap');
            $surat->addMediaFromRequest('cap')->toMediaCollection('cap');
        }

        $surat->ttds()->delete();

        foreach ($request->input('ttds', []) as $index => $ttdData) {
            $ttd = $surat->ttds()->create([
                'nama_penandatangan' => $ttdData['nama_penandatangan'],
                'jabatan'            => $ttdData['jabatan'] ?? '',
                'urutan'             => $ttdData['urutan'] ?? ($index + 1),
                'label'              => $ttdData['label'] ?? '',
            ]);

            if ($request->hasFile("ttds.{$index}.file")) {
                $ttd->clearMediaCollection('ttd');
                $ttd->addMediaFromRequest("ttds.{$index}.file")
                    ->toMediaCollection('ttd');
            }
        }

        // ✅ Wrap service call
        try {
            app(GenerateIEIPdf::class)->handle($surat);
        } catch (\Throwable $e) {
            // Tampilkan error ke Inertia — jangan silent fail
            return back()->withErrors([
                'generate' => 'Gagal generate PDF: ' . $e->getMessage()
            ]);
        }

    // ✅ Verifikasi PDF benar-benar tersimpan sebelum redirect
        $surat->refresh();
        $pdfUrl = $surat->getFirstMediaUrl('iei_pdf');

        if (empty($pdfUrl)) {
            return back()->withErrors([
                'generate' => 'PDF berhasil diproses tapi tidak ditemukan di media library. Cek storage & Spatie config.'
            ]);
        }

        // 🔥 GENERATE NOMOR + PDF
        //app(GenerateSPIPdf::class)->handle($surat);

        return Inertia::location(route('filing.surat.previewIEI', $surat)
        );
    }

public function previewIEI(Surat $surat)
    {
        $this->authorize('view', $surat);

        SuratRecentView::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'surat_id' => $surat->id,
            ],
            [
                'last_viewed_at' => Carbon::now(),
            ]
        );

        $pdfUrl = $surat->getFirstMediaUrl('iei_pdf');

        abort_if(empty($pdfUrl), 404);

        return inertia('filing/surat/preview/IEI-BRM', [
            'surat' => [
                'id' => $surat->id,
                'nomor_surat' => $surat->nomor_surat,
                'status' => $surat->status,
                'pdf_url' => $pdfUrl,
            ],
        ]);
    }

public function generateGRSPdf(Request $request, Surat $surat)
    {
        $data = $request->validate([
            'judul' => 'required|string|max:255',
            'perihal' => 'required|string|max:255',
            'tujuan' => 'required|string|max:255',
            'isi_surat' => 'required|string',
            'tanggal_surat' => 'required|date',
            'project' => 'required|string|max:255',
            'material' => 'required|string|max:255',
            'alamat' => 'required|string|max:255',
            'masa_garansi' => 'required|string|max:255',
            'cap' => 'nullable|image',
            'ttds'                        => 'nullable|array',
            'ttds.*.nama_penandatangan'   => 'required_with:ttds|string|max:255',
            'ttds.*.jabatan'              => 'nullable|string|max:255',
            'ttds.*.urutan'               => 'nullable|integer',
            'ttds.*.label'                => 'nullable|string|max:100',
            'ttds.*.file'                 => 'nullable|image|max:2048',
        ]);

        // 🔥 SIMPAN KE DATABASE (PENTING)
        $surat->update([
            'judul' => $data['judul'],
            'perihal' => $data['perihal'],
            'tujuan' => $data['tujuan'],
            'isi_surat' => $data['isi_surat'],
            'tanggal_surat' => $data['tanggal_surat'],
            'project' => $data['project'],
            'material' => $data['material'],
            'alamat' => $data['alamat'],
            'masa_garansi' => $data['masa_garansi'],
        ]);

        // 🔥 SIMPAN MEDIA
        if ($request->hasFile('cap')) {
            $surat->clearMediaCollection('cap');
            $surat->addMediaFromRequest('cap')->toMediaCollection('cap');
        }

        $surat->ttds()->delete();

        foreach ($request->input('ttds', []) as $index => $ttdData) {
            $ttd = $surat->ttds()->create([
                'nama_penandatangan' => $ttdData['nama_penandatangan'],
                'jabatan'            => $ttdData['jabatan'] ?? '',
                'urutan'             => $ttdData['urutan'] ?? ($index + 1),
                'label'              => $ttdData['label'] ?? '',
            ]);

            if ($request->hasFile("ttds.{$index}.file")) {
                $ttd->clearMediaCollection('ttd');
                $ttd->addMediaFromRequest("ttds.{$index}.file")
                    ->toMediaCollection('ttd');
            }
        }

        // 🔥 GENERATE NOMOR + PDF
        app(GenerateGRSPdf::class)->handle($surat);

        return redirect()->route('filing.surat.previewGRS', $surat->id);
    }

public function previewGRS(Surat $surat)
    {
        $this->authorize('view', $surat);

        SuratRecentView::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'surat_id' => $surat->id,
            ],
            [
                'last_viewed_at' => Carbon::now(),
            ]
        );

        $pdfUrl = $surat->getFirstMediaUrl('grs_pdf');

        abort_if(empty($pdfUrl), 404);

        return inertia('filing/surat/preview/GRS-BRM', [
            'surat' => [
                'id' => $surat->id,
                'nomor_surat' => $surat->nomor_surat,
                'status' => $surat->status,
                'pdf_url' => $pdfUrl,
            ],
        ]);
    }

public function generateBRM1Pdf(Request $request, Surat $surat)
{
    $data = $request->validate([
        'judul'                         => 'required|string|max:255',
        'perihal'                       => 'required|string|max:255',
        'tujuan'                        => 'required|string|max:255',
        'tanggal_surat'                 => 'required|date',
        'nama'                          => 'required|string|max:255',
        'lokasi_kerja'                  => 'required|string|max:255',
        'jenis_pekerjaan'               => 'required|string|max:255',
        'waktu'                         => 'required|string|max:255',
        'jam_kerja'                     => 'required|string|max:255',
        'jumlah_pekerja'                => 'required|string|max:255',
        'departemen'                    => 'required|string|max:255',
        'cap'                           => 'nullable|image',
        'ttds'                          => 'nullable|array',
        'ttds.*.nama_penandatangan'     => 'required_with:ttds|string|max:255',
        'ttds.*.jabatan'                => 'nullable|string|max:255',
        'ttds.*.urutan'                 => 'nullable|integer',
        'ttds.*.label'                  => 'nullable|string|max:100',
        'ttds.*.file'                   => 'nullable|image|max:2048',
        'apd'                           => 'nullable|string',
        'periode'                       => 'nullable|string',
        'no_pekerja'                    => 'nullable|string',
        'jsa'                           => 'nullable|array',
        'jsa.*.urutan_kerja'            => 'nullable|string',
        'jsa.*.potensi_bahaya'          => 'nullable|string',
        'jsa.*.upaya_pengendalian'      => 'nullable|string',
        'pekerja'                       => 'nullable|array',
        'pekerja.*.nama'                => 'nullable|string|max:255',
        'pekerja.*.role'                => 'nullable|string|max:100',
        'pekerja.*.ktp'                 => 'nullable|image|max:5120',
    ]);

    // ── Simpan data utama ──────────────────────────
    $surat->update([
        'judul'           => $data['judul'],
        'perihal'         => $data['perihal'],
        'tujuan'          => $data['tujuan'],
        'tanggal_surat'   => $data['tanggal_surat'],
        'nama'            => $data['nama'],
        'lokasi_kerja'    => $data['lokasi_kerja'],
        'jenis_pekerjaan' => $data['jenis_pekerjaan'],
        'waktu'           => $data['waktu'],
        'jam_kerja'       => $data['jam_kerja'],
        'jumlah_pekerja'  => $data['jumlah_pekerja'],
        'departemen'      => $data['departemen'],
        'apd'             => $data['apd'],
        'periode'         => $data['periode'],
        'no_pekerja'      => $data['no_pekerja'],
    ]);

    // Simpan JSA + daftar pekerja ke meta
    $surat->update([
        'meta' => [          
            'jsa'     => $request->input('jsa', []),
            'pekerja' => collect($request->input('pekerja', []))
                ->map(fn($p) => [
                    'nama' => $p['nama'] ?? '',
                    'role' => $p['role'] ?? 'Pekerja',
                ])
                ->toArray(),
        ],
    ]);

    // ── Simpan cap ─────────────────────────────────
    if ($request->hasFile('cap')) {
        $surat->clearMediaCollection('cap');
        $surat->addMediaFromRequest('cap')->toMediaCollection('cap');
    }

    // ── Simpan TTD ─────────────────────────────────
    $surat->ttds()->delete();
    foreach ($request->input('ttds', []) as $index => $ttdData) {
        $ttd = $surat->ttds()->create([
            'nama_penandatangan' => $ttdData['nama_penandatangan'],
            'jabatan'            => $ttdData['jabatan'] ?? '',
            'urutan'             => $ttdData['urutan'] ?? ($index + 1),
            'label'              => $ttdData['label'] ?? '',
        ]);

        if ($request->hasFile("ttds.{$index}.file")) {
            $ttd->clearMediaCollection('ttd');
            $ttd->addMediaFromRequest("ttds.{$index}.file")
                ->toMediaCollection('ttd');
        }
    }

    // ── Simpan foto KTP pekerja ────────────────────
    $surat->clearMediaCollection('ktp');
    foreach ($request->file('pekerja', []) as $idx => $p) {
        if (!empty($p['ktp'])) {
            $surat->addMedia($p['ktp'])
                ->usingFileName("ktp-{$idx}-" . time() . '.jpg')
                ->toMediaCollection('ktp');
        }
    }

    // ── Generate PDF ───────────────────────────────
    try {
        app(GenerateBRM1Pdf::class)->handle($surat);
    } catch (\Throwable $e) {
        return back()->withErrors(['generate' => 'Gagal: ' . $e->getMessage()]);
    }

    $surat->refresh();
    $pdfUrl = $surat->getFirstMediaUrl('brm1_pdf');
    if (empty($pdfUrl)) {
        return back()->withErrors(['generate' => 'PDF tidak ditemukan di media library.']);
    }

    return Inertia::location(route('filing.surat.previewBRM1', ['surat' => $surat->id]));
}

    public function previewBRM1(Surat $surat)
    {
        $this->authorize('view', $surat);

        SuratRecentView::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'surat_id' => $surat->id,
            ],
            [
                'last_viewed_at' => Carbon::now(),
            ]
        );

        $pdfUrl = $surat->getFirstMediaUrl('brm1_pdf');

        abort_if(empty($pdfUrl), 404);

        return inertia('filing/surat/preview/BRM-1', [
            'surat' => [
                'id' => $surat->id,
                'nomor_surat' => $surat->nomor_surat,
                'status' => $surat->status,
                'pdf_url' => $pdfUrl,
            ],
        ]);
    }

public function generateBRM2Pdf(Request $request, Surat $surat)
    {
        $data = $request->validate([
            'judul' => 'required|string|max:255',
            'perihal' => 'required|string|max:255',
            'tujuan' => 'required|string|max:255',
            'isi_surat' => 'nullable|string',
            'tanggal_surat' => 'required|date',
            'merk' => 'required|string|max:255',
            'warna' => 'required|string|max:255',
            'rangka' => 'required|string|max:255',
            'cap' => 'nullable|image',
            'ttds'                        => 'nullable|array',
            'ttds.*.nama_penandatangan'   => 'required_with:ttds|string|max:255',
            'ttds.*.jabatan'              => 'nullable|string|max:255',
            'ttds.*.urutan'               => 'nullable|integer',
            'ttds.*.label'                => 'nullable|string|max:100',
            'ttds.*.file'                 => 'nullable|image|max:2048',
        ]);

        // 🔥 SIMPAN KE DATABASE (PENTING)
        $surat->update([
            'judul' => $data['judul'],
            'perihal' => $data['perihal'],
            'tujuan' => $data['tujuan'],
            'isi_surat' => $data['isi_surat'],
            'tanggal_surat' => $data['tanggal_surat'],
            'merk' => $data['merk'],
            'warna' => $data['warna'],
            'rangka' => $data['rangka'],
        ]);

        // 🔥 SIMPAN MEDIA
        if ($request->hasFile('cap')) {
            $surat->clearMediaCollection('cap');
            $surat->addMediaFromRequest('cap')->toMediaCollection('cap');
        }
        
        $surat->ttds()->delete();

        foreach ($request->input('ttds', []) as $index => $ttdData) {
            $ttd = $surat->ttds()->create([
                'nama_penandatangan' => $ttdData['nama_penandatangan'],
                'jabatan'            => $ttdData['jabatan'] ?? '',
                'urutan'             => $ttdData['urutan'] ?? ($index + 1),
                'label'              => $ttdData['label'] ?? '',
            ]);

            if ($request->hasFile("ttds.{$index}.file")) {
                $ttd->clearMediaCollection('ttd');
                $ttd->addMediaFromRequest("ttds.{$index}.file")
                    ->toMediaCollection('ttd');
            }
        }

        // 🔥 GENERATE NOMOR + PDF
        app(GenerateBRM2Pdf::class)->handle($surat);

        return redirect()->route('filing.surat.previewBRM2', $surat->id);
    }

    public function previewBRM2(Surat $surat)
    {
        $this->authorize('view', $surat);

        SuratRecentView::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'surat_id' => $surat->id,
            ],
            [
                'last_viewed_at' => Carbon::now(),
            ]
        );

        $pdfUrl = $surat->getFirstMediaUrl('brm2_pdf');

        abort_if(empty($pdfUrl), 404);

        return inertia('filing/surat/preview/BRM-2', [
            'surat' => [
                'id' => $surat->id,
                'nomor_surat' => $surat->nomor_surat,
                'status' => $surat->status,
                'pdf_url' => $pdfUrl,
            ],
        ]);
    }
}