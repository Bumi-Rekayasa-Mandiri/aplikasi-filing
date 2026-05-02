<?php

namespace App\Console\Commands;

use App\Services\SuratArchiveService;
use Illuminate\Console\Command;

class ArchiveYearlySurat extends Command
{
    /**
     * php artisan surat:archive-yearly           → arsipkan tahun lalu
     * php artisan surat:archive-yearly --tahun=2024 → arsipkan tahun tertentu
     */
    protected $signature = 'surat:archive-yearly
                            {--tahun= : Tahun yang diarsipkan (default: tahun lalu)}
                            {--force : Jalankan tanpa konfirmasi}';

    protected $description = 'Arsipkan semua surat dari tahun tertentu ke index arsip surat';

    public function handle(SuratArchiveService $service): int
    {
        $tahun = (int) ($this->option('tahun') ?? now()->subYear()->year);

        $this->info("📁 Surat Archive — Tahun {$tahun}");
        $this->newLine();

        // Konfirmasi sebelum eksekusi (kecuali --force)
        if (!$this->option('force')) {
            if (!$this->confirm("Arsipkan semua surat tahun {$tahun}? (surat akan disembunyikan dari manajemen)")) {
                $this->warn('Dibatalkan.');
                return self::FAILURE;
            }
        }

        $this->info("Mengarsipkan surat tahun {$tahun}...");

        try {
            $count = $service->archiveYear($tahun);

            if ($count === 0) {
                $this->warn("⚠️  Tidak ada surat baru yang diarsipkan untuk tahun {$tahun}.");
                $this->line('   (Kemungkinan sudah diarsipkan sebelumnya atau tidak ada surat di tahun tersebut)');
            } else {
                $this->info("✅  Berhasil mengarsipkan {$count} surat tahun {$tahun}.");
            }

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('❌  Gagal mengarsipkan: ' . $e->getMessage());
            return self::FAILURE;
        }
    }
}