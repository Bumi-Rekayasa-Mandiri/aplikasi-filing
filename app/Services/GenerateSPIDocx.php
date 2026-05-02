<?php

namespace App\Services;

use App\Models\Surat;
use Carbon\Carbon;
use PhpOffice\PhpWord\SimpleType\Jc;

class GenerateSPIDocx extends BaseDocxGenerator
{
    public function handle(Surat $surat): string
    {
        $this->setup();
        $tgl = Carbon::parse($surat->tanggal_surat)->locale('id')->translatedFormat('d F Y');

        // ── KOP ───────────────────────────────────────
        $this->addKop();
        $this->addSpacing();

        // ── JUDUL ─────────────────────────────────────
        $this->section->addTextRun([
            'alignment'   => Jc::CENTER,
            'spaceBefore' => 150,
            'spaceAfter'  => 0,
        ])->addText('SURAT PERJANJIAN INVESTASI', [
            'bold'      => true,
            'size'      => 14,
            'name'      => $this->fontName,
            'underline' => 'single',
        ]);

        $this->addSpacing();

        // ── NOMOR & TANGGAL ───────────────────────────
        // Dari DOCX asli: 2 kolom (4513 | 4513)
        $this->addNomorTanggal($surat);

        // ── BISMILLAH ─────────────────────────────────
        $this->section->addText('Bismillahirrohmanirrohim.', [
            'size' => $this->fontSize,
            'name' => $this->fontName,
        ], ['spaceBefore' => 80, 'spaceAfter' => 80]);

        // ── PEMBUKA ───────────────────────────────────
        $this->section->addText('Yang bertanda tangan di bawah ini:', [
            'size' => $this->fontSize,
            'name' => $this->fontName,
        ], ['spaceAfter' => 80]);

        // ── IDENTITAS PIHAK PERTAMA (Investor) ────────
        // Dari DOCX asli: col widths 1000 | 200 | 7826
        $this->addIdentitasTable([
            ['Nama',   $surat->nama    ?? '—'],
            ['Alamat', $surat->alamat  ?? '—'],
            ['No KTP', $surat->no_ktp  ?? '—'],
        ], 1000);

        $this->addSpacing();

        // "Yang Menjadi Pihak Pertama" — italic sebagian
        $p = $this->section->addTextRun(['spaceAfter' => 80]);
        $p->addText('Yang Menjadi ', [
            'size' => $this->fontSize, 'name' => $this->fontName,
        ]);
        $p->addText('Pihak Pertama', [
            'size' => $this->fontSize, 'name' => $this->fontName, 'italic' => true,
        ]);

        // ── IDENTITAS PIHAK KEDUA (PT BRM) ────────────
        // Dari DOCX asli: col widths 1000 | 200 | 7826
        $this->addIdentitasTable([
            ['Nama',    'Ilman Sunaryo'],
            ['Alamat',  'Ruko Dharmawangsa 1 Blok D8/DC Grand Taruma Karawang'],
            ['Jabatan', 'Direktur PT. Bumi Rekayasa Mandiri'],
        ], 1000);

        $this->addSpacing();

        // "Yang Menjadi Pihak Kedua" — italic sebagian
        $p = $this->section->addTextRun(['spaceAfter' => 80]);
        $p->addText('Yang Menjadi ', [
            'size' => $this->fontSize, 'name' => $this->fontName,
        ]);
        $p->addText('Pihak Kedua', [
            'size' => $this->fontSize, 'name' => $this->fontName, 'italic' => true,
        ]);

        $this->addSpacing();

        // ── ISI PERJANJIAN ────────────────────────────
        $this->section->addText('ISI PERJANJIAN', [
            'bold' => true, 'size' => $this->fontSize, 'name' => $this->fontName,
        ], ['spaceAfter' => 80]);

        $bullets = [
            'Perjanjian ini bersifat Mudhorobah, Pihak Pertama Sebagai Shohibul Maal atau Investor, dan Pihak Kedua Sebagai Mudhorib atau Penerima',
            'Pihak Kedua diberikan dana investasi oleh Pihak Pertama',
            'Nominal Investasi berdasarkan kesepakatan yaitu sebesar ' . ($surat->nominal ?? '—'),
            'Pihak Kedua akan membayar kepada Pihak Pertama selambat lambatnya tanggal 01 November 2025',
            'Pihak Pertama Insya Allah akan mendapatkan hak bagi hasil sebesar ' . ($surat->nominal_bagihasil ?? '—'),
            'Apabila timbul perselisihan antara kedua belah pihak maka diselesaikan dengan cara kekeluargaan.',
        ];

        foreach ($bullets as $bullet) {
            $this->section->addText('• ' . $bullet, [
                'size' => $this->fontSize,
                'name' => $this->fontName,
            ], [
                'alignment'   => Jc::BOTH,
                'spaceBefore' => 40,
                'spaceAfter'  => 40,
                'indentation' => ['left' => 360, 'hanging' => 200],
            ]);
        }

        $this->addSpacing();

        // ── TANGGAL KANAN ─────────────────────────────
        $this->section->addText("Karawang, {$tgl}", [
            'size' => $this->fontSize,
            'name' => $this->fontName,
        ], ['alignment' => Jc::RIGHT, 'spaceBefore' => 100]);

        $this->addSpacing();

        // ── TTD 3 KOLOM (sesuai DOCX asli) ───────────
        $this->addTtdTigaKolom($surat);

        return $this->save('SPI', $surat->id);
    }

    /**
     * TTD 3 kolom sesuai DOCX asli:
     *   Col 0 (Pihak Pertama / Investor) : w=2727
     *   Col 1 (spacer kosong)            : w=2355
     *   Col 2 (Pihak Kedua  / PT BRM)   : w=3944
     *   Total: 9026 (content width A4 margin 1")
     *
     * Hanya 3 baris: Label | Gambar | Nama (tanpa baris Jabatan)
     */
    private function addTtdTigaKolom(Surat $surat): void
    {
        $ttds     = $surat->ttds;
        $ttdKiri  = $ttds->get(0); // Pihak Pertama (Investor)
        $ttdKanan = $ttds->get(1); // Pihak Kedua (PT BRM)

        $capMedia = $surat->getFirstMedia('cap');
        $capPath  = $capMedia
            ? str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $capMedia->getPath()) : null;

        $ttdKiriMedia  = $ttdKiri?->getFirstMedia('ttd');
        $ttdKiriPath   = $ttdKiriMedia
            ? str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $ttdKiriMedia->getPath()) : null;

        $ttdKananMedia = $ttdKanan?->getFirstMedia('ttd');
        $ttdKananPath  = $ttdKananMedia
            ? str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $ttdKananMedia->getPath()) : null;

        // Lebar kolom dari DOCX asli (proporsional → 9026 total)
        // Original DOCX: 3119 | 2693 | 4513 → scaled to 9026
        $wKiri   = 2727;
        $wSpacer = 2355;
        $wKanan  = 3944;

        $style = ['borderSize' => 0, 'borderColor' => 'FFFFFF'];
        $cs    = ['borderSize' => 0, 'borderColor' => 'FFFFFF']; // cell style
        $table = $this->section->addTable($style);

        // ── Baris 1: Label ─────────────────────────────
        $row = $table->addRow();
        $row->addCell($wKiri, $cs)
            ->addText($ttdKiri?->label ?? 'Pihak Pertama', [
                'size' => $this->fontSize, 'name' => $this->fontName,
            ], ['alignment' => Jc::CENTER]);
        $row->addCell($wSpacer, $cs)->addText(''); // spacer
        $row->addCell($wKanan, $cs)
            ->addText($ttdKanan?->label ?? 'Pihak Kedua', [
                'size' => $this->fontSize, 'name' => $this->fontName,
            ], ['alignment' => Jc::CENTER]);

        // ── Baris 2: Gambar ────────────────────────────
        $row = $table->addRow();

        // Kiri — Investor: TTD saja (tanpa cap perusahaan)
        $cellKiri = $row->addCell($wKiri, $cs);
        if ($ttdKiriPath && file_exists($ttdKiriPath)) {
            $this->safeAddImage($cellKiri, $ttdKiriPath, [
                'width'         => 90,
                'height'        => 90,
                'alignment'     => Jc::CENTER,
                'wrappingStyle' => 'inline',
            ]);
        } else {
            // Spasi kosong setinggi gambar
            for ($i = 0; $i < 4; $i++) {
                $cellKiri->addText('');
            }
        }

        $row->addCell($wSpacer, $cs)->addText(''); // spacer

        // Kanan — PT BRM: cap + TTD (merged)
        $cellKanan = $row->addCell($wKanan, $cs);
        if ($capPath && $ttdKananPath && file_exists($capPath) && file_exists($ttdKananPath)) {
            $merged = $this->mergeCapTtd($capPath, $ttdKananPath);
            if ($merged) {
                $this->safeAddImage($cellKanan, $merged, [
                    'width'         => 113, 'height' => 113, // 3cm × 3cm
                    'alignment'     => Jc::CENTER,
                    'wrappingStyle' => 'inline',
                ]);
            } else {
                $this->safeAddImage($cellKanan, $ttdKananPath, [
                    'width' => 90, 'height' => 90,
                    'alignment' => Jc::CENTER, 'wrappingStyle' => 'inline',
                ]);
            }
        } elseif ($ttdKananPath && file_exists($ttdKananPath)) {
            $this->safeAddImage($cellKanan, $ttdKananPath, [
                'width'         => 90,
                'height'        => 90,
                'alignment'     => Jc::CENTER,
                'wrappingStyle' => 'inline',
            ]);
        } elseif ($capPath && file_exists($capPath)) {
            $this->safeAddImage($cellKanan, $capPath, [
                'width'         => 100,
                'height'        => 100,
                'alignment'     => Jc::CENTER,
                'wrappingStyle' => 'inline',
            ]);
        } else {
            for ($i = 0; $i < 4; $i++) {
                $cellKanan->addText('');
            }
        }

        // ── Baris 3: Nama (bold) — tanpa baris Jabatan sesuai DOCX ──
        $row = $table->addRow();
        $row->addCell($wKiri, $cs)
            ->addText($ttdKiri?->nama_penandatangan ?? '—', [
                'bold'      => true,
                'size'      => $this->fontSize,
                'name'      => $this->fontName,
                'underline' => 'single',
            ], ['alignment' => Jc::CENTER]);
        $row->addCell($wSpacer, $cs)->addText(''); // spacer
        $row->addCell($wKanan, $cs)
            ->addText($ttdKanan?->nama_penandatangan ?? '—', [
                'bold'      => true,
                'size'      => $this->fontSize,
                'name'      => $this->fontName,
                'underline' => 'single',
            ], ['alignment' => Jc::CENTER]);
    }
}