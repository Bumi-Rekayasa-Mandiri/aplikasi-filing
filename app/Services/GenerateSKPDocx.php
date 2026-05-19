<?php

namespace App\Services;

use App\Models\Surat;
use Carbon\Carbon;
use PhpOffice\PhpWord\SimpleType\Jc;
use PhpOffice\PhpWord\Style\ListItem;

class GenerateSKPDocx extends BaseDocxGenerator
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
        ])->addText('SURAT PEMBERITAHUAN', [
            'bold'      => true,
            'size'      => 14,
            'name'      => $this->fontName,
            'underline' => 'single',
        ]);

        // ── NOMOR + TANGGAL ───────────────────────────
        // SKP tidak punya "Hal" — cukup 1 baris × 2 kolom (Nomor | Karawang)
        $this->addNomorTanggalSKPLocal($surat, $tgl);

        // ── Pembuka ───────────────────────────────────
        $this->addParagrafJustified('Yang bertanda tangan di bawah ini :');

        // ── Identitas penandatangan (BOLD labels) ─────
        // Lebar kolom: 1418 / 200 / 7408 — sesuai referensi
        $this->addIdentitasBold([
            ['Nama',       'Ilman Sunaryo'],
            ['Jabatan',    'Direktur'],
            ['Perusahaan', 'PT. Bumi Rekayasa Mandiri'],
            ['Alamat',     'Ruko Grand Taruma Blok D8/DC, Telukjambe Timur, Karawang, Indonesia'],
        ], labelWidth: 1418, valueWidth: 7408);

        $this->addParagrafJustified('Dengan ini menerangkan bahwa :');

        // ── Identitas karyawan (BOLD labels, label kolom lebih lebar) ──
        // Lebar kolom: 2400 / 200 / 6426 — muat "Departemen / Bagian"
        $this->addIdentitasBold([
            ['Nama',                $surat->nama             ?? '—'],
            ['Jabatan Terakhir',    $surat->jabatan_terakhir ?? '—'],
            ['Departemen / Bagian', $surat->departemen       ?? '—'],
        ], labelWidth: 2400, valueWidth: 6426);

        // ── Paragraf 1 — bold sebagian ────────────────
        $p = $this->section->addTextRun([
            'alignment'   => Jc::BOTH,
            'spaceBefore' => 80,
            'spaceAfter'  => 80,
        ]);
        $p->addText('Telah ', ['size' => $this->fontSize, 'name' => $this->fontName]);
        $p->addText(
            "tidak lagi bekerja di PT. Bumi Rekayasa Mandiri terhitung sejak tanggal {$tgl}",
            ['bold' => true, 'size' => $this->fontSize, 'name' => $this->fontName]
        );
        $p->addText(
            ', dengan alasan mengundurkan diri.',
            ['size' => $this->fontSize, 'name' => $this->fontName]
        );

        $this->addParagrafJustified(
            'Segala tindakan yang dilakukan setelah berakhirnya hubungan kerja sepenuhnya menjadi ' .
            'tanggung jawab pribadi yang bersangkutan dan tidak menjadi tanggung jawab perusahaan.'
        );

        $this->addParagrafJustified(
            'Apabila terdapat hal-hal yang perlu dikonfirmasi atau terkait dengan pekerjaan yang ' .
            'bersangkutan, dapat menghubungi Ilman Sunaryo di nomor +62811964060 atau email berikut:'
        );

        // ── Bullet email — TIGHT (spaceAfter=0) ───────
        $listStyle = ['listType' => ListItem::TYPE_BULLET_FILLED];
        $bulletFont = ['size' => $this->fontSize, 'name' => $this->fontName];
        $bulletPara = ['spaceBefore' => 0, 'spaceAfter' => 0];

        foreach ([
            'bumirekayasa.mandiri@gmail.com',
            'info@bumirekayasamandiri.co.id',
            'info@bumirekamandiri.id',
        ] as $email) {
            // addListItem signature di PHPWord 1.4:
            // (text, depth, fontStyle, listStyle, paragraphStyle)
            $this->section->addListItem(
                $email,
                0,
                $bulletFont,
                $listStyle,
                $bulletPara
            );
        }

        $this->addParagrafJustified(
            'Demikian surat pemberitahuan ini dibuat dengan sebenar-benarnya. ' .
            'Atas perhatian dan kerja samanya, kami ucapkan terima kasih.'
        );

        // ── TTD rata kanan dengan tanggal di atas ─────
        $this->addTtdRataKananSKP($surat, $tgl);

        return $this->save('SKP', $surat->id);
    }

    /**
     * Nomor (kolom kiri) | Karawang, [tgl] (kolom kanan).
     * Format: kolon ikut di dalam teks ("Nomor : 002/..."), bukan kolom terpisah.
     */
    private function addNomorTanggalSKPLocal(Surat $surat, string $tgl): void
    {
        $borderless = ['borderSize' => 0, 'borderColor' => 'FFFFFF'];
        $font   = ['size' => $this->fontSize, 'name' => $this->fontName];
        $pLeft  = ['spaceBefore' => 0, 'spaceAfter' => 120];
        $pRight = ['spaceBefore' => 0, 'spaceAfter' => 120, 'alignment' => Jc::RIGHT];

        $table = $this->section->addTable($borderless);
        $row   = $table->addRow();

        $row->addCell(4513, $borderless)
            ->addText('Nomor : ' . ($surat->nomor_surat ?? '—'), $font, $pLeft);
        $row->addCell(4513, $borderless)
            ->addText("Karawang, {$tgl}", $font, $pRight);
    }

    /**
     * Paragraf justified dengan spacing tight (before/after = 80).
     */
    private function addParagrafJustified(string $text): void
    {
        $this->section->addText(
            $text,
            ['size' => $this->fontSize, 'name' => $this->fontName],
            ['alignment' => Jc::BOTH, 'spaceBefore' => 80, 'spaceAfter' => 80]
        );
    }

    /**
     * Tabel identitas dengan label BOLD: 3 kolom (label | colon | value).
     * spaceAfter cell = 120 → tight tapi tetap readable.
     */
    private function addIdentitasBold(array $rows, int $labelWidth, int $valueWidth): void
    {
        $borderless = ['borderSize' => 0, 'borderColor' => 'FFFFFF'];
        $font       = ['size' => $this->fontSize, 'name' => $this->fontName];
        $fontBold   = $font + ['bold' => true];
        $pCell      = ['spaceBefore' => 0, 'spaceAfter' => 120];

        $colonWidth = 200;

        $table = $this->section->addTable($borderless);
        foreach ($rows as [$label, $value]) {
            $row = $table->addRow();
            $row->addCell($labelWidth, $borderless)->addText($label, $fontBold, $pCell);
            $row->addCell($colonWidth, $borderless)->addText(':',    $font,     $pCell);
            $row->addCell($valueWidth, $borderless)->addText($value, $font,     $pCell);
        }
    }

    /**
     * Blok TTD rata kanan dengan tight spacing (spaceAfter=0 per row).
     * Layout: Karawang [tgl] → Hormat Kami → Image → Nama (bold+underline) → Jabatan.
     */
    private function addTtdRataKananSKP(Surat $surat, string $tgl): void
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

        $pNoGap     = ['alignment' => Jc::CENTER, 'spaceBefore' => 0, 'spaceAfter' => 0];
        $pNoGapLeft = ['spaceBefore' => 0, 'spaceAfter' => 0];

        // Lebar kolom sesuai referensi DOCX
        $leftWidth  = 5183;
        $rightWidth = 4126;

        $table = $this->section->addTable($borderless);

        $addRow = function ($table, callable $fillRight)
            use ($borderless, $leftWidth, $rightWidth, $font, $pNoGapLeft)
        {
            $row = $table->addRow();
            $row->addCell($leftWidth, $borderless)->addText('', $font, $pNoGapLeft);
            $right = $row->addCell($rightWidth, $borderless);
            $fillRight($right);
        };

        // Baris 1: Tanggal
        $addRow($table, function ($cell) use ($tgl, $font, $pNoGap) {
            $cell->addText("Karawang, {$tgl}", $font, $pNoGap);
        });

        // Baris 2: Label ("Hormat Kami")
        $addRow($table, function ($cell) use ($label, $font, $pNoGap) {
            $cell->addText($label, $font, $pNoGap);
        });

        // Baris 3: Image (merged cap+ttd) — bungkus TextRun via safeAddImageWithParagraph
        $addRow($table, function ($imgCell) use ($capPath, $ttdPath, $font, $pNoGap) {
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
        });

        // Baris 4: Nama (bold + underline)
        $addRow($table, function ($cell) use ($nama, $fontNama, $pNoGap) {
            $cell->addText($nama, $fontNama, $pNoGap);
        });

        // Baris 5: Jabatan
        $addRow($table, function ($cell) use ($jabatan, $font, $pNoGap) {
            $cell->addText($jabatan, $font, $pNoGap);
        });
    }
}