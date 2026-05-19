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
        $tgl = Carbon::parse($surat->tanggal_surat)
            ->locale('id')
            ->translatedFormat('d F Y');

        // ── KOP ───────────────────────────────────────
        $this->addKop();

        // ── Email & Phone ─────────────────────────────
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
        ])->addText('PERMOHONAN KERINGANAN DENDA', [
            'bold'      => true,
            'size'      => 14,
            'name'      => $this->fontName,
            'underline' => 'single',
        ]);

        // ── NOMOR + TANGGAL ───────────────────────────
        // 2 kolom 4513/4513, colon ikut di teks ("Nomor : 001/SK-BRM/...")
        $this->addNomorTanggalSKLocal($surat, $tgl);

        // ── KEPADA YTH ────────────────────────────────
        $this->section->addText(
            'Kepada Yth. ' . ($surat->tujuan ?? '—'),
            ['size' => $this->fontSize, 'name' => $this->fontName],
            ['spaceBefore' => 200, 'spaceAfter' => 200]
        );

        $this->section->addText(
            'Dengan hormat,',
            ['size' => $this->fontSize, 'name' => $this->fontName],
            ['spaceBefore' => 100, 'spaceAfter' => 200]
        );

        // ── BODY: 8 paragraf, semuanya before=80, after=80 ──
        $this->addParagrafJustified(
            'Sehubungan dengan adanya evaluasi atas progres pekerjaan dan pengajuan tambahan pekerjaan ' .
            'yang telah kami sampaikan sebelumnya, bersama ini kami dari PT Bumi Rekayasa Mandiri ' .
            'mengajukan permohonan agar nilai denda yang dikenakan dapat disesuaikan.'
        );

        $this->addParagrafJustified(
            'Adapun berdasarkan hasil perhitungan awal, total nilai denda sebesar:'
        );

        // Hasil denda — BOLD, justified
        $this->section->addText(
            $surat->hasil_denda ?? '—',
            ['bold' => true, 'size' => $this->fontSize, 'name' => $this->fontName],
            ['alignment' => Jc::BOTH, 'spaceBefore' => 50, 'spaceAfter' => 150]
        );

        $this->addParagrafJustified(
            'Kami memohon keringanan denda agar nilai denda dikenakan sebesar :'
        );

        // Keringanan denda — BOLD, justified
        $this->section->addText(
            $surat->keringanan_denda ?? '—',
            ['bold' => true, 'size' => $this->fontSize, 'name' => $this->fontName],
            ['alignment' => Jc::BOTH, 'spaceBefore' => 50, 'spaceAfter' => 150]
        );

        $this->addParagrafJustified(
            'yang mana jumlah tersebut akan langsung dipotong dari nilai tagihan kami, sesuai dengan ' .
            'usulan dan pertimbangan nilai pekerjaan tambahan yang telah kami laksanakan di luar ' .
            'addendum awal.'
        );

        $this->addParagrafJustified(
            'Demikian surat ini kami sampaikan. Besar harapan kami agar permohonan ini dapat diterima ' .
            'demi kelancaran kerja sama antara kedua belah pihak.'
        );

        $this->addParagrafJustified(
            'Atas perhatian dan kerja samanya, kami ucapkan terima kasih.'
        );

        // ── TTD rata kanan (tanggal di dalam tabel) ───
        // CATATAN: TIDAK ada addSpacing() di sini. Gap visual didapat dari
        // spaceBefore pada row pertama TTD (300 twip ≈ 1 line height).
        $this->addTtdRataKananSK($surat, $tgl);

        return $this->save('SK', $surat->id);
    }

    /**
     * Nomor + Karawang dalam 1 baris × 2 kolom.
     * Kolon ikut di teks ("Nomor : 001/...") — match referensi.
     */
    private function addNomorTanggalSKLocal(Surat $surat, string $tgl): void
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
     * Paragraf body justified dengan spacing tight 80/80 — match referensi.
     */
    private function addParagrafJustified(string $text): void
    {
        $this->section->addText(
            $text,
            ['size' => $this->fontSize, 'name' => $this->fontName],
            ['alignment' => Jc::BOTH, 'spaceBefore' => 200, 'spaceAfter' => 200]
        );
    }

    /**
     * Blok TTD rata kanan. Tanggal + Hormat Kami → Image → Nama → Jabatan.
     * Tanggal di DALAM tabel (beda dengan GRS yang terpisah).
     * Row pertama punya spaceBefore=300 sebagai pengganti addSpacing().
     */
    private function addTtdRataKananSK(Surat $surat, string $tgl): void
    {
        $ttd     = $surat->ttds->first();
        $nama    = $ttd?->nama_penandatangan ?? 'Ilman Sunaryo';
        $jabatan = $ttd?->jabatan            ?? 'Direktur';
        $label   = $ttd?->label              ?? 'Hormat Kami';

        $capMedia = $surat->getFirstMedia('cap');
        $capPath  = $capMedia
            ? str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $capMedia->getPath())
            : null;

        $ttdMedia = $ttd?->getFirstMedia('ttd');
        $ttdPath  = $ttdMedia
            ? str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $ttdMedia->getPath())
            : null;

        $borderless = ['borderSize' => 0, 'borderColor' => 'FFFFFF'];
        $font       = ['size' => $this->fontSize, 'name' => $this->fontName];
        $fontNama   = $font + ['bold' => true, 'underline' => 'single'];

        // Paragraph style: tight, center untuk cell kanan
        $pNoGap        = ['alignment' => Jc::CENTER, 'spaceBefore' => 0, 'spaceAfter' => 0];
        $pNoGapWithBefore = ['alignment' => Jc::CENTER, 'spaceBefore' => 300, 'spaceAfter' => 0];
        $pNoGapLeft    = ['spaceBefore' => 0, 'spaceAfter' => 0];

        // Lebar kolom sesuai referensi (5812 / 4513)
        $leftWidth  = 5812;
        $rightWidth = 4513;

        $table = $this->section->addTable($borderless);

        // Baris 1: Tanggal (dengan spaceBefore untuk pisah dari body)
        $row = $table->addRow();
        $row->addCell($leftWidth, $borderless)->addText('', $font, $pNoGapLeft);
        $row->addCell($rightWidth, $borderless)
            ->addText("Karawang, {$tgl}", $font, $pNoGapWithBefore);

        // Baris 2: Label "Hormat Kami"
        $row = $table->addRow();
        $row->addCell($leftWidth, $borderless)->addText('', $font, $pNoGapLeft);
        $row->addCell($rightWidth, $borderless)->addText($label, $font, $pNoGap);

        // Baris 3: Image (merged cap+ttd) via safeAddImageWithParagraph
        $row = $table->addRow();
        $row->addCell($leftWidth, $borderless)->addText('', $font, $pNoGapLeft);
        $imgCell = $row->addCell($rightWidth, $borderless);

        $imgStyle = [
            'width'         => 90,    // 3cm @ 96 DPI
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
                $imgCell, $capPath, $imgStyle, $pNoGap
            );
        }

        if (!$placed) {
            $imgCell->addText('', $font, $pNoGap);
        }

        // Baris 4: Nama (bold + underline)
        $row = $table->addRow();
        $row->addCell($leftWidth, $borderless)->addText('', $font, $pNoGapLeft);
        $row->addCell($rightWidth, $borderless)->addText($nama, $fontNama, $pNoGap);

        // Baris 5: Jabatan
        $row = $table->addRow();
        $row->addCell($leftWidth, $borderless)->addText('', $font, $pNoGapLeft);
        $row->addCell($rightWidth, $borderless)->addText($jabatan, $font, $pNoGap);
    }
}