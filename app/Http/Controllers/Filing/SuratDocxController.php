<?php

namespace App\Http\Controllers\Filing;

use App\Http\Controllers\Controller;
use App\Models\Surat;
use App\Services\GenerateSKPDocx;
use App\Services\GenerateIEIDocx;
use App\Services\GenerateGRSDocx;
use App\Services\GenerateSPIDocx;
use App\Services\GenerateSPDDocx;
use App\Services\GenerateBRM1Docx;
use App\Services\GenerateBRM2Docx;
use App\Services\GenerateSKDocx;

class SuratDocxController extends Controller
{
    private function download(Surat $surat, object $service, string $prefix): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $this->authorize('view', $surat);

        try {
            $path     = $service->handle($surat);
            $filename = $prefix . '_' . str_replace('/', '-', $surat->nomor_surat ?? $surat->id) . '.docx';

            return response()->download($path, $filename, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            ])->deleteFileAfterSend(true);

        } catch (\Throwable $e) {
            abort(500, 'Gagal generate DOCX: ' . $e->getMessage());
        }
    }

    public function downloadSKP(Surat $surat)  { return $this->download($surat, app(GenerateSKPDocx::class),  'SKP'); }
    public function downloadIEI(Surat $surat)  { return $this->download($surat, app(GenerateIEIDocx::class),  'IEI'); }
    public function downloadGRS(Surat $surat)  { return $this->download($surat, app(GenerateGRSDocx::class),  'GRS'); }
    public function downloadSPI(Surat $surat)  { return $this->download($surat, app(GenerateSPIDocx::class),  'SPI'); }
    public function downloadSPD(Surat $surat)  { return $this->download($surat, app(GenerateSPDDocx::class),  'SPD'); }
    public function downloadBRM1(Surat $surat) { return $this->download($surat, app(GenerateBRM1Docx::class), 'BRM1'); }
    public function downloadBRM2(Surat $surat) { return $this->download($surat, app(GenerateBRM2Docx::class), 'BRM2'); }
    public function downloadSK(Surat $surat)   { return $this->download($surat, app(GenerateSKDocx::class),   'SK'); }
}