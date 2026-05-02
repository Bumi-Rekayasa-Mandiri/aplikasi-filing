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
        $this->addSpacing();

        // ── JUDUL ─────────────────────────────────────
        $this->section->addTextRun([
            'alignment'   => Jc::CENTER,
            'spaceBefore' => 200,
            'spaceAfter'  => 200,
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
        ], 1800);

        $this->addSpacing();

        // ── KETERANGAN KENDARAAN ──────────────────────
        $this->addParagraf('Dengan ini menerangkan bahwa data kendaraan tersebut di bawah ini :');

        $this->addIdentitasTable([
            ['Merk/Jenis',   $surat->merk   ?? '—'],
            ['Warna/Tahun',  $surat->warna  ?? '—'],
            ['Rangka/Mesin', $surat->rangka ?? '—'],
        ], 1800);

        $this->addSpacing();

        // ── ISI ───────────────────────────────────────
        $this->addParagraf('Dan telah melepaskan haknya atas kendaraan tersebut di atas.');
        $this->addParagraf('Demikian Surat Pelepasan Hak ini dibuat untuk dipergunakan sebagaimana mestinya.');

        $this->addSpacing();

        // ── TANGGAL KANAN ─────────────────────────────
        $this->section->addText("Karawang, {$tgl}", [
            'size' => $this->fontSize,
            'name' => $this->fontName,
        ], ['alignment' => Jc::RIGHT, 'spaceBefore' => 200]);

        $this->addSpacing();

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

        // ✅ Hapus 'cellMargin' => 0 — properti tidak valid di PHPWord
        $style = ['borderSize' => 0, 'borderColor' => 'FFFFFF'];
        $table = $this->section->addTable($style);

        // ── Baris label ────────────────────────────────
        $row = $table->addRow();
        $row->addCell(4513, ['borderSize' => 0, 'borderColor' => 'FFFFFF'])
            ->addText('');
        $row->addCell(4513, ['borderSize' => 0, 'borderColor' => 'FFFFFF'])
            ->addText($label, [
                'size' => $this->fontSize,
                'name' => $this->fontName,
            ], ['alignment' => Jc::CENTER]);

        // ── Baris gambar ───────────────────────────────
        $row    = $table->addRow();
        $row->addCell(4513, ['borderSize' => 0, 'borderColor' => 'FFFFFF'])
            ->addText('');

        $imgCell = $row->addCell(4513, ['borderSize' => 0, 'borderColor' => 'FFFFFF']);

        if ($capPath && $ttdPath && file_exists($capPath) && file_exists($ttdPath)) {
            $merged = $this->mergeCapTtd($capPath, $ttdPath);
            if ($merged) {
                // ✅ Fix: $imgCell bukan $this->section
                $this->safeAddImage($imgCell, $merged, [
                    'width'         => 113, 'height' => 113, // 3cm × 3cm
                    'alignment'     => Jc::CENTER,
                    'wrappingStyle' => 'inline',
                ]);
            } else {
                $this->safeAddImage($imgCell, $ttdPath, [
                    'width' => 90, 'height' => 90,
                    'alignment' => Jc::CENTER, 'wrappingStyle' => 'inline',
                ]);
            }
        } elseif ($ttdPath && file_exists($ttdPath)) {
            $imgCell->addImage($ttdPath, [
                'width'         => 90,
                'height'        => 90,
                'alignment'     => Jc::CENTER,
                'wrappingStyle' => 'inline',
            ]);
        } elseif ($capPath && file_exists($capPath)) {
            $imgCell->addImage($capPath, [
                'width'         => 100,
                'height'        => 100,
                'alignment'     => Jc::CENTER,
                'wrappingStyle' => 'inline',
            ]);
        } else {
            for ($i = 0; $i < 4; $i++) {
                $imgCell->addText('');
            }
        }

        // ── Baris nama ─────────────────────────────────
        $row = $table->addRow();
        $row->addCell(4513, ['borderSize' => 0, 'borderColor' => 'FFFFFF'])
            ->addText('');
        $row->addCell(4513, ['borderSize' => 0, 'borderColor' => 'FFFFFF'])
            ->addText($nama, [
                'bold'      => true,
                'size'      => $this->fontSize,
                'name'      => $this->fontName,
                'underline' => 'single',
            ], ['alignment' => Jc::CENTER]);

        // ── Baris jabatan ──────────────────────────────
        $row = $table->addRow();
        $row->addCell(4513, ['borderSize' => 0, 'borderColor' => 'FFFFFF'])
            ->addText('');
        $row->addCell(4513, ['borderSize' => 0, 'borderColor' => 'FFFFFF'])
            ->addText($jabatan, [
                'size' => $this->fontSize,
                'name' => $this->fontName,
            ], ['alignment' => Jc::CENTER]);
    }
}