<?php

namespace App\Services\Surat\BRM2;

use App\Models\Surat;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class GenerateBRM2Pdf
{
    public function handle(Surat $surat): void
    {
        $surat->finalize();

        $surat->refresh();

        Carbon::setLocale('id');
        setlocale(LC_TIME, 'id_ID.UTF8');
        
        $surat->load([
            'ttds.media',
            'media',
        ]);

        $pdf = Pdf::loadView('pdf.brm2', [
            'surat' => $surat,
        ])->setPaper('A4', 'portrait');

        // ✅ TEMP FILE (ABSOLUTE PATH)
        $tempPath = storage_path("app/temp/brm2-{$surat->id}.pdf");

        // pastikan folder ada
        if (!is_dir(dirname($tempPath))) {
            mkdir(dirname($tempPath), 0755, true);
        }

        // simpan pdf
        $pdf->save($tempPath);

        if (!file_exists($tempPath)) {
        throw new \RuntimeException("Gagal membuat temp PDF di: {$tempPath}");
        }

        $surat->clearMediaCollection('brm2_pdf');
        // ✅ SIMPAN KE MEDIA LIBRARY
        $surat->addMedia($tempPath)
            ->usingName("BRM2_{$surat->id}.pdf")
            ->toMediaCollection('brm2_pdf');
    }
}