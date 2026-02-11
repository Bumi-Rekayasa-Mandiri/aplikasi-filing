<?php

namespace App\Http\Controllers\Filing;

use App\Http\Controllers\Controller;
use App\Models\ArsipSertifikat;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ArsipSertifikatController extends Controller
{
    public function index(Request $request)
    {

    $this->authorize('viewAny', ArsipSertifikat::class);

    $sortField = $request->input('sortField', 'nomor_sertifikat');
    $sortDirection = $request->input('sortDirection', 'desc');

    $query = ArsipSertifikat::query();

    if ($request->filled('search')) {
        $query->where(function ($q) use ($request) {
            $q->where('nama_sertifikat', 'like', '%' . $request->search . '%')
              ->orWhere('nomor_sertifikat', 'like', '%' . $request->search . '%')
              ->orWhere('instansi', 'like', '%' . $request->search . '%')
              ->orWhere('jenis_sertifikat', 'like', '%' . $request->search . '%');
            });
        }

    if ($request->filled('jenis_sertifikat')) {
        $query->where('jenis_sertifikat', $request->jenis_sertifikat);
        }

    $sertifikat = $query
        ->orderBy($sortField, $sortDirection)
        ->paginate(10)
        ->withQueryString()
        ->through(fn ($se) => [
            'id' => $se->id,
            'nomor_sertifikat' => $se->nomor_sertifikat,
            'nama_sertifikat' => $se->nama_sertifikat,
            'instansi' => $se->instansi,
            'jenis_sertifikat' => $se->jenis_sertifikat,
            'file_url' => $se->getFirstMediaUrl('arsip_sertifikat_files'),
            'file_name' => optional(
                $se->getFirstMedia('arsip_sertifikat_files')
            )->file_name,
        ]);

    return Inertia::render('filing/sertifikat/Index', [
        'sertifikat'    => $sertifikat,
        'sort'          => $sortField,
        'direction'     => $sortDirection,
        'filters'       => $request->only(['search', 'jenis_sertifikat']),
        ]);
    }

    public function create()
    {
        return Inertia::render('filing/sertifikat/Create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_sertifikat' => 'required|string',
            'nomor_sertifikat' => 'nullable|string',
            'instansi' => 'nullable|string',
            'jenis_sertifikat' => 'nullable|string',
            'file' => 'required|mimes:pdf,png,jpg,jpeg|max:10240',
        ]);

        $sertifikat = ArsipSertifikat::create([
            'nama_sertifikat' => $data['nama_sertifikat'],
            'nomor_sertifikat' => $data['nomor_sertifikat'] ?? null,
            'instansi' => $data['instansi'],
            'jenis_sertifikat' => $data['jenis_sertifikat'],
        ]);

        
            $sertifikat->addMediaFromRequest('file')
            ->toMediaCollection('arsip_sertifikat_files');

        return redirect()
            ->route('filing.sertifikat.index')
            ->with('success', 'Arsip sertifikat created successfully.');
    }

    public function uploadFile(Request $request, ArsipSertifikat $sertifikat)
    {
        $request->validate([
            'file' => 'required|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        $sertifikat
            ->addMediaFromRequest('file')
            ->toMediaCollection('arsip_sertifikat_files');

        return back();
    }

    public function destroy(ArsipSertifikat $sertifikat)
    {
        $this->authorize('delete', $sertifikat);

        $sertifikat->clearMediaCollection('arsip_sertifikat_files');
        $sertifikat->delete();

        return redirect()
            ->route('filing.sertifikat.index')
            ->with('success', 'Arsip sertifikat deleted successfully.');
    }

    public function download(ArsipSertifikat $sertifikat)
    {
        $media = $sertifikat->getFirstMedia('arsip_sertifikat_files');
        
        abort_if(!$media, 404);

        return response()->download($media->getPath(), $media->file_name);
    }   
}