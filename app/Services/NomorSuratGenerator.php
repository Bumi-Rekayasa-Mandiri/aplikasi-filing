<?php

namespace App\Services;

use App\Models\NomorSuratLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class NomorSuratGenerator
{
    public function generate(?string $kodeJenis = null): array
    {
        return DB::transaction(function () use ($kodeJenis) {

            $now   = Carbon::now();
            $tahun = $now->year;
            $bulan = $now->month;

            $last = NomorSuratLog::where('tahun', $tahun)
                ->when($kodeJenis, fn ($q) => $q->where('kode_jenis', $kodeJenis))
                ->orderByDesc('running_number')
                ->lockForUpdate()
                ->first();

            $runningNumber = $last ? $last->running_number + 1 : 1;

            return [
                'running_number' => $runningNumber,
                'nomor_surat' => sprintf(
                    '%03d/%s/%s/%d',
                    $runningNumber,
                    $kodeJenis ?? 'BRM',
                    $this->bulanRomawi($bulan),
                    $tahun
                ),
                'tahun' => $tahun,
                'bulan' => $bulan,
                'kode_jenis' => $kodeJenis,
            ];
        });
    }

    private function bulanRomawi(int $bulan): string
    {
        return [
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV',
            5 => 'V', 6 => 'VI', 7 => 'VII', 8 => 'VIII',
            9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII',
        ][$bulan];
    }
}
