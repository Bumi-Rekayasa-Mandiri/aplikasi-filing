<?php

namespace App\Services\Surat\BRM1;

use App\Models\Surat;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use setasign\Fpdi\Fpdi;

class GenerateBRM1Pdf
{
    public function handle(Surat $surat): void
    {
        $surat->finalize();
        $surat->refresh();

        Carbon::setLocale('id');
        setlocale(LC_TIME, 'id_ID.UTF8');

        $surat->load(['ttds.media', 'media']);

        // ── TAMBAHAN: load gambar ──────────────────────
        $kopPath   = public_path('assets/kop.png');
        $kopBase64 = $this->encodeImage($kopPath, 'image/png', 1200);
        $kopLampiranPath   = public_path('assets/koplampiran.png');
        $kopLampiranBase64 = $this->encodeImage($kopLampiranPath, 'image/png', 1200);

        $capBase64 = null;
        $capMedia  = $surat->getFirstMedia('cap');
        if ($capMedia) {
            $capBase64 = $this->encodeImage(
                str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $capMedia->getPath()),
                $capMedia->mime_type
            );
        }

        $ttdBase64List = [];
        foreach ($surat->ttds as $i => $ttd) {
            $ttdMedia = $ttd->getFirstMedia('ttd');
            if ($ttdMedia) {
                $ttdBase64List[$i] = $this->encodeImage(
                    str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $ttdMedia->getPath()),
                    $ttdMedia->mime_type
                );
            }
        }

        // ── TAMBAHAN: load KTP pekerja ─────────────────
        $ktpBase64List = [];
        $ktpMediaList  = $surat->getMedia('ktp');
        foreach ($ktpMediaList as $idx => $ktp) {
            $ktpBase64List[$idx] = $this->encodeImage(
                str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $ktp->getPath()),
                $ktp->mime_type,
                800   // KTP butuh resolusi cukup agar terbaca
            );
        }

        // ── TAMBAHAN: parse JSA dari jenis_pekerjaan ──
        $jsaItems      = $surat->meta['jsa']     ?? [];
        $pekerjaDaftar = $surat->meta['pekerja'] ?? [];

        // ── Surat utama (format lama tetap sama) ──────
        $pdf = Pdf::loadView('pdf.brm1', [
            'surat' => $surat,
        ])->setPaper('A4', 'portrait');

        $tempMain = storage_path("app/temp/brm1-{$surat->id}-main.pdf");
        if (!is_dir(dirname($tempMain))) {
            mkdir(dirname($tempMain), 0755, true);
        }
        $pdf->save($tempMain);

        // ── TAMBAHAN: lampiran (JSA + Daftar Pekerja) ─
        $pdfLampiran = Pdf::loadView('pdf.brm1-lampiran', [
            'surat'                 => $surat,
            'kopLampiranBase64'     => $kopLampiranBase64,
            'capBase64'             => $capBase64,
            'ttdBase64List'         => $ttdBase64List,
            'ktpBase64List'         => $ktpBase64List,
            'jsaItems'              => $jsaItems,
            'pekerjaDaftar'         => $pekerjaDaftar,
        ])
        ->setPaper('A4', 'portrait')
        ->setOption([
            'isHtml5ParserEnabled'    => true,
            'isRemoteEnabled'         => true,
            'isFontSubsettingEnabled' => true,
            'defaultFont'             => 'serif',
            'dpi'                     => 96,
            'chroot'                  => base_path(),
        ]);

        $tempLampiran = storage_path("app/temp/brm1-{$surat->id}-lampiran.pdf");
        $pdfLampiran->save($tempLampiran);

        // ── Gabungkan main + lampiran ──────────────────
        $tempFinal = storage_path("app/temp/brm1-{$surat->id}.pdf");
        $this->mergePdf([$tempMain, $tempLampiran], $tempFinal);

        // Hapus temp file sementara
        @unlink($tempMain);
        @unlink($tempLampiran);

        if (!file_exists($tempFinal)) {
            throw new \RuntimeException("Gagal membuat PDF di: {$tempFinal}");
        }

        $surat->clearMediaCollection('brm1_pdf');
        $surat->addMedia($tempFinal)
            ->usingName("BRM1_{$surat->id}.pdf")
            ->toMediaCollection('brm1_pdf');
    }

    /**
     * Gabungkan beberapa PDF menjadi satu menggunakan FPDI (jika tersedia)
     * atau fallback ke copy file pertama + append raw bytes
     */
    private function mergePdf(array $paths, string $outputPath): void
    {
        // Cek apakah FPDI tersedia
        if (class_exists(\setasign\Fpdi\Fpdi::class)) {
            $fpdi = new Fpdi();
            foreach ($paths as $path) {
                if (!file_exists($path)) continue;
                $count = $fpdi->setSourceFile($path);
                for ($i = 1; $i <= $count; $i++) {
                    $tpl = $fpdi->importPage($i);
                    $size = $fpdi->getTemplateSize($tpl);
                    $fpdi->AddPage($size['orientation'] ?? 'P', [$size['width'], $size['height']]);
                    $fpdi->useTemplate($tpl);
                }
            }
            $fpdi->Output($outputPath, 'F');
            return;
        }

        // Fallback: simpan sebagai dua file terpisah jika FPDI tidak ada
        // Dalam hal ini, hanya simpan file pertama dan log warning
        if (file_exists($paths[0])) {
            copy($paths[0], $outputPath);
        }
        \Log::warning('FPDI tidak tersedia, lampiran tidak digabungkan. Install: composer require setasign/fpdi');
    }

    /**
     * Helper encode gambar ke base64 dengan optional resize
     */
    private function encodeImage(string $path, ?string $mimeType = null, int $maxW = 400): ?string
    {
        if (!file_exists($path) || filesize($path) === 0) return null;

        $info = getimagesize($path);
        if (!$info) return null;

        [$origW, $origH, $type] = $info;
        $mime = $mimeType ?? $info['mime'];

        if ($origW > $maxW) {
            $ratio = $maxW / $origW;
            $newW  = $maxW;
            $newH  = (int) ($origH * $ratio);

            $src = match($type) {
                IMAGETYPE_PNG  => imagecreatefrompng($path),
                IMAGETYPE_JPEG => imagecreatefromjpeg($path),
                default        => null,
            };

            if (!$src) return null;

            $dst = imagecreatetruecolor($newW, $newH);

            if ($type === IMAGETYPE_PNG) {
                imagealphablending($dst, false);
                imagesavealpha($dst, true);
                $transparent = imagecolorallocatealpha($dst, 255, 255, 255, 127);
                imagefilledrectangle($dst, 0, 0, $newW, $newH, $transparent);
            }

            imagecopyresampled($dst, $src, 0, 0, 0, 0, $newW, $newH, $origW, $origH);

            ob_start();
            if ($type === IMAGETYPE_PNG) imagepng($dst);
            else imagejpeg($dst, null, 85);
            $imageData = ob_get_clean();

            imagedestroy($src);
            imagedestroy($dst);

            return 'data:' . $mime . ';base64,' . base64_encode($imageData);
        }

        return 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($path));
    }
}