<?php

namespace App\Services;

use App\Models\Surat;
use Carbon\Carbon;
use PhpOffice\PhpWord\SimpleType\Jc;
use PhpOffice\PhpWord\Shared\Converter;

class GenerateIEIDocx extends BaseDocxGenerator
{
    public function handle(Surat $surat): string
    {
        $this->setup();

        // ── Section 1: Surat Utama (Surat Garansi Pemasangan) ──
        $this->buildSuratUtama($surat);

        // ── Section 2: Guarantee Letter ───────────────────────
        $this->addNewSection();
        $this->buildGuaranteeLetter($surat);

        // ── Section 3: Lampiran.png ────────────────────────────
        $this->addNewSection();
        $this->buildLampiranPng();

        return $this->save('IEI', $surat->id);
    }

    // ══════════════════════════════════════════════
    // SECTION 1: SURAT GARANSI PEMASANGAN
    // ══════════════════════════════════════════════
    private function buildSuratUtama(Surat $surat): void
    {
        $tgl = Carbon::parse($surat->tanggal_surat)->locale('id')->translatedFormat('d F Y');

        // Kop: pakai lampiran.png (kop khusus IEI), fallback ke kop.png
        $kopPath = public_path('assets/lampiran.png');
        if (!file_exists($kopPath)) {
            $kopPath = public_path('assets/kop.png');
        }
        if (file_exists($kopPath)) {
            $this->section->addImage($kopPath, [
                'width'         => 450,
                'height'        => 100,
                'alignment'     => Jc::CENTER,
                'wrappingStyle' => 'inline',
            ]);
        }

        $this->addSpacing();

        // ── JUDUL ─────────────────────────────────
        $this->section->addTextRun([
            'alignment' => Jc::CENTER, 'spaceBefore' => 200, 'spaceAfter' => 0,
        ])->addText('SURAT GARANSI PEMASANGAN', [
            'bold' => true, 'size' => 20, 'name' => $this->fontName, 'underline' => 'single',
        ]);

        // ── NOMOR ─────────────────────────────────
        $this->section->addText(
            'Nomor : ' . ($surat->nomor_surat ?? '—'),
            ['size' => 16, 'name' => $this->fontName],
            ['alignment' => Jc::CENTER, 'spaceBefore' => 80, 'spaceAfter' => 200]
        );

        // ── IDENTITAS PENANDATANGAN ────────────────
        $this->addParagraf('Yang bertanda tangan di bawah ini:');
        $this->addIdentitasTable([
            ['Nama',    'Ilman Sunaryo'],
            ['Jabatan', 'Direktur'],
        ], 800);

        $this->addSpacing();

        // ── DETAIL PEKERJAAN ──────────────────────
        $this->addParagraf('Bersama ini memberikan jaminan garansi untuk:');
        $this->addIdentitasTable([
            ['Project', $surat->project      ?? '—'],
            ['Lokasi',  $surat->lokasi_kerja ?? '—'],
        ], 800);

        $this->addSpacing();

        // ── PERIODE & JENIS PEKERJAAN ─────────────
        $this->section->addText(
            'Garansi berlaku mulai ' . ($surat->isi_surat ?? '—') . ', meliputi :',
            ['size' => $this->fontSize, 'name' => $this->fontName, 'underline' => 'single'],
            ['alignment' => Jc::CENTER, 'spaceBefore' => 80, 'spaceAfter' => 80]
        );

        $jenisPekerjaan = json_decode($surat->jenis_pekerjaan ?? '[]', true) ?? [];
        foreach ($jenisPekerjaan as $idx => $item) {
            $this->section->addText(
                ($idx + 1) . '. ' . $item,
                ['bold' => true, 'size' => $this->fontSize, 'name' => $this->fontName],
                ['alignment' => Jc::BOTH, 'spaceBefore' => 40, 'spaceAfter' => 40,
                 'indentation' => ['left' => Converter::cmToTwip(1)]]
            );
        }

        $this->addSpacing();

        // ── PENGECUALIAN GARANSI ──────────────────
        $this->addParagraf('Garansi tidak berlaku apabila kerusakan di akibatkan oleh :');
        foreach ([
            '1. Kesalahan Prosedur Pemakaian (Human Error)',
            '2. Gangguan Bencana Alam (Force Majure)',
            '3. Kerusakan karena Pekerjaan dari Pihak lain',
        ] as $item) {
            $this->section->addText($item, [
                'bold' => true, 'size' => $this->fontSize, 'name' => $this->fontName,
            ], ['alignment' => Jc::BOTH, 'spaceBefore' => 40, 'spaceAfter' => 40,
                'indentation' => ['left' => Converter::cmToTwip(1)]]);
        }

        $this->addSpacing();

        $this->addParagraf('Demikian Garansi ini kami buat untuk dapat dipergunakan sebagaimana mestinya.');

        // ── TANGGAL & TTD RATA KIRI ───────────────
        $this->section->addText("Karawang, {$tgl}", [
            'size' => $this->fontSize, 'name' => $this->fontName,
        ], ['spaceBefore' => 400, 'spaceAfter' => 0]);

        $this->addSpacing();
        $this->addTtdRataKiri($surat);
    }

    private function addTtdRataKiri(Surat $surat): void
    {
        $ttd = $surat->ttds->first();
        if (!$ttd) return;

        $nama    = $ttd->nama_penandatangan ?? 'Ilman Sunaryo';
        $jabatan = $ttd->jabatan ?? 'Direktur';
        // Dari DOCX asli: label "PT. BUMI REKAYASA MANDIRI"
        $label   = $ttd->label ?? 'PT. BUMI REKAYASA MANDIRI';

        $capMedia = $surat->getFirstMedia('cap');
        $capPath  = $capMedia
            ? str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $capMedia->getPath()) : null;

        $ttdMedia = $ttd->getFirstMedia('ttd');
        $ttdPath  = $ttdMedia
            ? str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $ttdMedia->getPath()) : null;

        // Label (misal "PT. BUMI REKAYASA MANDIRI")
        $this->section->addText($label, [
            'size' => $this->fontSize, 'name' => $this->fontName,
        ], ['spaceAfter' => 0]);

        // Gambar TTD — langsung di section (rata kiri, tanpa tabel)
        if ($capPath && $ttdPath && file_exists($capPath) && file_exists($ttdPath)) {
            $merged = $this->mergeCapTtd($capPath, $ttdPath);
            if ($merged) {
                // ✅ $this->section valid karena TTD ini tidak dalam tabel
                $this->safeAddImage($this->section, $merged, [
                    'width'         => 113, 'height' => 113, // 3cm × 3cm
                    'alignment'     => Jc::LEFT,
                    'wrappingStyle' => 'inline',
                ]);
            } else {
                $this->safeAddImage($this->section, $ttdPath, [
                    'width' => 90, 'height' => 90,
                    'alignment' => Jc::LEFT, 'wrappingStyle' => 'inline',
                ]);
            }
        } elseif ($ttdPath && file_exists($ttdPath)) {
            $this->section->addImage($ttdPath, [
                'width'         => 90,
                'height'        => 90,
                'alignment'     => Jc::LEFT,
                'wrappingStyle' => 'inline',
            ]);
        } elseif ($capPath && file_exists($capPath)) {
            $this->section->addImage($capPath, [
                'width'         => 100,
                'height'        => 100,
                'alignment'     => Jc::LEFT,
                'wrappingStyle' => 'inline',
            ]);
        } else {
            $this->addSpacing(4);
        }

        // Nama (bold, underline)
        $this->section->addText($nama, [
            'bold' => true, 'size' => $this->fontSize,
            'name' => $this->fontName, 'underline' => 'single',
        ], ['alignment' => Jc::LEFT, 'spaceAfter' => 0]);

        // Jabatan
        $this->section->addText($jabatan, [
            'size' => $this->fontSize, 'name' => $this->fontName,
        ], ['alignment' => Jc::LEFT]);
    }

    // ══════════════════════════════════════════════
    // SECTION 2: GUARANTEE LETTER
    // ══════════════════════════════════════════════
    private function buildGuaranteeLetter(Surat $surat): void
    {
        // Kop lampiran
        $kopLampiran = public_path('assets/lampiran.png');
        if (file_exists($kopLampiran)) {
            $this->section->addImage($kopLampiran, [
                'width'         => 450,
                'height'        => 80,
                'alignment'     => Jc::CENTER,
                'wrappingStyle' => 'inline',
            ]);
        }
        $this->addSpacing();

        $ttds     = $surat->ttds;
        $ttdKiri  = $ttds->get(0); // PT BRM (index 0)
        $ttdKanan = $ttds->get(1); // Penerima (index 1)

        $tglInggris     = Carbon::parse($surat->tanggal_surat)->locale('en')->translatedFormat('M, d Y');
        $tglHandingOver = Carbon::parse($surat->tanggal_surat)->locale('en')->translatedFormat('M jS Y');
        $dokumenList    = $surat->meta['dokumen'] ?? [];

        // ── Header: Penerima | Tanggal (col dari DOCX: 5500 | 3526) ──
        $style = ['borderSize' => 0, 'borderColor' => 'FFFFFF'];
        $table = $this->section->addTable($style);
        $row   = $table->addRow();

        $leftCell = $row->addCell(5500, ['borderSize' => 0, 'borderColor' => 'FFFFFF']);
        $leftCell->addText($surat->tujuan ?? '—', [
            'bold' => true, 'size' => $this->fontSize, 'name' => $this->fontName,
        ]);
        if ($ttdKanan) {
            $leftCell->addText('Attn : ' . $ttdKanan->nama_penandatangan, [
                'size' => $this->fontSize, 'name' => $this->fontName,
            ]);
        }
        $row->addCell(3526, ['borderSize' => 0, 'borderColor' => 'FFFFFF'])
            ->addText($tglInggris, [
                'size' => $this->fontSize, 'name' => $this->fontName,
            ], ['alignment' => Jc::RIGHT]);

        $this->addSpacing();

        // ── BODY ──────────────────────────────────
        $this->section->addText('Dear Sirs,', [
            'size' => $this->fontSize, 'name' => $this->fontName,
        ], ['spaceAfter' => 80]);

        $this->section->addText(
            strtoupper($surat->project ?? $surat->judul ?? '—'),
            ['bold' => true, 'size' => 12, 'name' => $this->fontName],
            ['alignment' => Jc::CENTER, 'spaceBefore' => 160, 'spaceAfter' => 0]
        );

        $this->section->addText(
            "Official Handing Over on {$tglHandingOver}",
            ['size' => $this->fontSize, 'name' => $this->fontName],
            ['alignment' => Jc::CENTER, 'spaceAfter' => 160]
        );

        $this->section->addText(
            "We refer to above mention and would like take this opportunity to thank you for your " .
            "full co-operation and support in the achievement of the completed project and the " .
            "official handing over on {$tglHandingOver}.",
            ['size' => $this->fontSize, 'name' => $this->fontName],
            ['alignment' => Jc::BOTH, 'spaceAfter' => 80]
        );

        $this->section->addText(
            'We have the pleasure to submit below document for your perusal and acceptance.',
            ['size' => $this->fontSize, 'name' => $this->fontName],
            ['alignment' => Jc::BOTH, 'spaceAfter' => 80]
        );

        foreach ($dokumenList as $idx => $dok) {
            $this->section->addText(
                ($idx + 1) . '. ' . $dok,
                ['size' => $this->fontSize, 'name' => $this->fontName],
                ['indentation' => ['left' => 360], 'spaceAfter' => 40]
            );
        }

        $this->section->addText(
            'Henceforth, once again, thank you for your precious time to be with me at the handing over. thank you.',
            ['size' => $this->fontSize, 'name' => $this->fontName],
            ['alignment' => Jc::BOTH, 'spaceBefore' => 80, 'spaceAfter' => 160]
        );

        $this->section->addText('Your faithfully', [
            'size' => $this->fontSize, 'name' => $this->fontName,
        ], ['spaceAfter' => 0]);

        $this->addSpacing();

        // ── TTD 2 KOLOM ───────────────────────────
        $this->addTtdDuaKolomLampiran($surat, $ttdKiri, $ttdKanan);
    }

    private function addTtdDuaKolomLampiran(Surat $surat, $ttdKiri, $ttdKanan): void
    {
        $capMedia = $surat->getFirstMedia('cap');
        $capPath  = $capMedia
            ? str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $capMedia->getPath()) : null;

        $ttdKiriMedia  = $ttdKiri?->getFirstMedia('ttd');
        $ttdKiriPath   = $ttdKiriMedia
            ? str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $ttdKiriMedia->getPath()) : null;

        $ttdKananMedia = $ttdKanan?->getFirstMedia('ttd');
        $ttdKananPath  = $ttdKananMedia
            ? str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $ttdKananMedia->getPath()) : null;

        // Lebar dari DOCX asli: 4513 | 4513
        $style = ['borderSize' => 0, 'borderColor' => 'FFFFFF'];
        $table = $this->section->addTable($style);

        // ── Baris Label ────────────────────────────
        $row = $table->addRow();
        $row->addCell(4513, ['borderSize' => 0, 'borderColor' => 'FFFFFF'])
            ->addText('PT. BUMI REKAYASA MANDIRI', [
                'bold' => true, 'size' => $this->fontSize, 'name' => $this->fontName,
            ], ['alignment' => Jc::LEFT]);
        $row->addCell(4513, ['borderSize' => 0, 'borderColor' => 'FFFFFF'])
            ->addText($surat->tujuan ?? '—', [
                'size' => $this->fontSize, 'name' => $this->fontName,
            ], ['alignment' => Jc::RIGHT]);

        // ── Baris Gambar ───────────────────────────
        $row      = $table->addRow();
        $cellKiri = $row->addCell(4513, ['borderSize' => 0, 'borderColor' => 'FFFFFF']);

        // Kiri: PT BRM — cap + TTD direktur
        if ($capPath && $ttdKiriPath && file_exists($capPath) && file_exists($ttdKiriPath)) {
            $merged = $this->mergeCapTtd($capPath, $ttdKiriPath);
            if ($merged) {
                // ✅ $cellKiri bukan $this->section
                $this->safeAddImage($cellKiri, $merged, [
                    'width'         => 113, 'height' => 113, // 3cm × 3cm
                    'alignment'     => Jc::LEFT,
                    'wrappingStyle' => 'inline',
                ]);
            } else {
                $this->safeAddImage($cellKiri, $ttdKiriPath, [
                    'width' => 90, 'height' => 90,
                    'alignment' => Jc::LEFT, 'wrappingStyle' => 'inline',
                ]);
            }
        } elseif ($ttdKiriPath && file_exists($ttdKiriPath)) {
            $this->safeAddImage($cellKiri, $ttdKiriPath, [
                'width'         => 90,
                'height'        => 90,
                'alignment'     => Jc::LEFT,
                'wrappingStyle' => 'inline',
            ]);
        } else {
            $cellKiri->addText('');
        }

        // Kanan: Penerima — TTD saja (tanpa cap)
        $cellKanan = $row->addCell(4513, ['borderSize' => 0, 'borderColor' => 'FFFFFF']);
        if ($ttdKananPath && file_exists($ttdKananPath)) {
            $this->safeAddImage($cellKanan, $ttdKananPath, [
                'width'         => 90,
                'height'        => 90,
                'alignment'     => Jc::RIGHT,
                'wrappingStyle' => 'inline',
            ]);
        } else {
            $cellKanan->addText('');
        }

        // ── Baris Nama ─────────────────────────────
        $row = $table->addRow();
        $row->addCell(4513, ['borderSize' => 0, 'borderColor' => 'FFFFFF'])
            ->addText($ttdKiri?->nama_penandatangan ?? '—', [
                'bold' => true, 'size' => $this->fontSize,
                'name' => $this->fontName, 'underline' => 'single',
            ], ['alignment' => Jc::LEFT, 'indentation' => ['left' => 360]]);
        $row->addCell(4513, ['borderSize' => 0, 'borderColor' => 'FFFFFF'])
            ->addText($ttdKanan?->nama_penandatangan ?? '—', [
                'bold' => true, 'size' => $this->fontSize,
                'name' => $this->fontName, 'underline' => 'single',
            ], ['alignment' => Jc::RIGHT, 'indentation' => ['right' => 560]]);

        // ── Baris Jabatan ──────────────────────────
        $row = $table->addRow();
        $row->addCell(4513, ['borderSize' => 0, 'borderColor' => 'FFFFFF'])
            ->addText($ttdKiri?->jabatan ?? '—', [
                'size' => $this->fontSize, 'name' => $this->fontName,
            ], ['alignment' => Jc::LEFT, 'indentation' => ['left' => 560]]);
        $row->addCell(4513, ['borderSize' => 0, 'borderColor' => 'FFFFFF'])
            ->addText($ttdKanan?->jabatan ?? '—', [
                'size' => $this->fontSize, 'name' => $this->fontName,
            ], ['alignment' => Jc::RIGHT, 'indentation' => ['right' => 560]]);
    }

    // ══════════════════════════════════════════════
    // SECTION 3: LAMPIRAN.PNG (halaman penuh)
    // ══════════════════════════════════════════════
    private function buildLampiranPng(): void
    {
        $lampiranPath = public_path('assets/lampiran.png');

        if (file_exists($lampiranPath)) {
            $this->section->addImage($lampiranPath, [
                'width'         => 450,
                'height'        => 550,
                'alignment'     => Jc::CENTER,
                'wrappingStyle' => 'inline',
            ]);
        } else {
            $this->section->addText('[File lampiran.png tidak ditemukan]', [
                'size' => $this->fontSize, 'name' => $this->fontName,
            ], ['alignment' => Jc::CENTER]);
        }
    }
}