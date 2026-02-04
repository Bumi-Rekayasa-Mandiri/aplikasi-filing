<?php

namespace App\Services\Surat\SPK;
use App\Models\Surat;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class GenerateSpkPdf
{
    public function handle(Surat $surat)
    {
        $pdf = Pdf::loadView(
            'pdf.spk',
            ['surat' => $surat]
        )->setPaper('A4');

        $path = "surat/SPK-BRM/{$surat->id}.pdf";

        Storage::put($path, $pdf->output());

        $surat->update([
            'pdf_path' => $path,
        ]);
    }
}