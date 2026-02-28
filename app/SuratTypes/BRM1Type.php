<?php

namespace App\SuratTypes;

use App\Models\Surat;
use App\Services\Surat\BRM1\GenerateBRM1Pdf;
use App\SuratTypes\Contracts\SuratTypeInterface;

class BRM1Type implements SuratTypeInterface
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
        return 'filing/surat/create/BRM1';
    }

    public function viewPreview(): string
    {
        return 'filing/surat/preview/BRM1';
    }

    public function generatePdf(Surat $surat)
    {
        return app(GenerateBRM1Pdf::class)->handle($surat);
    }
}