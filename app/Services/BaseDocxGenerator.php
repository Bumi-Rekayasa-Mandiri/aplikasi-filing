<?php

namespace App\Services;

use App\Models\Surat;
use Carbon\Carbon;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Settings;
use PhpOffice\PhpWord\Shared\Converter;
use PhpOffice\PhpWord\SimpleType\Jc;

abstract class BaseDocxGenerator
{
    protected PhpWord $phpWord;
    protected $section;
    protected string $fontName = 'Times New Roman';
    protected int    $fontSize  = 12;

    /**
     * ✅ Track semua temp file yang dibuat selama generate
     * Akan di-cleanup SETELAH save() berhasil
     */
    protected array $tempFiles = [];

    abstract public function handle(Surat $surat): string;

    protected function setup(): void
    {
        Settings::setOutputEscapingEnabled(true);

        $this->phpWord = new PhpWord();
        $this->phpWord->setDefaultFontName($this->fontName);
        $this->phpWord->setDefaultFontSize($this->fontSize);

        $this->section = $this->phpWord->addSection([
            'paperSize'    => 'A4',
            'marginTop'    => Converter::cmToTwip(1.27),
            'marginRight'  => Converter::cmToTwip(2.3),
            'marginBottom' => Converter::cmToTwip(2.3),
            'marginLeft'   => Converter::cmToTwip(2.3),
        ]);

        $this->tempFiles = [];
    }

    protected function addNewSection(): void
    {
        $this->section = $this->phpWord->addSection([
            'paperSize'    => 'A4',
            'marginTop'    => Converter::cmToTwip(1.27),
            'marginRight'  => Converter::cmToTwip(2.3),
            'marginBottom' => Converter::cmToTwip(2.3),
            'marginLeft'   => Converter::cmToTwip(2.3),
        ]);
    }

    /**
     * ✅ Helper untuk add image yang aman — track temp files
     * Gunakan ini di semua generator agar konsisten
     */
    protected function safeAddImage($container, ?string $path, array $style): bool
    {
        if (!$path || !file_exists($path) || filesize($path) === 0) {
            return false;
        }

        try {
            $container->addImage($path, $style);
            return true;
        } catch (\Throwable $e) {
            \Log::warning('Failed to add image to docx: ' . $e->getMessage(), [
                'path' => $path,
            ]);
            return false;
        }
    }

    /**
     * ✅ Merge cap + TTD dengan caching yang stabil
     * File temp di-track, baru dihapus setelah save()
     */
    protected function mergeCapTtd(string $capPath, string $ttdPath): ?string
    {
        try {
            $capContent = @file_get_contents($capPath);
            $ttdContent = @file_get_contents($ttdPath);

            if (!$capContent || !$ttdContent) return null;

            $cap = @imagecreatefromstring($capContent);
            $ttd = @imagecreatefromstring($ttdContent);

            if (!$cap || !$ttd) {
                if ($cap) imagedestroy($cap);
                if ($ttd) imagedestroy($ttd);
                return null;
            }

            // ✅ Resize ke ukuran konsisten untuk hindari overflow
            $maxW = 400;
            $capW = imagesx($cap);
            $capH = imagesy($cap);
            $ttdW = imagesx($ttd);
            $ttdH = imagesy($ttd);

            $w = max($capW, $ttdW);
            $h = max($capH, $ttdH);

            // Resize jika terlalu besar
            if ($w > $maxW) {
                $ratio = $maxW / $w;
                $w = $maxW;
                $h = (int)($h * $ratio);
            }

            $merged = imagecreatetruecolor($w, $h);
            imagealphablending($merged, false);
            imagesavealpha($merged, true);
            $transparent = imagecolorallocatealpha($merged, 255, 255, 255, 127);
            imagefilledrectangle($merged, 0, 0, $w, $h, $transparent);
            imagealphablending($merged, true);

            // Tempel cap (di-resample agar pas)
            imagecopyresampled($merged, $cap, 0, 0, 0, 0, $w, $h, $capW, $capH);

            // Tempel TTD di atas (juga di-resample)
            imagecopyresampled($merged, $ttd, 0, 0, 0, 0, $w, $h, $ttdW, $ttdH);

            // ✅ Simpan ke folder temp khusus dengan nama unik
            $dir = storage_path('app/temp/docx-merge');
            if (!is_dir($dir)) mkdir($dir, 0775, true);

            $tmpPath = $dir . '/merged_' . uniqid('', true) . '.png';
            imagepng($merged, $tmpPath);

            imagedestroy($cap);
            imagedestroy($ttd);
            imagedestroy($merged);

            // ✅ Track untuk cleanup nanti, JANGAN unlink di sini
            $this->tempFiles[] = $tmpPath;

            return $tmpPath;
        } catch (\Throwable $e) {
            \Log::warning('mergeCapTtd failed: ' . $e->getMessage());
            return null;
        }
    }

    protected function addKop(): void
    {
        $kopPath = public_path('assets/kop.jpeg');
        if (!file_exists($kopPath)) $kopPath = public_path('assets/kop.png');

        $this->safeAddImage($this->section, $kopPath, [
            'width'         => 450,
            'height'        => 100,
            'alignment'     => Jc::CENTER,
            'wrappingStyle' => 'inline',
        ]);
    }

    protected function addParagraf(string $text, bool $justify = true): void
    {
        $this->section->addText($text,
            ['size' => $this->fontSize, 'name' => $this->fontName],
            ['alignment' => $justify ? Jc::BOTH : Jc::LEFT, 'spaceBefore' => 80, 'spaceAfter' => 80]
        );
    }

    protected function addSpacing(int $n = 1): void
    {
        for ($i = 0; $i < $n; $i++) {
            $this->section->addTextRun(['spaceAfter' => 0]);
        }
    }

    protected function addIdentitasTable(array $rows, int $labelWidth = 2400): void
    {
        $style = ['borderSize' => 0, 'borderColor' => 'FFFFFF'];
        $table = $this->section->addTable($style);

        foreach ($rows as [$label, $value]) {
            $row = $table->addRow();
            $row->addCell($labelWidth, ['borderSize' => 0, 'borderColor' => 'FFFFFF'])
                ->addText($label, ['bold' => true, 'size' => $this->fontSize, 'name' => $this->fontName]);
            $row->addCell(200, ['borderSize' => 0, 'borderColor' => 'FFFFFF'])
                ->addText(':', ['size' => $this->fontSize, 'name' => $this->fontName]);
            $row->addCell(9026 - $labelWidth - 200, ['borderSize' => 0, 'borderColor' => 'FFFFFF'])
                ->addText($value ?? '—', ['size' => $this->fontSize, 'name' => $this->fontName]);
        }
    }

    protected function addNomorTanggal(Surat $surat): void
    {
        $tgl   = Carbon::parse($surat->tanggal_surat)->locale('id')->translatedFormat('d F Y');
        $style = ['borderSize' => 0, 'borderColor' => 'FFFFFF'];
        $table = $this->section->addTable($style);
        $row   = $table->addRow();

        $row->addCell(4513, ['borderSize' => 0, 'borderColor' => 'FFFFFF'])
            ->addText("Nomor : " . ($surat->nomor_surat ?? '—'),
                ['size' => $this->fontSize, 'name' => $this->fontName]);

        $row->addCell(4513, ['borderSize' => 0, 'borderColor' => 'FFFFFF'])
            ->addText("Karawang, {$tgl}",
                ['size' => $this->fontSize, 'name' => $this->fontName],
                ['alignment' => Jc::RIGHT]);

        $this->addSpacing();
    }

    protected function addTandaTangan(Surat $surat, bool $rataKanan = false): void
    {
        $ttd     = $surat->ttds->first();
        $nama    = $ttd?->nama_penandatangan ?? 'Ilman Sunaryo';
        $jabatan = $ttd?->jabatan ?? 'Direktur';
        $label   = $ttd?->label ?? 'Hormat Kami,';

        $capMedia = $surat->getFirstMedia('cap');
        $capPath  = $capMedia ? str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $capMedia->getPath()) : null;
        $ttdMedia = $ttd?->getFirstMedia('ttd');
        $ttdPath  = $ttdMedia ? str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $ttdMedia->getPath()) : null;

        $style = ['borderSize' => 0, 'borderColor' => 'FFFFFF'];
        $table = $this->section->addTable($style);
        $align = $rataKanan ? Jc::RIGHT : Jc::CENTER;

        // Label
        $row = $table->addRow();
        $row->addCell(4513, ['borderSize' => 0, 'borderColor' => 'FFFFFF'])->addText('');
        $row->addCell(4513, ['borderSize' => 0, 'borderColor' => 'FFFFFF'])
            ->addText($label, ['size' => $this->fontSize, 'name' => $this->fontName], ['alignment' => $align]);

        // Gambar
        $row = $table->addRow();
        $row->addCell(4513, ['borderSize' => 0, 'borderColor' => 'FFFFFF'])->addText('');
        $imgCell = $row->addCell(4513, ['borderSize' => 0, 'borderColor' => 'FFFFFF']);

        $imgAdded = false;
        if ($capPath && $ttdPath && file_exists($capPath) && file_exists($ttdPath)) {
            $merged = $this->mergeCapTtd($capPath, $ttdPath);
            if ($merged) {
                $imgAdded = $this->safeAddImage($imgCell, $merged, [
                    'width' => 110, 'height' => 110, 'alignment' => $align, 'wrappingStyle' => 'inline',
                ]);
            }
        }

        if (!$imgAdded && $ttdPath) {
            $imgAdded = $this->safeAddImage($imgCell, $ttdPath, [
                'width' => 80, 'height' => 80, 'alignment' => $align, 'wrappingStyle' => 'inline',
            ]);
        }
        if (!$imgAdded && $capPath) {
            $imgAdded = $this->safeAddImage($imgCell, $capPath, [
                'width' => 100, 'height' => 100, 'alignment' => $align, 'wrappingStyle' => 'inline',
            ]);
        }
        if (!$imgAdded) {
            $imgCell->addText('');
        }

        // Nama
        $row = $table->addRow();
        $row->addCell(4513, ['borderSize' => 0, 'borderColor' => 'FFFFFF'])->addText('');
        $row->addCell(4513, ['borderSize' => 0, 'borderColor' => 'FFFFFF'])
            ->addText($nama, ['bold' => true, 'size' => $this->fontSize, 'name' => $this->fontName, 'underline' => 'single'], ['alignment' => $align]);

        // Jabatan
        $row = $table->addRow();
        $row->addCell(4513, ['borderSize' => 0, 'borderColor' => 'FFFFFF'])->addText('');
        $row->addCell(4513, ['borderSize' => 0, 'borderColor' => 'FFFFFF'])
            ->addText($jabatan, ['size' => $this->fontSize, 'name' => $this->fontName], ['alignment' => $align]);
    }

    /**
     * ✅ Save dengan auto-cleanup temp files SETELAH save berhasil
     */
    protected function save(string $prefix, int $id): string
    {
        $dir = storage_path('app/public/docx');
        if (!is_dir($dir)) mkdir($dir, 0775, true);

        $path = "{$dir}/{$prefix}_{$id}_" . now()->format('YmdHis') . '.docx';

        try {
            IOFactory::createWriter($this->phpWord, 'Word2007')->save($path);
        } finally {
            // ✅ Cleanup SEMUA temp files setelah save selesai (atau gagal)
            $this->cleanupTempFiles();
        }

        return $path;
    }

    protected function cleanupTempFiles(): void
    {
        foreach ($this->tempFiles as $tmpFile) {
            if (file_exists($tmpFile)) {
                @unlink($tmpFile);
            }
        }
        $this->tempFiles = [];
    }
}