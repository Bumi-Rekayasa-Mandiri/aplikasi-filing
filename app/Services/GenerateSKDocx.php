<?php

namespace App\Services;

use App\Models\Surat;
use Carbon\Carbon;
use PhpOffice\PhpWord\SimpleType\Jc;

class GenerateSKDocx extends BaseDocxGenerator
{
    public function handle(Surat $surat): string
    {
        $this->setup();
        $tgl = Carbon::parse($surat->tanggal_surat)->locale('id')->translatedFormat('d F Y');

        // ── KOP ───────────────────────────────────────
        $this->addKop();

        $this->section->addText(
            'e-mail : bumirekayasa.mandiri@gmail.com Phone : 0267-8639-837 / Fax: 0267-8639-837',
            ['size' => 10, 'name' => $this->fontName],
            ['alignment' => Jc::CENTER, 'spaceAfter' => 200]
        );

        // ── JUDUL ─────────────────────────────────────
        $this->section->addTextRun([
            'alignment' => Jc::CENTER, 'spaceBefore' => 100, 'spaceAfter' => 200,
        ])->addText('PERMOHONAN KERINGANAN DENDA', [
            'bold' => true, 'size' => 14,
            'name' => $this->fontName, 'underline' => 'single',
        ]);

        // ── NOMOR & TANGGAL ───────────────────────────
        $this->addNomorTanggal($surat);

        // ── Kepada Yth ────────────────────────────────
        $this->section->addText(
            'Kepada Yth. ' . ($surat->tujuan ?? '—'),
            ['size' => $this->fontSize, 'name' => $this->fontName],
            ['spaceAfter' => 80]
        );

        $this->section->addText('Dengan hormat,', [
            'size' => $this->fontSize, 'name' => $this->fontName,
        ], ['spaceAfter' => 80]);

        $this->addParagraf(
            'Sehubungan dengan adanya evaluasi atas progres pekerjaan dan pengajuan tambahan pekerjaan ' .
            'yang telah kami sampaikan sebelumnya, bersama ini kami dari PT Bumi Rekayasa Mandiri ' .
            'mengajukan permohonan agar nilai denda yang dikenakan dapat disesuaikan.'
        );

        $this->addParagraf('Adapun berdasarkan hasil perhitungan awal, total nilai denda sebesar:');

        // ── Hasil denda — bold ───────────────────────
        $this->section->addText(
            $surat->hasil_denda ?? '—',
            ['bold' => true, 'size' => $this->fontSize, 'name' => $this->fontName],
            ['alignment' => Jc::BOTH, 'spaceBefore' => 80, 'spaceAfter' => 80]
        );

        $this->addParagraf('Kami memohon keringanan denda agar nilai denda dikenakan sebesar :');

        // ── Keringanan denda — bold ───────────────────
        $this->section->addText(
            $surat->keringanan_denda ?? '—',
            ['bold' => true, 'size' => $this->fontSize, 'name' => $this->fontName],
            ['alignment' => Jc::BOTH, 'spaceBefore' => 80, 'spaceAfter' => 80]
        );

        $this->addParagraf(
            'yang mana jumlah tersebut akan langsung dipotong dari nilai tagihan kami, sesuai dengan ' .
            'usulan dan pertimbangan nilai pekerjaan tambahan yang telah kami laksanakan di luar ' .
            'addendum awal.'
        );

        $this->addParagraf(
            'Demikian surat ini kami sampaikan. Besar harapan kami agar permohonan ini dapat diterima ' .
            'demi kelancaran kerja sama antara kedua belah pihak.'
        );

        $this->addParagraf('Atas perhatian dan kerja samanya, kami ucapkan terima kasih.');

        $this->addSpacing();

        // ── TTD rata kanan dengan tanggal + Hormat Kami ─
        $this->addTtdRataKananSK($surat, $tgl);

        return $this->save('SK', $surat->id);
    }

    private function addTtdRataKananSK(Surat $surat, string $tgl): void
    {
        $ttd     = $surat->ttds->first();
        $nama    = $ttd?->nama_penandatangan ?? 'Ilman Sunaryo';
        $jabatan = $ttd?->jabatan ?? 'Direktur';

        $capMedia = $surat->getFirstMedia('cap');
        $capPath  = $capMedia
            ? str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $capMedia->getPath()) : null;

        $ttdMedia = $ttd?->getFirstMedia('ttd');
        $ttdPath  = $ttdMedia
            ? str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $ttdMedia->getPath()) : null;

        $style = ['borderSize' => 0, 'borderColor' => 'FFFFFF'];
        $cellStyle = ['borderSize' => 0, 'borderColor' => 'FFFFFF'];
        $table = $this->section->addTable($style);

        // Tanggal
        $row = $table->addRow();
        $row->addCell(5812, $cellStyle)->addText('');
        $row->addCell(4513, $cellStyle)
            ->addText("Karawang, {$tgl}",
                ['size' => $this->fontSize, 'name' => $this->fontName],
                ['alignment' => Jc::CENTER]);

        // Hormat Kami
        $row = $table->addRow();
        $row->addCell(5812, $cellStyle)->addText('');
        $row->addCell(4513, $cellStyle)
            ->addText('Hormat Kami',
                ['size' => $this->fontSize, 'name' => $this->fontName],
                ['alignment' => Jc::CENTER]);

        // Gambar
        $row = $table->addRow();
        $row->addCell(5812, $cellStyle)->addText('');
        $imgCell = $row->addCell(4513, $cellStyle);

        $imgAdded = false;
        if ($capPath && $ttdPath && file_exists($capPath) && file_exists($ttdPath)) {
            $merged = $this->mergeCapTtd($capPath, $ttdPath);
            if ($merged) {
                $imgAdded = $this->safeAddImage($imgCell, $merged, [
                    'width' => 113, 'height' => 113, // 3cm × 3cm @ 96 DPI
                    'alignment' => Jc::CENTER, 'wrappingStyle' => 'inline',
                ]);
            }
        }
        if (!$imgAdded && $ttdPath) {
            $imgAdded = $this->safeAddImage($imgCell, $ttdPath, [
                'width' => 113, 'height' => 113, // 3cm × 3cm @ 96 DPI
                'alignment' => Jc::CENTER, 'wrappingStyle' => 'inline',
            ]);
        }
        if (!$imgAdded && $capPath) {
            $imgAdded = $this->safeAddImage($imgCell, $capPath, [
                'width' => 113, 'height' => 113, // 3cm × 3cm @ 96 DPI
                'alignment' => Jc::CENTER, 'wrappingStyle' => 'inline',
            ]);
        }
        if (!$imgAdded) {
            $imgCell->addText('');
        }
        
        // Nama
        $row = $table->addRow();
        $row->addCell(5812, $cellStyle)->addText('');
        $row->addCell(4513, $cellStyle)
            ->addText($nama, [
                'bold' => true, 'size' => $this->fontSize,
                'name' => $this->fontName, 'underline' => 'single',
            ], ['alignment' => Jc::CENTER]);

        // Jabatan
        $row = $table->addRow();
        $row->addCell(5812, $cellStyle)->addText('');
        $row->addCell(4513, $cellStyle)
            ->addText($jabatan,
                ['size' => $this->fontSize, 'name' => $this->fontName],
                ['alignment' => Jc::CENTER]);
    }
}