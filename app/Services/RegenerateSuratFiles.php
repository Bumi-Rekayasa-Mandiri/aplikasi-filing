<?php
 
namespace App\Services;
 
use App\Models\Surat;
 
// PDF generators
use App\Services\Surat\SKP\GenerateSKPPdf;
use App\Services\Surat\SK\GenerateSKPdf;
use App\Services\Surat\SPI\GenerateSPIPdf;
use App\Services\Surat\SPD\GenerateSPDPdf;
use App\Services\Surat\IEI\GenerateIEIPdf;
use App\Services\Surat\GRS\GenerateGRSPdf;
use App\Services\Surat\BRM1\GenerateBRM1Pdf;
use App\Services\Surat\BRM2\GenerateBRM2Pdf;
 
// DOCX generators
use App\Services\GenerateSKPDocx;
use App\Services\GenerateSKDocx;
use App\Services\GenerateSPIDocx;
use App\Services\GenerateSPDDocx;
use App\Services\GenerateIEIDocx;
use App\Services\GenerateGRSDocx;
use App\Services\GenerateBRM1Docx;
use App\Services\GenerateBRM2Docx;
 
class RegenerateSuratFiles
{
    /**
     * Regenerate PDF + DOCX untuk surat tertentu.
     * Dipanggil otomatis setelah update (jika status draft).
     *
     * @return array ['pdf' => bool, 'docx' => bool, 'errors' => array]
     */
    public function handle(Surat $surat): array
    {
        $result = [
            'pdf'    => false,
            'docx'   => false,
            'errors' => [],
        ];
 
        // 1. Regenerate PDF
        try {
            $this->regeneratePdf($surat);
            $result['pdf'] = true;
        } catch (\Throwable $e) {
            $result['errors']['pdf'] = $e->getMessage();
            \Log::error("Regenerate PDF failed for surat #{$surat->id}: " . $e->getMessage());
        }
 
        // 2. Regenerate DOCX
        try {
            $this->regenerateDocx($surat);
            $result['docx'] = true;
        } catch (\Throwable $e) {
            $result['errors']['docx'] = $e->getMessage();
            \Log::error("Regenerate DOCX failed for surat #{$surat->id}: " . $e->getMessage());
        }
 
        // 3. Update timestamp jika minimal salah satu berhasil
        if ($result['pdf'] || $result['docx']) {
            $surat->forceFill([
                'last_regenerated_at' => now(),
            ])->save();
        }
 
        return $result;
    }
 
    /**
     * Regenerate PDF berdasarkan jenis surat.
     */
    private function regeneratePdf(Surat $surat): void
    {
        $service = match ($surat->jenis) {
            'SKP-BRM' => app(GenerateSKPPdf::class),
            'SK-BRM'  => app(GenerateSKPdf::class),
            'SPI-BRM' => app(GenerateSPIPdf::class),
            'SPD-BRM' => app(GenerateSPDPdf::class),
            'IEI-BRM' => app(GenerateIEIPdf::class),
            'GRS-BRM' => app(GenerateGRSPdf::class),
            'BRM-1'   => app(GenerateBRM1Pdf::class),
            'BRM-2'   => app(GenerateBRM2Pdf::class),
            default   => throw new \InvalidArgumentException("Jenis surat tidak dikenal: {$surat->jenis}"),
        };
 
        $service->handle($surat);
    }
 
    /**
     * Regenerate DOCX berdasarkan jenis surat.
     */
    private function regenerateDocx(Surat $surat): void
    {
        $service = match ($surat->jenis) {
            'SKP-BRM' => app(GenerateSKPDocx::class),
            'SK-BRM'  => app(GenerateSKDocx::class),
            'SPI-BRM' => app(GenerateSPIDocx::class),
            'SPD-BRM' => app(GenerateSPDDocx::class),
            'IEI-BRM' => app(GenerateIEIDocx::class),
            'GRS-BRM' => app(GenerateGRSDocx::class),
            'BRM-1'   => app(GenerateBRM1Docx::class),
            'BRM-2'   => app(GenerateBRM2Docx::class),
            default   => throw new \InvalidArgumentException("Jenis surat tidak dikenal: {$surat->jenis}"),
        };
 
        $service->handle($surat);
    }
}