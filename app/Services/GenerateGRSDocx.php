<?php

namespace App\Services;

use App\Models\Surat;
use Carbon\Carbon;
use PhpOffice\PhpWord\SimpleType\Jc;

class GenerateGRSDocx extends BaseDocxGenerator
{
    public function handle(Surat $surat): string
    {
        $this->setup();
        $tgl = Carbon::parse($surat->tanggal_surat)->locale('id')->translatedFormat('d F Y');

        $this->addKop();
        $this->section->addText(
            'e-mail : bumirekayasa.mandiri@gmail.com Phone : 0267-8639-837 / Fax: 0267-8639-837',
            ['size' => 10, 'name' => $this->fontName],
            ['alignment' => Jc::CENTER, 'spaceAfter' => 200]
        );

        // ── JUDUL ─────────────────────────────────────
        $this->section->addTextRun([
            'alignment' => Jc::CENTER, 'spaceBefore' => 100, 'spaceAfter' => 200,
        ])->addText('SURAT PENGAJUAN GARANSI', [
            'bold' => true, 'size' => 14,
            'name' => $this->fontName, 'underline' => 'single',
        ]);

        $this->addNomorTanggal($surat);

        $this->addParagraf('Yang bertanda tangan di bawah ini:');

        // ── Identitas penandatangan ───────────────────
        $this->addIdentitasTable([
            ['Nama',       'Ilman Sunaryo'],
            ['Jabatan',    'Direktur'],
            ['Perusahaan', 'PT. Bumi Rekayasa Mandiri'],
        ], 2400);

        $this->addSpacing();
        $this->addParagraf('Dengan ini mengajukan garansi material untuk:');

        // ── Detail Garansi ────────────────────────────
        $this->addIdentitasTable([
            ['Project',      $surat->project      ?? '—'],
            ['Material',     $surat->material     ?? '—'],
            ['Masa Garansi', $surat->masa_garansi ?? '—'],
            ['Alamat',       $surat->alamat       ?? '—'],
        ], 2400);

        $this->addSpacing();

        $this->addParagraf(
            'Demikian surat pengajuan garansi material ini kami buat untuk dapat dipergunakan ' .
            'sebagaimana mestinya.'
        );

        $this->addSpacing();

        // ── TTD rata kanan ────────────────────────────
        $this->addTtdRataKananGRS($surat, $tgl);

        return $this->save('GRS', $surat->id);
    }

    private function addTtdRataKananGRS(Surat $surat, string $tgl): void
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
        $row->addCell(5670, $cellStyle)->addText('');
        $row->addCell(4513, $cellStyle)
            ->addText("Karawang, {$tgl}",
                ['size' => $this->fontSize, 'name' => $this->fontName],
                ['alignment' => Jc::CENTER]);

        // "Yang Membuat Pernyataan"
        $row = $table->addRow();
        $row->addCell(5670, $cellStyle)->addText('');
        $row->addCell(4513, $cellStyle)
            ->addText('Yang Membuat Pernyataan',
                ['size' => $this->fontSize, 'name' => $this->fontName],
                ['alignment' => Jc::CENTER]);

        // Gambar
        $row = $table->addRow();
        $row->addCell(5670, $cellStyle)->addText('');
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
        $row->addCell(5670, $cellStyle)->addText('');
        $row->addCell(4513, $cellStyle)
            ->addText($nama, [
                'bold' => true, 'size' => $this->fontSize,
                'name' => $this->fontName, 'underline' => 'single',
            ], ['alignment' => Jc::CENTER]);

        // Jabatan
        $row = $table->addRow();
        $row->addCell(5670, $cellStyle)->addText('');
        $row->addCell(4513, $cellStyle)
            ->addText($jabatan,
                ['size' => $this->fontSize, 'name' => $this->fontName],
                ['alignment' => Jc::CENTER]);
    }
}