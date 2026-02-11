<?php

namespace App\Services\Surat\SPK;

use App\Models\Surat;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class GenerateSpkPdf
{
    public function handle(Surat $surat): void
    {
        Carbon::setLocale('id');
        setlocale(LC_TIME, 'id_ID.UTF8');
        
        $surat->load([
            'ttds.media',
            'media',
        ]);

        $pdf = Pdf::loadView('pdf.spk', [
            'surat' => $surat,
        ])->setPaper('A4', 'portrait');

        // ✅ TEMP FILE (ABSOLUTE PATH)
        $tempPath = storage_path("app/temp/spk-{$surat->id}.pdf");

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
            ->usingName("SPK_BRM_{$surat->id}.pdf")
            ->toMediaCollection('pdf');

        // optional: hapus temp file
        //unlink($tempPath);
    }
}
