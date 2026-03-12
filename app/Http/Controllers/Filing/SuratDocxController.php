<?php

namespace App\Http\Controllers\Filing;

use App\Http\Controllers\Controller;
use App\Models\Surat;
use App\Services\GenerateSKPDocx;
use Illuminate\Http\Request;

class SuratDocxController extends Controller
{
    public function downloadSKP(Surat $surat)
    {
        $this->authorize('view', $surat);

        try {
            $path = app(GenerateSKPDocx::class)->handle($surat);

            $filename = 'SKP_' . str_replace('/', '-', $surat->nomor_surat) . '.docx';

            return response()->download($path, $filename, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            ])->deleteFileAfterSend(true);

        } catch (\Throwable $e) {
            return back()->withErrors(['docx' => 'Gagal generate DOCX: ' . $e->getMessage()]);
        }
    }

    // Nanti tambah method lain di sini:
    // public function downloadIEI(Surat $surat) { ... }
    // public function downloadGRS(Surat $surat) { ... }
    // public function downloadSPI(Surat $surat) { ... }
    // dst...
}