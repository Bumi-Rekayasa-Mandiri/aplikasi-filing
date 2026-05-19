<?php

namespace App\Services;

use App\Models\Surat;
use Carbon\Carbon;
use PhpOffice\PhpWord\SimpleType\Jc;

class GenerateBRM2Docx extends BaseDocxGenerator
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
            [
                'alignment'   => Jc::CENTER,
                'spaceBefore' => 0,        // ✅ pasangan: email text juga 0
                'spaceAfter'  => 300,
            ]
        );

        // ── JUDUL ─────────────────────────────────────
        $this->section->addTextRun([
            'alignment'   => Jc::CENTER,
            'spaceBefore' => 0,
            'spaceAfter'  => 300,
        ])->addText('SURAT PELEPASAN HAK', [
            'bold'      => true,
            'size'      => 14,
            'name'      => $this->fontName,
            'underline' => 'single',
        ]);

        // ── NOMOR & TANGGAL ───────────────────────────
        $this->addNomorTanggal($surat);

        // ── PEMBUKA ───────────────────────────────────
        $this->addParagraf('Yang bertanda tangan di bawah ini:');

        // ── IDENTITAS PENANDATANGAN (hardcoded) ───────
        $this->addIdentitasTable([
            ['Nama',                 'Ilman Sunaryo'],
            ['Jabatan',              'Direktur'],
            ['NIK',                  '3215030103870006'],
            ['Tempat/Tanggal Lahir', 'Karawang, 01 Maret 1987'],
            ['Alamat',               'Dusun Bobojong RT 005 RW 003, Purwadana, Telukjambe Timur, Karawang'],
        ], 2500);

        // ── KETERANGAN KENDARAAN ──────────────────────
        $this->addParagraf('Dengan ini menerangkan bahwa data kendaraan tersebut di bawah ini :');

        $this->addIdentitasTable([
            ['Merk/Jenis',   $surat->merk   ?? '—'],
            ['Warna/Tahun',  $surat->warna  ?? '—'],
            ['Rangka/Mesin', $surat->rangka ?? '—'],
        ], 2500);

        // ── ISI ───────────────────────────────────────
        $this->addParagraf('Dan telah melepaskan haknya atas kendaraan tersebut di atas.');
        $this->addParagraf('Demikian Surat Pelepasan Hak ini dibuat untuk dipergunakan sebagaimana mestinya.');

        // ── TANGGAL KANAN ─────────────────────────────
        $this->section->addText("Karawang, {$tgl}", [
            'size' => $this->fontSize,
            'name' => $this->fontName,
        ], ['alignment' => Jc::RIGHT, 'spaceBefore' => 400]);

        // ── TTD RATA KANAN ────────────────────────────
        $this->addTtdRataKanan($surat);

        return $this->save('BRM2', $surat->id);
    }

    private function addTtdRataKanan(Surat $surat): void
    {
        $ttd = $surat->ttds->first();

        $nama    = $ttd?->nama_penandatangan ?? 'Ilman Sunaryo';
        $jabatan = $ttd?->jabatan ?? 'Direktur';
        $label   = ($ttd?->label ?? 'Hormat Kami') . ',';

        $capMedia = $surat->getFirstMedia('cap');
        $capPath  = $capMedia
            ? str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $capMedia->getPath())
            : null;

        $ttdMedia = $ttd?->getFirstMedia('ttd');
        $ttdPath  = $ttdMedia
            ? str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $ttdMedia->getPath())
            : null;

        // ── Style reusable ─────────────────────────────
        $fontPlain = ['size' => $this->fontSize, 'name' => $this->fontName];
        $fontNama  = $fontPlain + ['bold' => true, 'underline' => 'single'];

        // No-gap paragraph untuk teks center
        $pNoGap = [
            'alignment'   => Jc::CENTER,
            'spaceBefore' => 0,
            'spaceAfter'  => 0,
        ];
        // No-gap paragraph untuk cell kiri kosong (left-aligned default)
        $pNoGapLeft = ['spaceBefore' => 0, 'spaceAfter' => 0];

        $cellStyle = ['borderSize' => 0, 'borderColor' => 'FFFFFF'];

        // Lebar 70/30
        $leftWidth  = 6318;
        $rightWidth = 2708;

        $table = $this->section->addTable(['borderSize' => 0, 'borderColor' => 'FFFFFF']);

        // Helper closure: row dengan cell kiri kosong + cell kanan diisi callback
        $addRow = function ($table, callable $fillRight)
            use ($cellStyle, $leftWidth, $rightWidth, $fontPlain, $pNoGapLeft)
        {
            $row = $table->addRow();
            $row->addCell($leftWidth, $cellStyle)->addText('', $fontPlain, $pNoGapLeft);
            $right = $row->addCell($rightWidth, $cellStyle);
            $fillRight($right);
        };

        // Baris label
        $addRow($table, function ($cell) use ($label, $fontPlain, $pNoGap) {
            $cell->addText($label, $fontPlain, $pNoGap);
        });

        // Baris gambar — image WAJIB dibungkus TextRun supaya paragraph style berlaku
        $addRow($table, function ($imgCell) use ($capPath, $ttdPath, $fontPlain, $pNoGap) {
            // CATATAN: tidak ada 'alignment' di image style.
            // Alignment image di-handle oleh paragraph wrapper (TextRun) → sumber tunggal.
            $imgStyle = [
                'width'         => 90,
                'height'        => 90,
                'wrappingStyle' => 'inline',
            ];

            $placed = false;

            if ($capPath && $ttdPath && file_exists($capPath) && file_exists($ttdPath)) {
                $merged = $this->mergeCapTtd($capPath, $ttdPath);
                if ($merged) {
                    $placed = $this->safeAddImageWithParagraph($imgCell, $merged, $imgStyle, $pNoGap);
                }
                if (!$placed) {
                    $placed = $this->safeAddImageWithParagraph($imgCell, $ttdPath, $imgStyle, $pNoGap);
                }
            } elseif ($ttdPath && file_exists($ttdPath)) {
                $placed = $this->safeAddImageWithParagraph($imgCell, $ttdPath, $imgStyle, $pNoGap);
            } elseif ($capPath && file_exists($capPath)) {
                $placed = $this->safeAddImageWithParagraph(
                    $imgCell,
                    $capPath,
                    ['width' => 100, 'height' => 100, 'wrappingStyle' => 'inline'],
                    $pNoGap
                );
            }

            if (!$placed) {
                $imgCell->addText('', $fontPlain, $pNoGap);
            }
        });

        // Baris nama
        $addRow($table, function ($cell) use ($nama, $fontNama, $pNoGap) {
            $cell->addText($nama, $fontNama, $pNoGap);
        });

        // Baris jabatan
        $addRow($table, function ($cell) use ($jabatan, $fontPlain, $pNoGap) {
            $cell->addText($jabatan, $fontPlain, $pNoGap);
        });
    }
}