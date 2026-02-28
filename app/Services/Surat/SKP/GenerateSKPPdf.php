<?php

namespace App\Services\Surat\SKP;

use App\Models\Surat;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class GenerateSKPPdf
{
    public function handle(Surat $surat)
    {
        $surat->finalize();

        if (!$surat->nomor_surat) {
        app(NomorSuratGenerator::class)
            ->generateForSurat($surat);
        }

        Carbon::setLocale('id');
        setlocale(LC_TIME, 'id_ID.UTF8');
        
        $surat->load([
            'ttds.media',
            'media',
        ]);

        $pdf = Pdf::loadView('pdf.skp', [
            'surat' => $surat,
        ])->setPaper('A4', 'portrait');

        // ✅ TEMP FILE (ABSOLUTE PATH)
        $tempPath = storage_path("app/temp/skp-{$surat->id}.pdf");

        // pastikan folder ada
        if (!is_dir(dirname($tempPath))) {
            mkdir(dirname($tempPath), 0755, true);
        }

        // simpan pdf
        $pdf->save($tempPath);

        // ✅ SIMPAN KE MEDIA LIBRARY
        $surat
            ->clearMediaCollection('pdf')
            ->addMedia($tempPath)
            ->usingName("SKP_BRM_{$surat->id}.pdf")
            ->toMediaCollection('pdf');

        return true;
        // optional: hapus temp file
        //unlink($tempPath);
    }
}