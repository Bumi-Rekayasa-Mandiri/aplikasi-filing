<?php

namespace App\Services\Surat\SPI;

use App\Models\Surat;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class GenerateSPIPdf
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

        $pdf = Pdf::loadView('pdf.spi', [
            'surat' => $surat,
        ])->setPaper('A4', 'portrait');

        // ✅ TEMP FILE (ABSOLUTE PATH)
        $tempPath = storage_path("app/temp/spi-{$surat->id}.pdf");

        // pastikan folder ada
        if (!is_dir(dirname($tempPath))) {
            mkdir(dirname($tempPath), 0755, true);
        }

        // simpan pdf
        $pdf->save($tempPath);

        if (!file_exists($tempPath)) {
        throw new \RuntimeException("Gagal membuat temp PDF di: {$tempPath}");
        }

        $surat->clearMediaCollection('spi_pdf');
        // ✅ SIMPAN KE MEDIA LIBRARY
        $surat->addMedia($tempPath)
                ->usingName("SPI_BRM_{$surat->id}.pdf")
                ->toMediaCollection('spi_pdf');

        // optional: hapus temp file
        //unlink($tempPath);
    }
}
