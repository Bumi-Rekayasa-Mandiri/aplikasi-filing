<?php

namespace App\Services;

use App\Models\Surat;
use App\Models\NomorSuratLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class NomorSuratGenerator
{
    public function generateForSurat(Surat $surat)
    {
        return DB::transaction(function () use ($surat) {

            $now   = now();
            $tahun = $now->year;
            $bulan = $now->month;

            $kodeJenis = $surat->jenis ?? 'BRM';

            $lastRunning = NomorSuratLog::where('tahun', $tahun)
                ->where('kode_jenis', $kodeJenis)
                ->lockForUpdate()
                ->max('running_number');

            $running = ($lastRunning ?? 0) + 1;

            $nomorSurat = sprintf(
                '%03d/%s/%s/%d',
                $running,
                $kodeJenis,
                $this->bulanRomawi($bulan),
                $tahun
            );

            // ✅ Buat log
            $log = NomorSuratLog::create([
                'tahun' => $tahun,
                'bulan' => $bulan,
                'running_number' => $running,
                'kode_jenis' => $kodeJenis,
                'nomor_surat' => $nomorSurat,
                'surat_id' => $surat->id,
            ]);

            // ✅ Update surat
            $surat->update([
                'nomor_surat' => $nomorSurat,
            ]);

                return [
                    'log_id' => $log->id,
                    'nomor_surat' => $nomorSurat,
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