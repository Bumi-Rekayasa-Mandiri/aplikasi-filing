<?php

namespace App\SuratTypes;

use App\Models\Surat;
use App\Services\Surat\SKP\GenerateSKPPdf;
use App\SuratTypes\Contracts\SuratTypeInterface;

class SKPType implements SuratTypeInterface
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
        return 'filing/surat/create/SKP-BRM';
    }

    public function viewPreview(): string
    {
        return 'filing/surat/preview/SKP-BRM';
    }

    public function generatePdf(Surat $surat)
    {
        return app(GenerateSKPPdf::class)->handle($surat);
    }
}