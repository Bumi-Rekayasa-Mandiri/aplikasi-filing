<?php

namespace App\Http\Controllers\Filing;

use App\Http\Controllers\Controller;
use App\Models\ArsipSurat;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ArsipSuratController extends Controller
{
    public function index(Request $request)
    {

    $this->authorize('viewAny', ArsipSurat::class);

    $sortField = $request->input('sortField', 'nomor_surat');
    $sortDirection = $request->input('sortDirection', 'desc');

    $query = ArsipSurat::query();

    if ($request->filled('search')) {
        $query->where(function ($q) use ($request) {
            $q->where('judul', 'like', '%' . $request->search . '%')
              ->orWhere('nomor_surat', 'like', '%' . $request->search . '%')
              ->orWhere('tujuan', 'like', '%' . $request->search . '%')
              ->orWhere('jenis_surat', 'like', '%' . $request->search . '%');
            });
        }

    if ($request->filled('jenis_surat')) {
        $query->where('jenis_surat', $request->jenis_surat);
        }

    $arsip = $query
        ->orderBy($sortField, $sortDirection)
        ->paginate(10)
        ->withQueryString()
        ->through(fn ($a) => [
            'id' => $a->id,
            'nomor_surat' => $a->nomor_surat,
            'judul' => $a->judul,
            'tujuan' => $a->tujuan,
            'jenis_surat' => $a->jenis_surat,
            'file_url' => $a->getFirstMediaUrl('arsip_surat_files'),
            'file_name' => optional(
                $a->getFirstMedia('arsip_surat_files')
            )->file_name,
        ]);

    return Inertia::render('filing/arsip/Index', [
        'arsip'     => $arsip,
        'sort'      => $sortField,
        'direction' => $sortDirection,
        'filters'   => $request->only(['search', 'status']),
        ]);
    }

    public function create()
    {
        return Inertia::render('filing/arsip/Create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'judul' => 'required|string',
            'nomor_surat' => 'nullable|string',
            'tujuan' => 'nullable|string',
            'jenis_surat' => 'nullable|string',
            'file' => 'required|mimes:pdf,docx,doc|max:10240',
        ]);

        $arsip = ArsipSurat::create([
            'judul' => $data['judul'],
            'nomor_surat' => $data['nomor_surat'] ?? null,
            'tujuan' => $data['tujuan'],
            'jenis_surat' => $data['jenis_surat'],
        ]);
        
            $arsip->addMediaFromRequest('file')
            ->toMediaCollection('arsip_surat_files');

        return redirect()
            ->route('filing.arsip.index')
            ->with('success', 'Arsip surat created successfully.');

    }

    public function uploadFile(Request $request, ArsipSurat $arsip)
    {
        $request->validate([
            'file' => 'required|mimes:pdf|max:10240',
        ]);

        $arsip
            ->addMediaFromRequest('file')
            ->toMediaCollection('arsip_surat_files');

        return back();
    }

    public function destroy(ArsipSurat $arsip)
    {
        $this->authorize('delete', $arsip);

        $arsip->clearMediaCollection('arsip_surat_files');

        $arsip->delete();

        return redirect()
            ->route('filing.arsip.index')
            ->with('success', 'Arsip surat deleted successfully.');
    }

    public function download(ArsipSurat $arsip)
    {
        $media = $arsip->getFirstMedia('arsip_surat_files');

        abort_if(!$media, 404);

        return response()->download($media->getPath(), $media->file_name);
    }
    
    public function show(ArsipSurat $arsip)
    {
        $this->authorize('view', $arsip);

        return Inertia::render('filing/arsip/Preview', [
            'arsip' => [
                'id'          => $arsip->id,
                'nomor_surat' => $arsip->nomor_surat,
                'judul'       => $arsip->judul,
                'tujuan'      => $arsip->tujuan,
                'jenis_surat' => $arsip->jenis_surat,
                'file_url'    => $arsip->getFirstMediaUrl('arsip_surat_files'),
                'file_name'   => optional($arsip->getFirstMedia('arsip_surat_files'))->file_name,
                'file_mime'   => optional($arsip->getFirstMedia('arsip_surat_files'))->mime_type,
            ],
        ]);
    }
}