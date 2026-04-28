<?php

namespace App\Services\Surat\IEI;

use App\Models\Surat;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use setasign\Fpdi\Fpdi;

class GenerateIEIPdf
{
    public function handle(Surat $surat): void
    {
        $surat->finalize();
        $surat->refresh();

        Carbon::setLocale('id');
        setlocale(LC_TIME, 'id_ID.UTF8');

        $surat->load(['ttds.media', 'media']);

        // ── Load gambar ────────────────────────────────
        $kopPath           = public_path('assets/lampiran.png');
        $kopBase64         = $this->encodeImage($kopPath, 'image/png', 1200);
        $kopLampiranPath   = public_path('assets/lampiran.png');
        $kopLampiranBase64 = $this->encodeImage($kopLampiranPath, 'image/png', 1200);
        $lampiranPath   = public_path('assets/lampiran.png');
        $lampiranBase64 = $this->encodeImage($lampiranPath, 'image/png', 1200);

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

        $dokumenList = $surat->meta['dokumen'] ?? [];

        // ── Surat utama (tidak diubah) ─────────────────
        $pdf = Pdf::loadView('pdf.iei', [
            'surat' => $surat,
        ])->setPaper('A4', 'portrait');

        $tempMain = storage_path("app/temp/iei-{$surat->id}-main.pdf");
        if (!is_dir(dirname($tempMain))) {
            mkdir(dirname($tempMain), 0755, true);
        }
        $pdf->save($tempMain);

        // ── Lampiran (format surat garansi Inggris) ────
        $pdfLampiran = Pdf::loadView('pdf.iei-lampiran', [
            'surat'             => $surat,
            'kopLampiranBase64' => $kopLampiranBase64,
            'capBase64'         => $capBase64,
            'ttdBase64List'     => $ttdBase64List,
            'lampiranBase64'    => $lampiranBase64,
            'dokumenList'       => $dokumenList,
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

        $tempLampiran = storage_path("app/temp/iei-{$surat->id}-lampiran.pdf");
        $pdfLampiran->save($tempLampiran);

        // ── Gabungkan + simpan ─────────────────────────
        $tempFinal = storage_path("app/temp/iei-{$surat->id}.pdf");
        $this->mergePdf([$tempMain, $tempLampiran], $tempFinal);

        @unlink($tempMain);
        @unlink($tempLampiran);

        if (!file_exists($tempFinal)) {
            throw new \RuntimeException("Gagal membuat PDF di: {$tempFinal}");
        }

        $surat->clearMediaCollection('iei_pdf');
        $surat->addMedia($tempFinal)
            ->usingName("IEI_BRM_{$surat->id}.pdf")
            ->toMediaCollection('iei_pdf');
    }

    private function mergePdf(array $paths, string $outputPath): void
    {
        if (class_exists(\setasign\Fpdi\Fpdi::class)) {
            $fpdi = new Fpdi();
            foreach ($paths as $path) {
                if (!file_exists($path)) continue;
                $count = $fpdi->setSourceFile($path);
                for ($i = 1; $i <= $count; $i++) {
                    $tpl  = $fpdi->importPage($i);
                    $size = $fpdi->getTemplateSize($tpl);
                    $fpdi->AddPage(
                        ($size['width'] > $size['height']) ? 'L' : 'P',
                        [$size['width'], $size['height']]
                    );
                    $fpdi->useTemplate($tpl);
                }
            }
            $fpdi->Output($outputPath, 'F');
            return;
        }

        if (file_exists($paths[0])) {
            copy($paths[0], $outputPath);
        }
    }

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