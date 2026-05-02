<?php

namespace App\Services;

use App\Models\ArsipSurat;
use App\Models\Surat;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class SuratArchiveService
{
    /**
     * Arsipkan semua surat dari tahun tertentu.
     *
     * - Surat di-hide dari manajemen (archived_at diisi).
     * - Data summary disalin ke arsip_surats.
     * - Surat yang sudah diarsipkan sebelumnya di-skip.
     *
     * @return int Jumlah surat yang berhasil diarsipkan
     */
    public function archiveYear(int $tahun): int
    {
        $count = 0;

        // Ambil semua surat dari tahun tsb yang BELUM diarsipkan
        $surats = Surat::query()
            ->whereYear('tanggal_surat', $tahun)
            ->whereNull('archived_at')
            ->get();

        DB::transaction(function () use ($surats, $tahun, &$count) {
            $now = Carbon::now();

            foreach ($surats as $surat) {
                // Cegah duplikat: skip jika sudah ada di arsip_surats
                $alreadyArchived = ArsipSurat::where('surat_id', $surat->id)->exists();
                if ($alreadyArchived) {
                    // Pastikan surat juga ter-hide di manajemen
                    if (is_null($surat->archived_at)) {
                        $surat->timestamps = false;
                        $surat->archived_at = $now;
                        $surat->save();
                    }
                    continue;
                }

                // Salin data ke arsip_surats
                ArsipSurat::create([
                    'surat_id'    => $surat->id,
                    'tahun'       => $tahun,
                    'judul'       => $surat->judul,
                    'nomor_surat' => $surat->nomor_surat,
                    'tujuan'      => $surat->tujuan,
                    'jenis_surat' => $surat->jenis,
                    'archived_at' => $now,
                ]);

                // Soft-hide dari manajemen surat
                $surat->timestamps = false; // jangan update updated_at
                $surat->archived_at = $now;
                $surat->save();

                $count++;
            }
        });

        return $count;
    }

    /**
     * Restore surat dari arsip kembali ke manajemen.
     *
     * - Hapus record dari arsip_surats (soft delete).
     * - Bersihkan archived_at di surat asli.
     */
    public function restore(ArsipSurat $arsip): bool
    {
        if (is_null($arsip->surat_id)) {
            return false; // arsip lama tanpa FK, tidak bisa di-restore otomatis
        }

        DB::transaction(function () use ($arsip) {
            // Hapus dari arsip (soft delete agar history terjaga)
            $arsip->delete();

            // Bersihkan flag archived_at di surat asli
            Surat::withoutTimestamps(function () use ($arsip) {
                Surat::where('id', $arsip->surat_id)
                    ->whereNotNull('archived_at')
                    ->update(['archived_at' => null]);
            });
        });

        return true;
    }

    /**
     * Daftar tahun yang tersedia untuk diarsipkan
     * (tahun yang ada di tabel surat, belum atau sudah diarsipkan).
     */
    public function availableYears(): array
    {
        return Surat::query()
            ->selectRaw('YEAR(tanggal_surat) as tahun')
            ->whereNotNull('tanggal_surat')
            ->groupBy('tahun')
            ->orderByDesc('tahun')
            ->pluck('tahun')
            ->toArray();
    }

    /**
     * Statistik arsip per tahun.
     */
    public function statsByYear(): array
    {
        return ArsipSurat::query()
            ->selectRaw('tahun, COUNT(*) as total')
            ->whereNotNull('tahun')
            ->groupBy('tahun')
            ->orderByDesc('tahun')
            ->get()
            ->keyBy('tahun')
            ->map(fn($r) => $r->total)
            ->toArray();
    }
}