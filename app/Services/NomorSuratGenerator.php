<?php

namespace App\Services;

use App\Models\NomorSuratLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class NomorSuratGenerator
{
    public function generate(string $kodeJenis = 'BRM', ?int $tahunOverride = null): array
    {
        return DB::transaction(function () use ($kodeJenis, $tahunOverride) {

            $now   = Carbon::now();
            $tahun = $tahunOverride ?? $now->year;
            $bulan = $now->month;

            $last = NomorSuratLog::where('tahun', $tahun)
                ->where('kode_jenis', $kodeJenis)
                ->lockForUpdate()
                ->orderByDesc('running_number')
                ->first();

            $running = $last ? $last->running_number + 1 : 1;

            $nomorSurat = sprintf(
                '%03d/%s/%s/%d',
                $running,
                $kodeJenis,
                $this->bulanRomawi($bulan),
                $tahun
            );

            $log = NomorSuratLog::create([
                'tahun'          => $tahun,
                'bulan'          => $bulan,
                'running_number' => $running,
                'nomor_surat'    => $nomorSurat,
                'kode_jenis'     => $kodeJenis,
                'surat_id'       => null,
            ]);

            return [
                'log_id'         => $log->id,     // ← INI YANG HILANG
                'nomor_surat'    => $nomorSurat,
                'running_number' => $running,
                'tahun'          => $tahun,
                'bulan'          => $bulan,
                'kode_jenis'     => $kodeJenis,
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