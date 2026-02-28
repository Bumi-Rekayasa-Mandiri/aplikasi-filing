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
                    'SKP-BRM' => 'Surat Pemberitahuan atau PHK',
                    'GRS-BRM' => 'Surat Pengajuan Garansi Material',
                    'SPD-BRM' => 'Surat Pengembalian Dana',
                    'SK-BRM' => 'Surat Permohonan Keringanan Denda',
                    'IEI-BRM' => 'Surat Garansi Pekerjaan',
                    'SPI-BRM' => 'Surat Permohonan Investasi',
                    'BRM1' => 'Surat Izin Kerja dan LK3',
                    'BRM2' => 'Surat Pelepasan Hak'
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
// public function uploadCap(Request $request, Surat $surat)
//     {
//         $this->authorize('update', $surat);

//         $request->validate([
//             'cap' => 'required|image|mimes:png,jpg,jpeg|max:10240',
//         ]);

//         $surat->clearMediaCollection('cap');
         
//         $surat->addMediaFromRequest('cap')->toMediaCollection('cap');

//         $this->regeneratePdf($surat);

//         return redirect()
//             ->back()
//             ->with('success', 'Cap perusahaan berhasil diupload');
//     }

// public function uploadTtd(Request $request, Surat $surat)
//     {
//         $this->authorize('update', $surat);

//         $validated = $request->validate([
//             'ttd' => 'required|image|mimes:png,jpg,jpeg|max:2048',
//             'nama_penandatangan' => 'required|string',
//             'jabatan' => 'required|string',
//         ]);

//         $ttd = $surat->ttds()->create([
//             'nama_penandatangan' => $validated['nama_penandatangan'],
//             'jabatan' => $validated['jabatan'],
//         ]);

//         $ttd->addMediaFromRequest('ttd')->toMediaCollection('ttd');

//         $this->regeneratePdf($surat);

//         return redirect()
//             ->back()
//             ->with('success', 'Tanda tangan berhasil ditambahkan');
//     }

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
            'nama_penandatangan' => 'nullable|string',
            'jabatan' => 'nullable|string',
            'ttd' => 'nullable|image',
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
            'nama_penandatangan' => $data['nama_penandatangan'] ?? null,
            'jabatan' => $data['jabatan'] ?? null,
        ]);

        // 🔥 SIMPAN MEDIA
        if ($request->hasFile('cap')) {
            $surat->clearMediaCollection('cap');
            $surat->addMediaFromRequest('cap')->toMediaCollection('cap');
        }

        if ($request->hasFile('ttd')) {
            $surat->clearMediaCollection('ttd');
            $surat->addMediaFromRequest('ttd')->toMediaCollection('ttd');
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
            'nama_penandatangan' => 'nullable|string',
            'jabatan' => 'nullable|string',
            'ttd' => 'nullable|image',
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

        if ($request->hasFile('ttd')) {
            $surat->clearMediaCollection('ttd');
            $surat->addMediaFromRequest('ttd')->toMediaCollection('ttd');
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
                'label'                 => $ttdData['label'] ?? 'Hormat Kami', 
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
            'nama_penandatangan' => 'nullable|string',
            'jabatan' => 'nullable|string',
            'ttd' => 'nullable|image',
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
            'nama_penandatangan' => $data['nama_penandatangan'] ?? null,
            'jabatan' => $data['jabatan'] ?? null,
        ]);

        // 🔥 SIMPAN MEDIA
        if ($request->hasFile('cap')) {
            $surat->clearMediaCollection('cap');
            $surat->addMediaFromRequest('cap')->toMediaCollection('cap');
        }

        if ($request->hasFile('ttd')) {
            $surat->clearMediaCollection('ttd');
            $surat->addMediaFromRequest('ttd')->toMediaCollection('ttd');
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
            'lampiran' => 'nullable|string|max:255',
            'cap' => 'nullable|image',
            'nama_penandatangan' => 'nullable|string',
            'jabatan' => 'nullable|string',
            'ttd' => 'nullable|image',
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
            'nama_penandatangan' => $data['nama_penandatangan'] ?? null,
            'jabatan' => $data['jabatan'] ?? null,
        ]);

        // 🔥 SIMPAN MEDIA
        if ($request->hasFile('cap')) {
            $surat->clearMediaCollection('cap');
            $surat->addMediaFromRequest('cap')->toMediaCollection('cap');
        }

        if ($request->hasFile('ttd')) {
            $surat->clearMediaCollection('ttd');
            $surat->addMediaFromRequest('ttd')->toMediaCollection('ttd');
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
            'nama_penandatangan' => 'nullable|string',
            'jabatan' => 'nullable|string',
            'ttd' => 'nullable|image',
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
            'nama_penandatangan' => $data['nama_penandatangan'] ?? null,
            'jabatan' => $data['jabatan'] ?? null,
        ]);

        // 🔥 SIMPAN MEDIA
        if ($request->hasFile('cap')) {
            $surat->clearMediaCollection('cap');
            $surat->addMediaFromRequest('cap')->toMediaCollection('cap');
        }

        if ($request->hasFile('ttd')) {
            $surat->clearMediaCollection('ttd');
            $surat->addMediaFromRequest('ttd')->toMediaCollection('ttd');
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
            'judul' => 'required|string|max:255',
            'perihal' => 'required|string|max:255',
            'tujuan' => 'required|string|max:255',
            'tanggal_surat' => 'required|date',
            'nama' => 'required|string|max:255',
            'lokasi_kerja' => 'required|string|max:255',
            'jenis_pekerjaan' => 'required|string|max:255',
            'waktu' => 'required|string|max:255',
            'jam_kerja' => 'required|string|max:255',
            'jumlah_pekerja' => 'required|string|max:255',
            'departemen' => 'required|string|max:255',
            'cap' => 'nullable|image',
            'ttds'                      => 'nullable|array',
            'ttds.*.nama_penandatangan' => 'required_with:ttds|string|max:255',
            'ttds.*.urutan'             => 'nullable|integer',
            'ttds.*.label'              => 'nullable|string|max:100',
            'ttds.*.file'               => 'nullable|image|max:2048',
            'apd' => 'nullable|string',
            'periode' => 'nullable|string',
            'no_pekerja' => 'nullable|string',

        ]);

        // 🔥 SIMPAN KE DATABASE (PENTING)
        $surat->update([
            'judul' => $data['judul'],
            'perihal' => $data['perihal'],
            'tujuan' => $data['tujuan'],
            'tanggal_surat' => $data['tanggal_surat'],
            'nama' => $data['nama'],
            'lokasi_kerja' => $data['lokasi_kerja'],
            'jenis_pekerjaan' => $data['jenis_pekerjaan'],
            'waktu' => $data['waktu'],
            'jam_kerja' => $data['jam_kerja'],
            'jumlah_pekerja' => $data['jumlah_pekerja'],
            'departemen' => $data['departemen'],
            'nama_penandatangan' => $data['nama_penandatangan'] ?? null,
            'apd' => $data['apd'],
            'periode' => $data['periode'],
            'no_pekerja' => $data['no_pekerja'],
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
                'jabatan'   => $ttdData['jabatan'] ?? '',
                'urutan'  => $ttdData['urutan'] ?? ($index + 1),
                'label'   => $ttdData['label'] ?? '',
            ]);

            // Upload file TTD jika ada
            if ($request->hasFile("ttds.{$index}.file")) {
                $ttd->clearMediaCollection('ttd');
                $ttd->addMediaFromRequest("ttds.{$index}.file")
                    ->toMediaCollection('ttd');
            }
        }

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
            'nama_penandatangan' => 'nullable|string',
            'jabatan' => 'nullable|string',
            'ttd' => 'nullable|image',
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
            'nama_penandatangan' => $data['nama_penandatangan'] ?? null,
            'jabatan' => $data['jabatan'] ?? null,
        ]);

        // 🔥 SIMPAN MEDIA
        if ($request->hasFile('cap')) {
            $surat->clearMediaCollection('cap');
            $surat->addMediaFromRequest('cap')->toMediaCollection('cap');
        }
        
        if ($request->hasFile('ttd')) {
            $surat->clearMediaCollection('ttd');
            $surat->addMediaFromRequest('ttd')->toMediaCollection('ttd');
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