<?php

namespace App\Http\Controllers\Filing;

use App\Http\Controllers\Controller;
use App\Models\ArsipSurat;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ArsipSuratController extends Controller
{
    public function index()
    {
        return Inertia::render('filing/arsip/Index', [
            'data' => ArsipSurat::latest()->get(),
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
            'tahun' => 'nullable|string',
            'kategori' => 'nullable|string',
            'keterangan' => 'nullable|string',
        ]);

        ArsipSurat::create($data);

        return redirect()->route('filing.arsip.Index');
    }

    public function uploadFile(Request $request, ArsipSurat $arsip)
    {
        $request->validate([
            'file' => 'required|mimes:pdf|max:10240',
        ]);

        $arsip
            ->addMediaFromRequest('file')
            ->toMediaCollection('arsip');

        return back();
    }
}