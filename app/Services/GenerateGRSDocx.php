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
        $tgl = Carbon::parse($surat->tanggal_surat)
            ->locale('id')
            ->translatedFormat('d F Y');

        // ── KOP ───────────────────────────────────────
        $this->addKop();
        $this->section->addText(
            'e-mail : bumirekayasa.mandiri@gmail.com Phone : 0267-8639-837 / Fax: 0267-8639-837',
            ['size' => 10, 'name' => $this->fontName],
            ['alignment' => Jc::CENTER, 'spaceBefore' => 0, 'spaceAfter' => 200]
        );

        // ── JUDUL ─────────────────────────────────────
        $this->section->addTextRun([
            'alignment'   => Jc::CENTER,
            'spaceBefore' => 100,
            'spaceAfter'  => 200,
        ])->addText('SURAT PENGAJUAN GARANSI', [
            'bold'      => true,
            'size'      => 14,
            'name'      => $this->fontName,
            'underline' => 'single',
        ]);

        // ── NOMOR + HAL + TANGGAL ─────────────────────
        // 2 baris × 4 kolom (label | colon | value | tanggal)
        // Match Blade <table class="nomor-table">: tanggal hanya di baris 1, rata kanan.
        $this->addNomorPerihalTanggal($surat, $tgl);

        // ── PEMBUKA ───────────────────────────────────
        $this->addParagraf('Saya yang bertanda tangan di bawah ini:');

        // ── IDENTITAS PENANDATANGAN (urutan = PDF) ────
        // Nama → Perusahaan → Jabatan → Alamat
        // "Direktur Utama" hardcoded di identitas (sesuai Blade);
        // jabatan di TTD ambil dari relasi $surat->ttds.
        $this->addIdentitasTable([
            ['Nama',       'Ilman Sunaryo'],
            ['Perusahaan', 'PT. Bumi Rekayasa Mandiri'],
            ['Jabatan',    'Direktur Utama'],
            ['Alamat',     'Ruko Dharmawangsa 1 Blok D8/DC Grand Taruma Telukjambe Karawang'],
        ], 2400);

        $this->addParagraf(
            'Dengan ini mengajukan garansi material kepada PT. BlueScope Steel Indonesia berupa :'
        );

        // ── DETAIL GARANSI (urutan = PDF) ─────────────
        // Material → Project → Alamat → Masa Garansi
        $this->addIdentitasTable([
            ['Material',     $surat->material     ?? '—'],
            ['Project',      $surat->project      ?? '—'],
            ['Alamat',       $surat->alamat       ?? '—'],
            ['Masa Garansi', $surat->masa_garansi ?? '—'],
        ], 2400);

        $this->addParagraf(
            'Demikian Surat Pengajuan ini kami buat, atas perhatian dan kerja samanya kami ucapkan terima kasih.'
        );

        // ── TANGGAL BAWAH (terpisah dari tabel TTD) ───
        // Match Blade: <div style="text-align: right; margin-top: 25px;">
        $this->section->addText(
            "Karawang, {$tgl}",
            ['size' => $this->fontSize, 'name' => $this->fontName],
            ['alignment' => Jc::RIGHT, 'spaceBefore' => 400, 'spaceAfter' => 80]
        );

        // ── TTD RATA KANAN ────────────────────────────
        $this->addTtdRataKananGRS($surat);

        return $this->save('GRS', $surat->id);
    }

    /**
     * Render blok 2-baris: Nomor + Karawang[tgl] di baris 1, Hal + perihal di baris 2.
     * Lokal untuk GRS karena tidak semua jenis surat punya field "Hal".
     */
    private function addNomorPerihalTanggal(Surat $surat, string $tgl): void
    {
        $borderless = ['borderSize' => 0, 'borderColor' => 'FFFFFF'];

        // Lebar kolom (twip). Total ≈ 9026 = lebar konten A4 (margin 1440 × 2).
        $wLabel = 1200;   // "Nomor" / "Hal"
        $wColon = 200;
        $wValue = 4400;
        $wDate  = 3226;

        $font   = ['size' => $this->fontSize, 'name' => $this->fontName];
        $pLeft  = ['spaceBefore' => 0, 'spaceAfter' => 0];
        $pRight = ['spaceBefore' => 0, 'spaceAfter' => 0, 'alignment' => Jc::RIGHT];

        $table = $this->section->addTable($borderless);

        // Baris 1: Nomor + Karawang [tgl]
        $row = $table->addRow();
        $row->addCell($wLabel, $borderless)->addText('Nomor', $font, $pLeft);
        $row->addCell($wColon, $borderless)->addText(':',     $font, $pLeft);
        $row->addCell($wValue, $borderless)->addText($surat->nomor_surat ?? '—', $font, $pLeft);
        $row->addCell($wDate,  $borderless)->addText("Karawang, {$tgl}",         $font, $pRight);

        // Baris 2: Hal (perihal)
        $row = $table->addRow();
        $row->addCell($wLabel, $borderless)->addText('Hal', $font, $pLeft);
        $row->addCell($wColon, $borderless)->addText(':',   $font, $pLeft);
        $row->addCell($wValue, $borderless)->addText($surat->perihal ?? '—', $font, $pLeft);
        $row->addCell($wDate,  $borderless)->addText('', $font, $pLeft);

        $this->addSpacing();
    }

    private function addTtdRataKananGRS(Surat $surat): void
    {
        $ttd     = $surat->ttds->first();
        $nama    = $ttd?->nama_penandatangan ?? 'Ilman Sunaryo';
        $jabatan = $ttd?->jabatan            ?? 'Direktur';
        $label   = $ttd?->label              ?? 'Yang Membuat Pernyataan';

        $capMedia = $surat->getFirstMedia('cap');
        $capPath  = $capMedia
            ? str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $capMedia->getPath())
            : null;

        $ttdMedia = $ttd?->getFirstMedia('ttd');
        $ttdPath  = $ttdMedia
            ? str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $ttdMedia->getPath())
            : null;

        // ── Styles ─────────────────────────────────────
        $fontPlain = ['size' => $this->fontSize, 'name' => $this->fontName];
        $fontNama  = $fontPlain + ['bold' => true, 'underline' => 'single'];

        $pNoGap     = ['alignment' => Jc::CENTER, 'spaceBefore' => 0, 'spaceAfter' => 0];
        $pNoGapLeft = ['spaceBefore' => 0, 'spaceAfter' => 0];

        $cellStyle  = ['borderSize' => 0, 'borderColor' => 'FFFFFF'];

        // 50/50 — match Blade: <table style="width: 50%; margin-left: auto;">
        $leftWidth  = 6318;
        $rightWidth = 2708;

        $table = $this->section->addTable(['borderSize' => 0, 'borderColor' => 'FFFFFF']);

        $addRow = function ($table, callable $fillRight)
            use ($cellStyle, $leftWidth, $rightWidth, $fontPlain, $pNoGapLeft)
        {
            $row = $table->addRow();
            $row->addCell($leftWidth, $cellStyle)->addText('', $fontPlain, $pNoGapLeft);
            $right = $row->addCell($rightWidth, $cellStyle);
            $fillRight($right);
        };

        // Label "Yang Membuat Pernyataan"
        $addRow($table, function ($cell) use ($label, $fontPlain, $pNoGap) {
            $cell->addText($label, $fontPlain, $pNoGap);
        });

        // Image (merged cap + ttd) — bungkus TextRun via safeAddImageWithParagraph
        $addRow($table, function ($imgCell) use ($capPath, $ttdPath, $fontPlain, $pNoGap) {
            $imgStyle = [
                'width'         => 90,
                'height'        => 90,
                'wrappingStyle' => 'inline',
            ];

            $placed = false;

            if ($capPath && $ttdPath && file_exists($capPath) && file_exists($ttdPath)) {
                $merged = $this->mergeCapTtd($capPath, $ttdPath);
                if ($merged) {
                    $placed = $this->safeAddImageWithParagraph(
                        $imgCell, $merged, $imgStyle, $pNoGap
                    );
                }
                if (!$placed) {
                    $placed = $this->safeAddImageWithParagraph(
                        $imgCell, $ttdPath, $imgStyle, $pNoGap
                    );
                }
            } elseif ($ttdPath && file_exists($ttdPath)) {
                $placed = $this->safeAddImageWithParagraph(
                    $imgCell, $ttdPath, $imgStyle, $pNoGap
                );
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

        // Nama (bold + underline)
        $addRow($table, function ($cell) use ($nama, $fontNama, $pNoGap) {
            $cell->addText($nama, $fontNama, $pNoGap);
        });

        // Jabatan
        $addRow($table, function ($cell) use ($jabatan, $fontPlain, $pNoGap) {
            $cell->addText($jabatan, $fontPlain, $pNoGap);
        });
    }
}