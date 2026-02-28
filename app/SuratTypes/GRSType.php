<?php

namespace App\SuratTypes;

use App\Models\Surat;
use App\Services\Surat\GRS\GenerateGRSPdf;
use App\SuratTypes\Contracts\SuratTypeInterface;

class GRSType implements SuratTypeInterface
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
        return 'filing/surat/create/GRS-BRM';
    }

    public function viewPreview(): string
    {
        return 'filing/surat/preview/GRS-BRM';
    }

    public function generatePdf(Surat $surat)
    {
        return app(GenerateGRSPdf::class)->handle($surat);
    }
}