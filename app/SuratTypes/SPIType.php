<?php

namespace App\SuratTypes;

use App\Models\Surat;
use App\Services\Surat\SPI\GenerateSPIPdf;
use App\SuratTypes\Contracts\SuratTypeInterface;

class SPIType implements SuratTypeInterface
{
    public function rules(): array
    {
        return [
            'meta.tujuan' => 'required|string',
            'meta.ruang_lingkup' => 'required|string',
        ];
    }

    public function viewCreate(): string
    {
        return 'filing/surat/create/SPI-BRM';
    }

    public function viewPreview(): string
    {
        return 'filing/surat/preview/SPI-BRM';
    }

    public function generatePdf(Surat $surat)
    {
        return app(GenerateSPIPdf::class)->handle($surat);
    }
}