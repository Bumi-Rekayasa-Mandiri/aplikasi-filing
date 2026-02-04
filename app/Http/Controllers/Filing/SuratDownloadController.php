<?php

namespace App\Http\Controllers\Filing;

use App\Http\Controllers\Controller;
use App\Models\Surat;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SuratDownloadController extends Controller
{
    public function download (Surat $surat): StreamedResponse
    {
        Gate::authorize('view', $surat);

        $media = $surat->getFirstMedia('surat_files');
        
        abort_if(! $media, 404, 'File surat tidak ditemukan');

        return  response()->download(
            $media->getPath(),
            $media->file_name, [
                'Content-Type' => $media->mime_type,
            ]
        );
    }
}