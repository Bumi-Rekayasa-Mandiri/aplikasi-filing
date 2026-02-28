<?php

namespace App\SuratTypes;

use App\Models\Surat;
use App\Services\Surat\IEI\GenerateIEIPdf;
use App\SuratTypes\Contracts\SuratTypeInterface;

class IEIType implements SuratTypeInterface
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
        return 'filing/surat/create/IEI-BRM';
    }

    public function viewPreview(): string
    {
        return 'filing/surat/preview/IEI-BRM';
    }

    public function generatePdf(Surat $surat)
    {
        return app(GenerateIEIPdf::class)->handle($surat);
    }
}