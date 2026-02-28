<?php

namespace App\SuratTypes;

use App\Models\Surat;
use App\Services\Surat\BRM2\GenerateBRM2Pdf;
use App\SuratTypes\Contracts\SuratTypeInterface;

class BRM2Type implements SuratTypeInterface
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
        return 'filing/surat/create/BRM2';
    }

    public function viewPreview(): string
    {
        return 'filing/surat/preview/BRM2';
    }

    public function generatePdf(Surat $surat)
    {
        return app(GenerateBRM2Pdf::class)->handle($surat);
    }
}