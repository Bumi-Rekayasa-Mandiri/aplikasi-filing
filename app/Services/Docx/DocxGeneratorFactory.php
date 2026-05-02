<?php

namespace App\Services\Docx;

use App\Models\Surat;

class DocxGeneratorFactory
{
    public static function make(string $jenis): BaseDocxGenerator
    {
        return match($jenis) {
            'SKP-BRM' => new SKPDocxGenerator(),
            'GRS-BRM' => new GRSDocxGenerator(),
            'SPD-BRM' => new SPDDocxGenerator(),
            'SPI-BRM' => new SPIDocxGenerator(),
            'IEI-BRM' => new IEIDocxGenerator(),
            'SK-BRM'  => new SKDocxGenerator(),
            'BRM-1'   => new BRM1DocxGenerator(),
            'BRM-2'   => new BRM2DocxGenerator(),
            default   => throw new \InvalidArgumentException("Jenis surat tidak dikenal: {$jenis}"),
        };
    }
}