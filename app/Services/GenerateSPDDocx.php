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
        $tgl = Carbon::parse($surat->tanggal_surat)
            ->locale('id')
            ->translatedFormat('d F Y');

        // ── KOP ───────────────────────────────────────
        $this->addKop();

        // ── Email & Phone (spaceBefore=0 untuk hilangkan gap dari kop) ──
        $this->section->addText(
            'e-mail : bumirekayasa.mandiri@gmail.com Phone : 0267-8639-837 / Fax: 0267-8639-837',
            ['size' => 10, 'name' => $this->fontName],
            ['alignment' => Jc::CENTER, 'spaceBefore' => 0, 'spaceAfter' => 200]
        );

        // ── JUDUL (after=0, Nomor langsung di bawah) ──
        $this->section->addTextRun([
            'alignment'   => Jc::CENTER,
            'spaceBefore' => 100,
            'spaceAfter'  => 0,
        ])->addText('SURAT PERMOHONAN', [
            'bold'      => true,
            'size'      => 14,
            'name'      => $this->fontName,
            'underline' => 'single',
        ]);

        // ── NOMOR (centered, di bawah judul) ──────────
        $this->section->addText(
            'Nomor : ' . ($surat->nomor_surat ?? '—'),
            ['size' => $this->fontSize, 'name' => $this->fontName],
            ['alignment' => Jc::CENTER, 'spaceBefore' => 0, 'spaceAfter' => 200]
        );

        // ── LAMPIRAN + HAL + TANGGAL (3 kolom × 2 baris) ──
        $this->addLampiranHalTanggal($surat, $tgl);

        // ── KEPADA YTH (4 baris super-tight) ──────────
        $font   = ['size' => $this->fontSize, 'name' => $this->fontName];
        $pTight = ['spaceBefore' => 180,  'spaceAfter' => 0];
        $pTightBefore60 = ['spaceBefore' => 60, 'spaceAfter' => 0];

        $this->section->addText('Kepada Yth.', $font, $pTight);
        $this->section->addText($surat->tujuan ?? '—', $font, $pTightBefore60);
        $this->section->addText($surat->alamat ?? '—', $font, $pTightBefore60);
        $this->section->addText(
            'Di Tempat',
            $font,
            ['spaceBefore' => 60, 'spaceAfter' => 240]
        );

        // ── Dengan hormat ─────────────────────────────
        $this->section->addText(
            'Dengan hormat,',
            $font,
            ['spaceBefore' => 0, 'spaceAfter' => 120]
        );

        // ── BODY (justified, after=240 untuk spasi sebelum bank table) ──
        $this->section->addText(
            "Berdasarkan Invoice tersebut di atas terkait pembelian " .
            ($surat->item_pembelian ?? '—') .
            ". Maka dengan ini Kami mohon pengembalian transfer pembelian material tersebut sebesar " .
            ($surat->nominal ?? '—') .
            " Kiranya dapat dibayarkan melalui rekening sbb :",
            $font,
            ['alignment' => Jc::BOTH, 'spaceBefore' => 0, 'spaceAfter' => 240]
        );

        // ── BANK REKENING (3 kolom, label BOLD) ───────
        $this->addBankRekening($surat);

        // ── PENUTUP (justified) ───────────────────────
        $this->section->addText(
            'Demikian yang dapat kami sampaikan, atas perhatian dan kerjasamanya disampaikan terima kasih.',
            $font,
            ['alignment' => Jc::BOTH, 'spaceBefore' => 180, 'spaceAfter' => 180]
        );

        // ── TTD rata kiri (tight stacking) ────────────
        $this->addTtdRataKiriSPD($surat);

        return $this->save('SPD', $surat->id);
    }

    /**
     * Tabel 3 kolom × 2 baris: (Lampiran/Hal) | (value) | (Karawang [tgl] di row 1 only).
     * Lebar 1411 / 4937 / 2961 (total ~9309, slightly overflows seperti referensi).
     */
    private function addLampiranHalTanggal(Surat $surat, string $tgl): void
    {
        $borderless = ['borderSize' => 0, 'borderColor' => 'FFFFFF'];
        $font   = ['size' => $this->fontSize, 'name' => $this->fontName];
        $pCell  = ['spaceBefore' => 0, 'spaceAfter' => 80];
        $pRight = ['spaceBefore' => 0, 'spaceAfter' => 80, 'alignment' => Jc::RIGHT];

        $wLabel = 1411;
        $wValue = 4937;
        $wDate  = 2961;

        $table = $this->section->addTable($borderless);

        // Baris 1: Lampiran + value + Karawang [tgl]
        $row = $table->addRow();
        $row->addCell($wLabel, $borderless)->addText('Lampiran', $font, $pCell);
        $row->addCell($wValue, $borderless)
            ->addText(': ' . ($surat->lampiran ?: '-'), $font, $pCell);
        $row->addCell($wDate, $borderless)
            ->addText("Karawang, {$tgl}", $font, $pRight);

        // Baris 2: Hal + value + (kosong)
        $row = $table->addRow();
        $row->addCell($wLabel, $borderless)->addText('Hal', $font, $pCell);
        $row->addCell($wValue, $borderless)
            ->addText(': ' . ($surat->perihal ?? '—'), $font, $pCell);
        $row->addCell($wDate, $borderless)->addText('', $font, $pCell);
    }

    /**
     * Tabel rekening bank: 3 kolom (2410 label / 200 colon / 6416 value).
     * Label BOLD, spaceAfter=120 per cell.
     */
    private function addBankRekening(Surat $surat): void
    {
        $borderless = ['borderSize' => 0, 'borderColor' => 'FFFFFF'];
        $font     = ['size' => $this->fontSize, 'name' => $this->fontName];
        $fontBold = $font + ['bold' => true];
        $pCell    = ['spaceBefore' => 0, 'spaceAfter' => 80];

        $rows = [
            ['Bank',           $surat->nama      ?? '—'],
            ['Cabang',         $surat->isi_surat ?? '—'],
            ['Nomor Rekening', $surat->no_ktp    ?? '—'],
            ['Atas Nama',      'PT. Bumi Rekayasa Mandiri'],
        ];

        $table = $this->section->addTable($borderless);
        foreach ($rows as [$label, $value]) {
            $row = $table->addRow();
            $row->addCell(2410, $borderless)->addText($label, $fontBold, $pCell);
            $row->addCell(200,  $borderless)->addText(':',    $font,     $pCell);
            $row->addCell(6416, $borderless)->addText($value, $font,     $pCell);
        }
    }

    /**
     * TTD rata kiri (bukan tabel — sequence paragraph + image).
     * Layout: Diajukan oleh → PT. Bumi... → Image → Nama (bold+underline) → Jabatan.
     * Semua tight (after=0).
     */
    private function addTtdRataKiriSPD(Surat $surat): void
    {
        $ttd     = $surat->ttds->first();
        $nama    = $ttd?->nama_penandatangan ?? 'Ilman Sunaryo';
        $jabatan = $ttd?->jabatan            ?? 'Direktur';

        $capMedia = $surat->getFirstMedia('cap');
        $capPath  = $capMedia
            ? str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $capMedia->getPath())
            : null;

        $ttdMedia = $ttd?->getFirstMedia('ttd');
        $ttdPath  = $ttdMedia
            ? str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $ttdMedia->getPath())
            : null;

        $font     = ['size' => $this->fontSize, 'name' => $this->fontName];
        $fontNama = $font + ['bold' => true, 'underline' => 'single'];

        $pTight     = ['spaceBefore' => 0, 'spaceAfter' => 0];
        // Gap dari "Demikian..." (yang after=0) ke "Diajukan oleh," — 240 twip ≈ 1 line
        $pTightWithBefore = ['spaceBefore' => 240, 'spaceAfter' => 0];
        $pImgLeft   = ['alignment' => Jc::LEFT, 'spaceBefore' => 0, 'spaceAfter' => 0];

        // Baris 1: "Diajukan oleh," (dengan gap dari paragraf sebelumnya)
        $this->section->addText('Diajukan oleh,', $font, $pTightWithBefore);

        // Baris 2: "PT. Bumi Rekayasa Mandiri,"
        $this->section->addText('PT. Bumi Rekayasa Mandiri,', $font, $pTight);

        // Baris 3: Image (left-aligned) — via safeAddImageWithParagraph
        $imgStyle = [
            'width'         => 80,
            'height'        => 80,
            'wrappingStyle' => 'inline',
        ];

        $placed = false;

        if ($capPath && $ttdPath && file_exists($capPath) && file_exists($ttdPath)) {
            $merged = $this->mergeCapTtd($capPath, $ttdPath);
            if ($merged) {
                $placed = $this->safeAddImageWithParagraph(
                    $this->section, $merged, $imgStyle, $pImgLeft
                );
            }
            if (!$placed) {
                $placed = $this->safeAddImageWithParagraph(
                    $this->section, $ttdPath, $imgStyle, $pImgLeft
                );
            }
        } elseif ($ttdPath && file_exists($ttdPath)) {
            $placed = $this->safeAddImageWithParagraph(
                $this->section, $ttdPath, $imgStyle, $pImgLeft
            );
        } elseif ($capPath && file_exists($capPath)) {
            $placed = $this->safeAddImageWithParagraph(
                $this->section, $capPath, $imgStyle, $pImgLeft
            );
        }

        if (!$placed) {
            // Fallback: 1 paragraf kosong dengan alignment left
            $this->section->addText('', $font, $pImgLeft);
        }

        // Baris 4: Nama (bold + underline, left-aligned)
        $this->section->addText(
            $nama,
            $fontNama,
            ['alignment' => Jc::LEFT, 'spaceBefore' => 0, 'spaceAfter' => 0]
        );

        // Baris 5: Jabatan (left-aligned)
        $this->section->addText(
            $jabatan,
            $font,
            ['alignment' => Jc::LEFT, 'spaceBefore' => 0, 'spaceAfter' => 0]
        );
    }
}