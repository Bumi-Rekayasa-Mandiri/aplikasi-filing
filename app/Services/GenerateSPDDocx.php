<?php

namespace App\Services;

use App\Models\Surat;
use Carbon\Carbon;
use PhpOffice\PhpWord\SimpleType\Jc;

class GenerateSPDDocx extends BaseDocxGenerator
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
            'alignment' => Jc::CENTER, 'spaceBefore' => 100, 'spaceAfter' => 0,
        ])->addText('SURAT PERMOHONAN', [
            'bold' => true, 'size' => 14,
            'name' => $this->fontName, 'underline' => 'single',
        ]);

        // ── Nomor center di bawah judul ──────────────
        $this->section->addText(
            'Nomor : ' . ($surat->nomor_surat ?? '—'),
            ['size' => $this->fontSize, 'name' => $this->fontName],
            ['alignment' => Jc::CENTER, 'spaceAfter' => 200]
        );

        // ── Lampiran + Tanggal (kanan) ───────────────
        $cellStyle = ['borderSize' => 0, 'borderColor' => 'FFFFFF'];
        $tableStyle = ['borderSize' => 0, 'borderColor' => 'FFFFFF'];

        $table = $this->section->addTable($tableStyle);
        $row = $table->addRow();
        $row->addCell(1418, $cellStyle)
            ->addText('Lampiran', ['size' => $this->fontSize, 'name' => $this->fontName]);
        $row->addCell(4961, $cellStyle)
            ->addText(': ' . ($surat->lampiran ?: '-'),
                ['size' => $this->fontSize, 'name' => $this->fontName]);
        $row->addCell(2976, $cellStyle)
            ->addText("Karawang, {$tgl}",
                ['size' => $this->fontSize, 'name' => $this->fontName],
                ['alignment' => Jc::RIGHT]);

        // ── Hal ───────────────────────────────────────
        $row = $table->addRow();
        $row->addCell(1418, $cellStyle)
            ->addText('Hal', ['size' => $this->fontSize, 'name' => $this->fontName]);
        $row->addCell(4961, $cellStyle)
            ->addText(': ' . ($surat->perihal ?? '—'),
                ['size' => $this->fontSize, 'name' => $this->fontName]);
        $row->addCell(2976, $cellStyle)->addText('');

        $this->addSpacing();

        // ── Kepada Yth (multi-line) ───────────────────
        $this->section->addText('Kepada Yth.',
            ['size' => $this->fontSize, 'name' => $this->fontName],
            ['spaceAfter' => 0]);
        $this->section->addText($surat->tujuan ?? '—',
            ['size' => $this->fontSize, 'name' => $this->fontName],
            ['spaceAfter' => 0]);
        $this->section->addText($surat->alamat ?? '—',
            ['size' => $this->fontSize, 'name' => $this->fontName],
            ['spaceAfter' => 0]);
        $this->section->addText('Di Tempat',
            ['size' => $this->fontSize, 'name' => $this->fontName],
            ['spaceAfter' => 200]);

        // ── Dengan hormat ─────────────────────────────
        $this->section->addText('Dengan hormat,',
            ['size' => $this->fontSize, 'name' => $this->fontName],
            ['spaceAfter' => 80]);

        $this->addParagraf(
            "Berdasarkan Invoice tersebut di atas terkait pembelian {$surat->item_pembelian}. " .
            "Maka dengan ini Kami mohon pengembalian transfer pembelian material tersebut sebesar " .
            "{$surat->nominal} Kiranya dapat dibayarkan melalui rekening sbb :"
        );

        $this->addSpacing();

        // ── Detail rekening — label bold ─────────────
        $this->addIdentitasTable([
            ['Bank',           $surat->nama      ?? '—'],
            ['Cabang',         $surat->isi_surat ?? '—'],
            ['Nomor Rekening', $surat->no_ktp    ?? '—'],
            ['Atas Nama',      'PT. Bumi Rekayasa Mandiri'],
        ], 2410);

        $this->addSpacing();

        $this->addParagraf(
            'Demikian yang dapat kami sampaikan, atas perhatian dan kerjasamanya disampaikan terima kasih.'
        );

        $this->addSpacing();

        // ── TTD rata kiri ─────────────────────────────
        $this->addTtdRataKiriSPD($surat);

        return $this->save('SPD', $surat->id);
    }

    private function addTtdRataKiriSPD(Surat $surat): void
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

        // Diajukan oleh,
        $this->section->addText('Diajukan oleh,', [
            'size' => $this->fontSize, 'name' => $this->fontName,
        ], ['spaceAfter' => 0]);

        // PT. Bumi Rekayasa Mandiri,
        $this->section->addText('PT. Bumi Rekayasa Mandiri,', [
            'size' => $this->fontSize, 'name' => $this->fontName,
        ], ['spaceAfter' => 0]);

        // Gambar rata kiri
        $imgAdded = false;
        if ($capPath && $ttdPath && file_exists($capPath) && file_exists($ttdPath)) {
            $merged = $this->mergeCapTtd($capPath, $ttdPath);
            if ($merged) {
                $imgAdded = $this->safeAddImage($this->section, $merged, [
                    'width' => 110, 'height' => 110,
                    'alignment' => Jc::LEFT, 'wrappingStyle' => 'inline',
                ]);
            }
        }
        if (!$imgAdded && $ttdPath) {
            $imgAdded = $this->safeAddImage($this->section, $ttdPath, [
                'width' => 80, 'height' => 80,
                'alignment' => Jc::LEFT, 'wrappingStyle' => 'inline',
            ]);
        }
        if (!$imgAdded && $capPath) {
            $imgAdded = $this->safeAddImage($this->section, $capPath, [
                'width' => 100, 'height' => 100,
                'alignment' => Jc::LEFT, 'wrappingStyle' => 'inline',
            ]);
        }
        if (!$imgAdded) {
            $this->addSpacing(4);
        }

        // Nama
        $this->section->addText($nama, [
            'bold' => true, 'size' => $this->fontSize,
            'name' => $this->fontName, 'underline' => 'single',
        ], ['alignment' => Jc::LEFT, 'spaceAfter' => 0]);

        // Jabatan
        $this->section->addText($jabatan, [
            'size' => $this->fontSize, 'name' => $this->fontName,
        ], ['alignment' => Jc::LEFT]);
    }
}