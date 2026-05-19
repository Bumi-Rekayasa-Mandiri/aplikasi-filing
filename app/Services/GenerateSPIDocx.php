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
        $tgl = Carbon::parse($surat->tanggal_surat)
            ->locale('id')
            ->translatedFormat('d F Y');

        // ── KOP ───────────────────────────────────────
        $this->addKop();

        // ── Email & Phone (spaceBefore=0 untuk hilangkan gap dari kop) ──
        $this->section->addText(
            'e-mail : bumirekayasa.mandiri@gmail.com Phone : 0267-8639-837 / Fax: 0267-8639-837',
            ['size' => 10, 'name' => $this->fontName],
            ['alignment' => Jc::CENTER, 'spaceBefore' => 0, 'spaceAfter' => 240]
        );

        // ── JUDUL ─────────────────────────────────────
        $this->section->addTextRun([
            'alignment'   => Jc::CENTER,
            'spaceBefore' => 240,
            'spaceAfter'  => 360,
        ])->addText('SURAT PERJANJIAN INVESTASI', [
            'bold'      => true,
            'size'      => 14,
            'name'      => $this->fontName,
            'underline' => 'single',
        ]);

        // ── NOMOR + TANGGAL ───────────────────────────
        // 2 kolom 4513/4513, after=0 per cell
        $this->addNomorTanggalSPILocal($surat, $tgl);

        // ── BISMILLAH ─────────────────────────────────
        $font = ['size' => $this->fontSize, 'name' => $this->fontName];
        $this->section->addText(
            'Bismillahirrohmanirrohim.',
            $font,
            ['spaceBefore' => 80, 'spaceAfter' => 80]
        );

        // ── PEMBUKA ───────────────────────────────────
        $this->section->addText(
            'Yang bertanda tangan di bawah ini:',
            $font,
            ['spaceBefore' => 0, 'spaceAfter' => 120]
        );

        // ── IDENTITAS PIHAK PERTAMA (Investor) — BOLD labels ──
        $this->addIdentitasSPI([
            ['Nama',   $surat->nama   ?? '—'],
            ['Alamat', $surat->alamat ?? '—'],
            ['No KTP', $surat->no_ktp ?? '—'],
        ]);

        // ── "Yang Menjadi Pihak Pertama" (italic sebagian) ──
        $p = $this->section->addTextRun(['spaceBefore' => 0, 'spaceAfter' => 0]);
        $p->addText('Yang Menjadi ', $font);
        $p->addText('Pihak Pertama', $font + ['italic' => true]);

        // Separator antar identitas
        $this->section->addText('', $font, ['spaceBefore' => 0, 'spaceAfter' => 0]);

        // ── IDENTITAS PIHAK KEDUA (PT BRM) — BOLD labels ──
        $this->addIdentitasSPI([
            ['Nama',    'Ilman Sunaryo'],
            ['Alamat',  'Ruko Dharmawangsa 1 Blok D8/DC Grand Taruma Karawang'],
            ['Jabatan', 'Direktur PT. Bumi Rekayasa Mandiri'],
        ]);

        // ── "Yang Menjadi Pihak Kedua" (italic sebagian) ──
        $p = $this->section->addTextRun(['spaceBefore' => 0, 'spaceAfter' => 0]);
        $p->addText('Yang Menjadi ', $font);
        $p->addText('Pihak Kedua', $font + ['italic' => true]);

        // Empty separator
        $this->section->addText('', $font, ['spaceBefore' => 0, 'spaceAfter' => 0]);

        // ── ISI PERJANJIAN (bold) ─────────────────────
        $this->section->addText(
            'ISI PERJANJIAN',
            $font + ['bold' => true],
            ['spaceBefore' => 0, 'spaceAfter' => 80]
        );

        // ── BULLETS ────────────────────────────────────
        $bullets = [
            'Perjanjian ini bersifat Mudhorobah, Pihak Pertama Sebagai Shohibul Maal atau Investor, dan Pihak Kedua Sebagai Mudhorib atau Penerima',
            'Pihak Kedua diberikan dana investasi oleh Pihak Pertama',
            'Nominal Investasi berdasarkan kesepakatan yaitu sebesar ' . ($surat->nominal ?? '—'),
            'Pihak Kedua akan membayar kepada Pihak Pertama selambat lambatnya tanggal 01 November 2025',
            'Pihak Pertama Insya Allah akan mendapatkan hak bagi hasil sebesar ' . ($surat->nominal_bagihasil ?? '—'),
            'Apabila timbul perselisihan antara kedua belah pihak maka diselesaikan dengan cara kekeluargaan.',
        ];
 
        // ── BULLETS via tabel 2-kolom (konversi pola Blade) ──
        $this->addBulletList($bullets);

        // ── TANGGAL KANAN (sebelum TTD) ───────────────
        $this->section->addText(
            "Karawang, {$tgl}",
            $font,
            ['alignment' => Jc::RIGHT, 'spaceBefore' => 100, 'spaceAfter' => 200]
        );

        // ── TTD 3 KOLOM ────────────────────────────────
        $this->addTtdTigaKolom($surat);

        return $this->save('SPI', $surat->id);
    }

    /**
     * Nomor + Karawang dalam 1 baris × 2 kolom (4513/4513), TIGHT.
     */
    private function addNomorTanggalSPILocal(Surat $surat, string $tgl): void
    {
        $borderless = ['borderSize' => 0, 'borderColor' => 'FFFFFF'];
        $font   = ['size' => $this->fontSize, 'name' => $this->fontName];
        $pLeft  = ['spaceBefore' => 0, 'spaceAfter' => 0];
        $pRight = ['spaceBefore' => 0, 'spaceAfter' => 0, 'alignment' => Jc::RIGHT];

        $table = $this->section->addTable($borderless);
        $row   = $table->addRow();

        $row->addCell(4513, $borderless)
            ->addText('Nomor : ' . ($surat->nomor_surat ?? '—'), $font, $pLeft);
        $row->addCell(4513, $borderless)
            ->addText("Karawang, {$tgl}", $font, $pRight);
    }

    /**
     * Tabel identitas SPI: 3 kolom (1000 / 200 / 7826), label BOLD.
     * Semua cell after=0 KECUALI baris terakhir value (after=80).
     */
    private function addIdentitasSPI(array $rows): void
    {
        $borderless = ['borderSize' => 0, 'borderColor' => 'FFFFFF'];
        $font     = ['size' => $this->fontSize, 'name' => $this->fontName];
        $fontBold = $font + ['bold' => true];
        $pTight   = ['spaceBefore' => 0, 'spaceAfter' => 0];
        $pLast    = ['spaceBefore' => 0, 'spaceAfter' => 80];

        $table = $this->section->addTable($borderless);
        $lastIdx = count($rows) - 1;

        foreach ($rows as $i => [$label, $value]) {
            $row = $table->addRow();
            // Hanya value di baris terakhir yang punya spaceAfter=80
            $pValue = ($i === $lastIdx) ? $pLast : $pTight;

            $row->addCell(1000, $borderless)->addText($label, $fontBold, $pTight);
            $row->addCell(200,  $borderless)->addText(':',    $font,     $pTight);
            $row->addCell(7826, $borderless)->addText($value, $font,     $pValue);
        }
    }

    /**
     * TTD 3 kolom (3119 / 2693 / 4513) — sesuai DOCX referensi.
     * 3 baris: Label → Image → Nama (bold + underline).
     * Semua cell after=0 (tight).
     */
    private function addTtdTigaKolom(Surat $surat): void
    {
        $ttds     = $surat->ttds;
        $ttdKiri  = $ttds->get(0); // Pihak Pertama (Investor)
        $ttdKanan = $ttds->get(1); // Pihak Kedua (PT BRM)

        $capMedia = $surat->getFirstMedia('cap');
        $capPath  = $capMedia
            ? str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $capMedia->getPath())
            : null;

        $ttdKiriMedia  = $ttdKiri?->getFirstMedia('ttd');
        $ttdKiriPath   = $ttdKiriMedia
            ? str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $ttdKiriMedia->getPath())
            : null;

        $ttdKananMedia = $ttdKanan?->getFirstMedia('ttd');
        $ttdKananPath  = $ttdKananMedia
            ? str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $ttdKananMedia->getPath())
            : null;

        // Lebar kolom sesuai DOCX referensi (3119/2693/4513)
        $wKiri   = 3119;
        $wSpacer = 3500;
        $wKanan  = 3706;

        $borderless = ['borderSize' => 0, 'borderColor' => 'FFFFFF'];
        $font     = ['size' => $this->fontSize, 'name' => $this->fontName];
        $fontNama = $font + ['bold' => true, 'underline' => 'single'];

        $pCenter = ['alignment' => Jc::CENTER, 'spaceBefore' => 0, 'spaceAfter' => 0];
        $pTight  = ['spaceBefore' => 0, 'spaceAfter' => 0];

        $table = $this->section->addTable($borderless);

        // ── Baris 1: Label ─────────────────────────────
        $row = $table->addRow();
        $row->addCell($wKiri, $borderless)
            ->addText($ttdKiri?->label ?? 'Pihak Pertama', $font, $pCenter);
        $row->addCell($wSpacer, $borderless)->addText('', $font, $pTight);
        $row->addCell($wKanan, $borderless)
            ->addText($ttdKanan?->label ?? 'Pihak Kedua', $font, $pCenter);

        // ── Baris 2: Image ─────────────────────────────
        $row = $table->addRow();

        // Kiri — Pihak Pertama (TTD saja, tanpa cap)
        $cellKiri = $row->addCell($wKiri, $borderless);
        $imgStyle90 = [
            'width'         => 80,
            'height'        => 80,
            'wrappingStyle' => 'inline',
        ];

        if ($ttdKiriPath && file_exists($ttdKiriPath)) {
            $this->safeAddImageWithParagraph($cellKiri, $ttdKiriPath, $imgStyle90, $pCenter);
        } else {
            $cellKiri->addText('', $font, $pCenter);
        }

        // Spacer
        $row->addCell($wSpacer, $borderless)->addText('', $font, $pTight);

        // Kanan — Pihak Kedua (cap + TTD merged)
        $cellKanan = $row->addCell($wKanan, $borderless);
        $imgStyleMerged = [
            'width'         => 80,
            'height'        => 80,
            'wrappingStyle' => 'inline',
        ];

        $placed = false;

        if ($capPath && $ttdKananPath && file_exists($capPath) && file_exists($ttdKananPath)) {
            $merged = $this->mergeCapTtd($capPath, $ttdKananPath);
            if ($merged) {
                $placed = $this->safeAddImageWithParagraph(
                    $cellKanan, $merged, $imgStyleMerged, $pCenter
                );
            }
            if (!$placed) {
                $placed = $this->safeAddImageWithParagraph(
                    $cellKanan, $ttdKananPath, $imgStyle90, $pCenter
                );
            }
        } elseif ($ttdKananPath && file_exists($ttdKananPath)) {
            $placed = $this->safeAddImageWithParagraph(
                $cellKanan, $ttdKananPath, $imgStyle90, $pCenter
            );
        } elseif ($capPath && file_exists($capPath)) {
            $placed = $this->safeAddImageWithParagraph(
                $cellKanan,
                $capPath,
                ['width' => 80, 'height' => 80, 'wrappingStyle' => 'inline'],
                $pCenter
            );
        }

        if (!$placed) {
            $cellKanan->addText('', $font, $pCenter);
        }

        // ── Baris 3: Nama (bold + underline) ───────────
        $row = $table->addRow();
        $row->addCell($wKiri, $borderless)
            ->addText($ttdKiri?->nama_penandatangan ?? '—', $fontNama, $pCenter);
        $row->addCell($wSpacer, $borderless)->addText('', $font, $pTight);
        $row->addCell($wKanan, $borderless)
            ->addText($ttdKanan?->nama_penandatangan ?? '—', $fontNama, $pCenter);
    }

    private function addBulletList(array $bullets): void
    {
        $borderless = ['borderSize' => 0, 'borderColor' => 'FFFFFF'];
        $font       = ['size' => $this->fontSize, 'name' => $this->fontName];
 
        // Lebar kolom — total 9026 (lebar konten A4 default)
        // wBullet 360 ≈ 6mm: cukup untuk glyph "•" + cell margin default Word (~108 twip kiri-kanan)
        // wText 8666: sisa lebar. Wrap baris kedua otomatis align di sini.
        $wSpacer = 300;
        $wBullet = 360;
        $wText   = 8366;
 
        // Bullet glyph: no extra indent — biarkan cell margin default jadi
        // posisi natural bullet (≈ 108 twip dari margin kiri, mirip Blade
        // tanpa margin-left).
        $pTight  = ['spaceBefore' => 40, 'spaceAfter' => 40];
        // Text: JUSTIFIED — match Blade yang inherit .content { text-align: justify }
        $pText   = $pTight + ['alignment' => Jc::BOTH];
 
        $table = $this->section->addTable($borderless);
        foreach ($bullets as $bullet) {
            $row = $table->addRow();
            $row->addCell($wSpacer, $borderless)->addText('', $font, $pTight);
            $row->addCell($wBullet, $borderless)->addText('•', $font, $pTight);
            $row->addCell($wText,   $borderless)->addText($bullet, $font, $pText);
        }
    }
}