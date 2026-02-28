<?php

namespace App\Services\Surat\SK;

use App\Models\Surat;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class GenerateSKPdf
{
    public function handle(Surat $surat): void
    {
        $surat->finalize();
        
        Carbon::setLocale('id');
        setlocale(LC_TIME, 'id_ID.UTF8');
        
        $surat->load([
            'ttds.media',
            'media',
        ]);

        $pdf = Pdf::loadView('pdf.sk', [
            'surat' => $surat,
        ])->setPaper('A4', 'portrait');

        // ✅ TEMP FILE (ABSOLUTE PATH)
        $tempPath = storage_path("app/temp/sk-{$surat->id}.pdf");

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
            ->usingName("SK_BRM_{$surat->id}.pdf")
            ->toMediaCollection('pdf');

        // optional: hapus temp file
        //unlink($tempPath);
    }
}