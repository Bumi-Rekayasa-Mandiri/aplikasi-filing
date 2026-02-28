<?php

namespace App\SuratTypes;

use App\SuratTypes\Contracts\SuratTypeInterface;

class SuratTypeFactory
{
    public static function make(string $kode): SuratTypeInterface
    {
        return match ($kode) {
            'SKP-BRM' => app(SKPType::class),
            'SPI-BRM' => app(SPIType::class),
            'SPD-BRM' => app(SPDType::class),
            'SK-BRM'  => app(SKType::class),
            'IEI-BRM' => app(IEIType::class),
            'GRS-BRM' => app(GRSType::class),
            'BRM1' => app(BRM1Type::class),
            'BRM2' => app(BRM2Type::class),
            default => throw new \Exception("Jenis surat tidak dikenali: {$kode}"),
        };
    }
}