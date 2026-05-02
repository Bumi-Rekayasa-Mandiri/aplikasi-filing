<?php

namespace App\Services;

use App\Models\Surat;
use Carbon\Carbon;
use PhpOffice\PhpWord\SimpleType\Jc;

class GenerateBRM1Docx extends BaseDocxGenerator
{
    public function handle(Surat $surat): string
    {
        $this->setup();

        // ── Section 1: Surat Utama ────────────────
        $this->buildSuratUtama($surat);

        // ── Section 2: Lampiran A — JSA ───────────
        $this->addNewSection();
        $this->buildLampiranJSA($surat);

        // ── Section 3: Lampiran B — Daftar Pekerja ─
        $this->addNewSection();
        $this->buildLampiranPekerja($surat);

        return $this->save('BRM1', $surat->id);
    }

    // ══════════════════════════════════════════════
    // BAGIAN 1: SURAT UTAMA
    // ══════════════════════════════════════════════
    private function buildSuratUtama(Surat $surat): void
    {
        $tgl = Carbon::parse($surat->tanggal_surat)->locale('id')->translatedFormat('d F Y');

        // ── KOP ───────────────────────────────────
        $this->addKop();
        $this->addSpacing();

        // ── JUDUL ─────────────────────────────────
        $this->section->addTextRun([
            'alignment'   => Jc::CENTER,
            'spaceBefore' => 200,
            'spaceAfter'  => 200,
        ])->addText('SURAT PEMBERITAHUAN KERJA', [
            'bold'      => true,
            'size'      => 14,
            'name'      => $this->fontName,
            'underline' => 'single',
        ]);

        // ── KEPADA (single-cell table, no border) ─
        $style = ['borderSize' => 0, 'borderColor' => 'FFFFFF'];
        $tabel = $this->section->addTable($style);
        $row   = $tabel->addRow();
        $cell  = $row->addCell(9026, ['borderSize' => 0, 'borderColor' => 'FFFFFF']);

        $cell->addText('Kepada :', [
            'bold' => true, 'size' => $this->fontSize, 'name' => $this->fontName,
        ], ['spaceAfter' => 0]);
        $cell->addText('Yth. ' . ($surat->tujuan ?? '—'), [
            'bold' => true, 'size' => $this->fontSize, 'name' => $this->fontName,
        ], ['spaceAfter' => 0]);
        $cell->addText('Up. Dept. ' . ($surat->departemen ?? '—'), [
            'size' => $this->fontSize, 'name' => $this->fontName,
        ], ['spaceAfter' => 0]);
        $cell->addText('Bp. ' . ($surat->nama ?? '—'), [
            'size' => $this->fontSize, 'name' => $this->fontName,
        ], ['spaceAfter' => 0]);
        $cell->addText('Di Tempat', [
            'size' => $this->fontSize, 'name' => $this->fontName,
        ], ['spaceAfter' => 100]);

        $this->addSpacing();

        // ── PEMBUKA ───────────────────────────────
        $this->section->addText('Dengan Hormat,', [
            'size' => $this->fontSize, 'name' => $this->fontName,
        ], ['spaceAfter' => 0]);
        $this->section->addText('Bersama ini kami mengajukan pelaksanaan kerja :', [
            'size' => $this->fontSize, 'name' => $this->fontName,
        ], ['spaceAfter' => 80]);

        // ── DETAIL PEKERJAAN (col widths dari DOCX: 2268 | 200 | 7326) ──
        $this->addIdentitasTable([
            ['Lokasi Kerja',    $surat->lokasi_kerja    ?? '—'],
            ['Jenis Pekerjaan', $surat->jenis_pekerjaan ?? '—'],
            ['Waktu',           $surat->waktu           ?? '—'],
            ['Jam Kerja',       $surat->jam_kerja       ?? '—'],
        ], 1500);

        $this->addSpacing();

        $this->addIdentitasTable([
            ['Penanggung Jawab', 'Ilman Sunaryo'],
            ['Jumlah Pekerja',   $surat->jumlah_pekerja ?? '—'],
        ], 1500);

        $this->addSpacing();

        // ── PENUTUP ───────────────────────────────
        $this->section->addText(
            'Atas kerja samanya, kami mengucapkan terima kasih.',
            ['size' => $this->fontSize, 'name' => $this->fontName],
            ['spaceBefore' => 150, 'spaceAfter' => 80]
        );

        $this->section->addText("Karawang, {$tgl}", [
            'size' => $this->fontSize, 'name' => $this->fontName,
        ], ['spaceAfter' => 0]);

        $this->addSpacing();

        // ── TTD ───────────────────────────────────
        $this->addTtdBRM1($surat);
    }

    private function addTtdBRM1(Surat $surat): void
    {
        $ttds   = $surat->ttds;
        $jumlah = $ttds->count() ?: 1;

        $capMedia = $surat->getFirstMedia('cap');
        $capPath  = $capMedia
            ? str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $capMedia->getPath())
            : null;

        // Lebar per kolom: 9026 / jumlah (dari DOCX: 2256 untuk 4 TTD)
        $colWidth = intval(9026 / $jumlah);
        $style    = ['borderSize' => 0, 'borderColor' => 'FFFFFF'];
        $table    = $this->section->addTable($style);

        // ── Label row ─────────────────────────────
        $row = $table->addRow();
        foreach ($ttds as $ttd) {
            $row->addCell($colWidth, ['borderSize' => 0, 'borderColor' => 'FFFFFF'])
                ->addText($ttd->label ?? '', [
                    'size' => $this->fontSize, 'name' => $this->fontName,
                ], ['alignment' => Jc::CENTER, 'spaceAfter' => 0]);
        }

        // ── Gambar row ────────────────────────────
        $row = $table->addRow();
        foreach ($ttds as $i => $ttd) {
            // ✅ Gambar ke $cell bukan $this->section
            $cell     = $row->addCell($colWidth, ['borderSize' => 0, 'borderColor' => 'FFFFFF']);
            $ttdMedia = $ttd->getFirstMedia('ttd');
            $ttdPath  = $ttdMedia
                ? str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $ttdMedia->getPath())
                : null;

            // Cap hanya di index 0 (kolom Hormat Kami / PT BRM)
            $showCap = $capPath && ($i === 0);

            if ($showCap && $ttdPath && file_exists($capPath) && file_exists($ttdPath)) {
                $merged = $this->mergeCapTtd($capPath, $ttdPath);
                if ($merged) {
                    // ✅ Fix: $cell bukan $this->section
                    $this->safeAddImage($cell, $merged, [
                        'width' => 113, 'height' => 113, // 3cm × 3cm @ 96 DPI
                        'alignment' => Jc::CENTER, 'wrappingStyle' => 'inline',
                    ]);
                } else {
                    $this->safeAddImage($cell, $ttdPath, [
                        'width' => 90, 'height' => 90,
                        'alignment' => Jc::CENTER, 'wrappingStyle' => 'inline',
                    ]);
                }
            } elseif ($ttdPath && file_exists($ttdPath)) {
                $this->safeAddImage($cell, $ttdPath, [
                    'width' => 90, 'height' => 90,
                    'alignment' => Jc::CENTER, 'wrappingStyle' => 'inline',
                ]);
            } elseif ($showCap && file_exists($capPath)) {
                $this->safeAddImage($cell, $capPath, [
                    'width' => 100, 'height' => 100,
                    'alignment' => Jc::CENTER, 'wrappingStyle' => 'inline',
                ]);
            } else {
                // Spasi kosong (kolom non-BRM)
                for ($k = 0; $k < 4; $k++) {
                    $cell->addText('');
                }
            }
        }

        // ── Nama row ──────────────────────────────
        $row = $table->addRow();
        foreach ($ttds as $ttd) {
            $row->addCell($colWidth, ['borderSize' => 0, 'borderColor' => 'FFFFFF'])
                ->addText('(' . ($ttd->nama_penandatangan ?? '—') . ')', [
                    'bold' => true, 'size' => $this->fontSize, 'name' => $this->fontName,
                ], ['alignment' => Jc::CENTER]);
        }
    }

    // ══════════════════════════════════════════════
    // BAGIAN 2: LAMPIRAN A — JSA
    // ══════════════════════════════════════════════
    private function buildLampiranJSA(Surat $surat): void
    {
        // KOP lampiran
        $kopLampiran = public_path('assets/koplampiran.png');
        if (file_exists($kopLampiran)) {
            $this->section->addImage($kopLampiran, [
                'width' => 450, 'height' => 80,
                'alignment' => Jc::CENTER, 'wrappingStyle' => 'inline',
            ]);
        }
        $this->addSpacing();

        $this->section->addText('A. JSA', [
            'bold' => true, 'size' => 12, 'name' => $this->fontName,
        ], ['spaceAfter' => 80]);

        $meta          = $surat->meta ?? [];
        $jsaItems      = $meta['jsa']     ?? [];
        $pekerjaDaftar = $meta['pekerja'] ?? [];
        $pengawas      = collect($pekerjaDaftar)->firstWhere('role', 'Pengawas');
        $namaPengawas  = $pengawas['nama'] ?? ($surat->nama ?? '—');

        // Meta table JSA
        $this->addIdentitasTable([
            ['No JSA',           $surat->no_pekerja      ?? '—'],
            ['Nama Pekerjaan',   $surat->jenis_pekerjaan ?? '—'],
            ['Pengawas',         $namaPengawas],
            ['APD',              $surat->apd             ?? '—'],
            ['Lokasi Pekerjaan', $surat->lokasi_kerja    ?? '—'],
            ['Periode',          $surat->periode         ?? '—'],
        ], 1400);

        $this->addSpacing();

        // ── Tabel JSA dengan border ────────────────
        $this->buildJsaTable($jsaItems);

        $this->addSpacing();

        // ── Tabel TTD kosong (4 kolom) ────────────
        $this->buildTtdKosong();
    }

    private function buildJsaTable(array $jsaItems): void
    {
        $table = $this->section->addTable([
            'borderSize'  => 6,
            'borderColor' => '000000',
        ]);

        $cellBorder = [
            'borderTopSize'    => 6, 'borderTopColor'    => '000000',
            'borderBottomSize' => 6, 'borderBottomColor' => '000000',
            'borderLeftSize'   => 6, 'borderLeftColor'   => '000000',
            'borderRightSize'  => 6, 'borderRightColor'  => '000000',
        ];

        $headerFont = ['bold' => true, 'size' => 10, 'name' => $this->fontName];
        $headerBg   = array_merge($cellBorder, ['bgColor' => 'f0f0f0']);

        // Header: lebar dari DOCX asli — 400 | 1800 | 3413 | 3413
        $row = $table->addRow();
        foreach ([
            [400,  'NO'],
            [1800, 'Urutan Kerja'],
            [3413, 'Potensi Bahaya'],
            [3413, 'Upaya Pengendalian'],
        ] as [$w, $label]) {
            $row->addCell($w, $headerBg)
                ->addText($label, $headerFont, ['alignment' => Jc::CENTER]);
        }

        if (empty($jsaItems)) {
            $row = $table->addRow();
            $row->addCell(400,  $cellBorder)->addText('');
            $row->addCell(1800, $cellBorder)->addText('');
            $row->addCell(3413, $cellBorder)
                ->addText('Belum ada data JSA', [
                    'size' => 10, 'name' => $this->fontName, 'color' => '999999',
                ], ['alignment' => Jc::CENTER]);
            $row->addCell(3413, $cellBorder)->addText('');
        } else {
            foreach ($jsaItems as $idx => $item) {
                $row = $table->addRow();

                $row->addCell(400, $cellBorder)
                    ->addText(($idx + 1) . '.', [
                        'size' => 10, 'name' => $this->fontName,
                    ], ['alignment' => Jc::CENTER]);

                $row->addCell(1800, $cellBorder)
                    ->addText($item['urutan_kerja'] ?? '—', [
                        'size' => 10, 'name' => $this->fontName,
                    ]);

                $potensiCell = $row->addCell(3413, $cellBorder);
                $potensiLines = array_filter(explode("\n", trim($item['potensi_bahaya'] ?? '')));
                if (!empty($potensiLines)) {
                    foreach ($potensiLines as $p) {
                        $potensiCell->addText('; ' . trim($p), [
                            'size' => 10, 'name' => $this->fontName,
                        ], ['spaceAfter' => 0]);
                    }
                } else {
                    $potensiCell->addText('—', ['size' => 10, 'name' => $this->fontName]);
                }

                $upayaCell = $row->addCell(3413, $cellBorder);
                $upayaLines = array_filter(explode("\n", trim($item['upaya_pengendalian'] ?? '')));
                if (!empty($upayaLines)) {
                    foreach ($upayaLines as $u) {
                        $upayaCell->addText('; ' . trim($u), [
                            'size' => 10, 'name' => $this->fontName,
                        ], ['spaceAfter' => 0]);
                    }
                } else {
                    $upayaCell->addText('—', ['size' => 10, 'name' => $this->fontName]);
                }
            }
        }
    }

    private function buildTtdKosong(): void
    {
        $table = $this->section->addTable([
            'borderSize'  => 6,
            'borderColor' => '000000',
        ]);

        $cellBorder = [
            'borderTopSize'    => 6, 'borderTopColor'    => '000000',
            'borderBottomSize' => 6, 'borderBottomColor' => '000000',
            'borderLeftSize'   => 6, 'borderLeftColor'   => '000000',
            'borderRightSize'  => 6, 'borderRightColor'  => '000000',
        ];

        $colW       = 2256; // 9026 / 4 ≈ 2256 (dari DOCX asli)
        $headerFont = ['bold' => true, 'size' => 10, 'name' => $this->fontName];

        // Header
        $row = $table->addRow();
        foreach (['Disusun', 'Diperiksa', 'Disetujui 1', 'Disetujui 2'] as $label) {
            $row->addCell($colW, array_merge($cellBorder, ['bgColor' => 'f9f9f9']))
                ->addText($label, $headerFont, ['alignment' => Jc::CENTER]);
        }

        // Baris kosong untuk tanda tangan
        $row = $table->addRow(1440);
        for ($i = 0; $i < 4; $i++) {
            $row->addCell($colW, $cellBorder)->addText('');
        }
    }

    // ══════════════════════════════════════════════
    // BAGIAN 3: LAMPIRAN B — DAFTAR PEKERJA
    // ══════════════════════════════════════════════
    private function buildLampiranPekerja(Surat $surat): void
    {
        $kopLampiran = public_path('assets/koplampiran.png');
        if (file_exists($kopLampiran)) {
            $this->section->addImage($kopLampiran, [
                'width' => 450, 'height' => 80,
                'alignment' => Jc::CENTER, 'wrappingStyle' => 'inline',
            ]);
        }
        $this->addSpacing();

        $this->section->addText('B. Daftar Pekerja', [
            'bold' => true, 'size' => 12, 'name' => $this->fontName,
        ], ['spaceAfter' => 80]);

        $meta          = $surat->meta ?? [];
        $pekerjaDaftar = $meta['pekerja'] ?? [];
        $pengawas      = collect($pekerjaDaftar)->firstWhere('role', 'Pengawas');
        $namaPengawas  = $pengawas['nama'] ?? ($surat->nama ?? '—');

        $this->addIdentitasTable([
            ['No Daftar Pekerja', $surat->no_pekerja      ?? '—'],
            ['Nama Pekerjaan',    $surat->jenis_pekerjaan ?? '—'],
            ['Pengawas',          $namaPengawas],
            ['Lokasi Pekerjaan',  $surat->lokasi_kerja    ?? '—'],
            ['Periode',           $surat->periode         ?? '—'],
        ], 1600);

        $this->addSpacing();

        $this->buildPekerjaTable($surat, $pekerjaDaftar);
    }

    private function buildPekerjaTable(Surat $surat, array $pekerjaDaftar): void
    {
        $table = $this->section->addTable([
            'borderSize'  => 6,
            'borderColor' => '000000',
        ]);

        $cellBorder = [
            'borderTopSize'    => 6, 'borderTopColor'    => '000000',
            'borderBottomSize' => 6, 'borderBottomColor' => '000000',
            'borderLeftSize'   => 6, 'borderLeftColor'   => '000000',
            'borderRightSize'  => 6, 'borderRightColor'  => '000000',
        ];

        $headerFont = ['bold' => true, 'size' => 10, 'name' => $this->fontName];
        $headerBg   = array_merge($cellBorder, ['bgColor' => 'f0f0f0']);

        // Header: lebar dari DOCX asli — 400 | 2706 | 5920
        $row = $table->addRow();
        foreach ([
            [400,  'NO'],
            [2706, 'NAMA PEKERJA'],
            [5920, 'ID CARD'],
        ] as [$w, $label]) {
            $row->addCell($w, $headerBg)
                ->addText($label, $headerFont, ['alignment' => Jc::CENTER]);
        }

        $ktpMediaList = $surat->getMedia('ktp');

        if (empty($pekerjaDaftar)) {
            $row = $table->addRow();
            $row->addCell(400,  $cellBorder)->addText('');
            $row->addCell(2706, $cellBorder)->addText('Belum ada data pekerja', [
                'size' => 10, 'name' => $this->fontName, 'color' => '999999',
            ]);
            $row->addCell(5920, $cellBorder)->addText('');
        } else {
            foreach ($pekerjaDaftar as $idx => $pekerja) {
                $row = $table->addRow(2160);

                $row->addCell(400, $cellBorder)
                    ->addText((string)($idx + 1), [
                        'bold' => true, 'size' => 10, 'name' => $this->fontName,
                    ], ['alignment' => Jc::CENTER]);

                $namaCell = $row->addCell(2706, $cellBorder);
                $namaText = ($pekerja['nama'] ?? '—');
                if (!empty($pekerja['role'])) {
                    $namaText .= ' (' . $pekerja['role'] . ')';
                }
                $namaCell->addText($namaText, [
                    'bold' => true, 'size' => 10, 'name' => $this->fontName,
                ]);

                $ktpCell  = $row->addCell(5920, $cellBorder);
                $ktpMedia = $ktpMediaList->get($idx);

                if ($ktpMedia) {
                    $ktpPath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $ktpMedia->getPath());
                    if (file_exists($ktpPath) && filesize($ktpPath) > 0) {
                        try {
                            $ktpCell->addImage($ktpPath, [
                                'width'         => 300,
                                'height'        => 150,
                                'alignment'     => Jc::CENTER,
                                'wrappingStyle' => 'inline',
                            ]);
                        } catch (\Exception $e) {
                            $ktpCell->addText('(Foto tidak dapat ditampilkan)', [
                                'size' => 9, 'name' => $this->fontName, 'color' => '999999',
                            ], ['alignment' => Jc::CENTER]);
                        }
                    } else {
                        $ktpCell->addText('—', [
                            'size' => 10, 'name' => $this->fontName,
                        ], ['alignment' => Jc::CENTER]);
                    }
                } else {
                    $ktpCell->addText('', ['size' => 10, 'name' => $this->fontName]);
                }
            }
        }
    }
}