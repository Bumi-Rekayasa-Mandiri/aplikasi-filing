<?php

namespace App\SuratTypes;

use App\Models\Surat;
use App\Services\Surat\SPD\GenerateSPDPdf;
use App\SuratTypes\Contracts\SuratTypeInterface;

class SPDType implements SuratTypeInterface
{
    public function rules(): array
    {
        return [
            'meta.nama_penerima' => 'required|string|max:255',
            'meta.jabatan' => 'required|string|max:255',
        ];
    }

    public function viewCreate(): string
    {
        return 'filing/surat/create/SPD-BRM';
    }

    public function viewPreview(): string
    {
        return 'filing/surat/preview/SPD-BRM';
    }

    public function generatePdf(Surat $surat)
    {
        return app(GenerateSPDPdf::class)->handle($surat);
    }
}