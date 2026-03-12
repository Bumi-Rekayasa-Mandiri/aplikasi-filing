<?php

namespace App\Services;

use App\Models\Surat;
use Carbon\Carbon;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Shared\Converter;
use PhpOffice\PhpWord\Style\Table as TableStyle;
use PhpOffice\PhpWord\SimpleType\Jc;

class GenerateSKPDocx
{
    private PhpWord $phpWord;
    private $section;
    private $fontName = 'Times New Roman';
    private $fontSize  = 12;

    public function handle(Surat $surat): string
    {
        $this->phpWord = new PhpWord();
        $this->phpWord->setDefaultFontName($this->fontName);
        $this->phpWord->setDefaultFontSize($this->fontSize);

        // ── Section / Page Setup (A4) ──────────────────────────────────────
        $this->section = $this->phpWord->addSection([
            'paperSize'   => 'A4',
            'marginTop'   => Converter::cmToTwip(1.27),  // 720 twip
            'marginRight' => Converter::cmToTwip(2.3),  // 1440 twip
            'marginBottom'=> Converter::cmToTwip(2.3),
            'marginLeft'  => Converter::cmToTwip(2.3),
        ]);

        // ── Build Sections ─────────────────────────────────────────────────
        $this->addKop();
        $this->addJudul();
        $this->addNomorTanggal($surat);
        $this->addParagraf('Yang bertanda tangan di bawah ini:');
        $this->addIdentitasPenandatangan($surat);
        $this->addParagraf('Dengan ini menerangkan bahwa:');
        $this->addIdentitasKaryawan($surat);
        $this->addIsiSurat($surat);
        $this->addPenutup();
        $this->addTandaTangan($surat);

        // ── Save ke storage ────────────────────────────────────────────────
        $filename = 'skp_' . $surat->id . '_' . now()->format('YmdHis') . '.docx';
        $path     = storage_path('app/public/docx/' . $filename);

        if (!file_exists(storage_path('app/public/docx'))) {
            mkdir(storage_path('app/public/docx'), 0775, true);
        }

        $writer = IOFactory::createWriter($this->phpWord, 'Word2007');
        $writer->save($path);

        return $path;
    }

    // Merge cap + TTD dengan GD
    private function mergeCapTtd(string $capPath, string $ttdPath): string
    {
        $cap = imagecreatefromstring(file_get_contents($capPath));
        $ttd = imagecreatefromstring(file_get_contents($ttdPath));

        $w = max(imagesx($cap), imagesx($ttd));
        $h = max(imagesy($cap), imagesy($ttd));

        $merged = imagecreatetruecolor($w, $h);

        // Background transparan
        imagealphablending($merged, false);
        imagesavealpha($merged, true);
        $transparent = imagecolorallocatealpha($merged, 0, 0, 0, 127);
        imagefill($merged, 0, 0, $transparent);

        // Tempel cap dulu lalu TTD di atasnya
        imagealphablending($merged, true);
        imagecopy($merged, $cap, 0, 0, 0, 0, imagesx($cap), imagesy($cap));
        imagecopy($merged, $ttd, 0, 0, 0, 0, imagesx($ttd), imagesy($ttd));

        $tmpPath = storage_path('app/tmp_merged_' . uniqid() . '.png');
        imagepng($merged, $tmpPath);

        imagedestroy($cap);
        imagedestroy($ttd);
        imagedestroy($merged);

        return $tmpPath;
    }

    private function addKop(): void
    {
        // ── Gambar KOP ──────────────────────────────────────────
        $kopPath = public_path('assets/kop.jpeg');

        if (file_exists($kopPath)) {
            $this->section->addImage($kopPath, [
                'width'            => 450,   // sesuaikan lebar (px)
                'height'           => 100,   // sesuaikan tinggi (px)
                'alignment'        => Jc::CENTER,
                'marginTop'        => 0,
                'marginLeft'       => 0,
                'wrappingStyle'    => 'inline',
            ]);
        } else {
            // Fallback teks jika gambar tidak ditemukan
            $p = $this->section->addTextRun(['alignment' => Jc::CENTER, 'spaceAfter' => 0]);
            $p->addText('PT. BUMI REKAYASA MANDIRI', [
                'bold' => true, 'size' => 18, 'name' => $this->fontName,
            ]);

            $p = $this->section->addTextRun(['alignment' => Jc::CENTER, 'spaceAfter' => 0]);
            $p->addText('Precision Building. Sustainable Value.', [
                'size' => 11, 'name' => $this->fontName, 'italic' => true,
            ]);

            $p = $this->section->addTextRun([
                'alignment'         => Jc::CENTER,
                'spaceAfter'        => 20,
                'borderBottomSize'  => 6,
                'borderBottomColor' => '000000',
            ]);
            $p->addText(
                'Ruko Dharmawangsa Grand Taruma Blok D8/DC Telukjambe Karawang No. HP : 08568708992',
                ['bold' => true, 'size' => 9, 'name' => $this->fontName]
            );

            $p = $this->section->addTextRun(['alignment' => Jc::CENTER, 'spaceAfter' => 100]);
            $p->addText(
                'e-mail : bumirekayasa.mandiri@gmail.com  Phone : 0267-8639-837 / Fax: 0267-8639-837',
                ['size' => 10, 'name' => $this->fontName]
            );
        }
    }

    // ── JUDUL ──────────────────────────────────────────────────────────────
    private function addJudul(): void
    {
        $p = $this->section->addTextRun([
            'alignment'   => Jc::CENTER,
            'spaceBefore' => 100,
            'spaceAfter'  => 100,
        ]);
        $p->addText('SURAT PEMBERITAHUAN', [
            'bold'      => true,
            'size'      => 14,
            'name'      => $this->fontName,
            'underline' => 'single',
        ]);
    }

    // ── NOMOR & TANGGAL ────────────────────────────────────────────────────
    private function addNomorTanggal(Surat $surat): void
    {
        $tanggal = Carbon::parse($surat->tanggal_surat)->locale('id')->translatedFormat('d F Y');

        $tableStyle = ['borderSize' => 0, 'borderColor' => 'FFFFFF', 'cellMargin' => 0];
        $table = $this->section->addTable($tableStyle);
        $table->addRow();

        $table->addCell(4513, ['borderSize' => 0, 'borderColor' => 'FFFFFF'])
            ->addText("Nomor : {$surat->nomor_surat}", ['size' => $this->fontSize, 'name' => $this->fontName]);

        $table->addCell(4513, ['borderSize' => 0, 'borderColor' => 'FFFFFF'])
            ->addText("Karawang, {$tanggal}", ['size' => $this->fontSize, 'name' => $this->fontName], ['alignment' => Jc::RIGHT]);

        $this->addSpacing();
    }

    // ── IDENTITAS PENANDATANGAN ────────────────────────────────────────────
    private function addIdentitasPenandatangan(Surat $surat): void
    {
        // ✅ Ambil dari TTD pertama jika ada
        $ttd = $surat->ttds->first();

        $rows = [
            ['Nama',       $ttd?->nama_penandatangan ?? 'Ilman Sunaryo'],
            ['Jabatan',    $ttd?->jabatan ?? 'Direktur'],
            ['Perusahaan', 'PT. Bumi Rekayasa Mandiri'],
            ['Alamat',     'Ruko Grand Taruma Blok D8/DC, Telukjambe Timur, Karawang, Indonesia'],
        ];

        $this->addIdentitasTable($rows);
        $this->addSpacing();
    }

    // ── IDENTITAS KARYAWAN ─────────────────────────────────────────────────
    private function addIdentitasKaryawan(Surat $surat): void
    {
        $rows = [
            ['Nama',               $surat->nama],
            ['Jabatan Terakhir',   $surat->jabatan_terakhir],
            ['Departemen / Bagian', $surat->departemen],
        ];

        $this->addIdentitasTable($rows);
        $this->addSpacing();
    }

    // ── ISI SURAT ──────────────────────────────────────────────────────────
    private function addIsiSurat(Surat $surat): void
    {
        $tanggal = Carbon::parse($surat->tanggal_surat)->locale('id')->translatedFormat('d F Y');

        // Paragraf 1 — dengan bold sebagian
        $p = $this->section->addTextRun([
            'alignment'   => Jc::BOTH,
            'spaceBefore' => 80,
            'spaceAfter'  => 80,
        ]);
        $p->addText('Telah ', ['size' => $this->fontSize, 'name' => $this->fontName]);
        $p->addText(
            "tidak lagi bekerja di PT. Bumi Rekayasa Mandiri terhitung sejak tanggal {$tanggal}",
            ['bold' => true, 'size' => $this->fontSize, 'name' => $this->fontName]
        );
        $p->addText(
            ", dengan alasan {$surat->isi_surat}.",
            ['size' => $this->fontSize, 'name' => $this->fontName]
        );

        // Paragraf 2
        $this->addParagraf(
            'Segala tindakan yang dilakukan setelah berakhirnya hubungan kerja sepenuhnya menjadi tanggung jawab pribadi yang bersangkutan dan tidak menjadi tanggung jawab perusahaan.'
        );

        // Paragraf 3
        $kontak = $surat->nama_penandatangan ?? 'Ilman Sunaryo';
        $telp   = $surat->no_pekerja ?? '+62811964060';
        $this->addParagraf(
            "Apabila terdapat hal-hal yang perlu dikonfirmasi atau terkait dengan pekerjaan yang bersangkutan, dapat menghubungi {$kontak} di nomor {$telp} atau email berikut:"
        );

        // Bullet email
        $listStyle = ['listType' => \PhpOffice\PhpWord\Style\ListItem::TYPE_BULLET_FILLED];
        $this->section->addListItem('bumirekayasa.mandiri@gmail.com', 0, ['size' => $this->fontSize, 'name' => $this->fontName], $listStyle);
        $this->section->addListItem('info@bumirekayasamandiri.co.id', 0, ['size' => $this->fontSize, 'name' => $this->fontName], $listStyle);
        $this->section->addListItem('info@bumirekamandiri.id',        0, ['size' => $this->fontSize, 'name' => $this->fontName], $listStyle);
    }

    // ── PENUTUP ────────────────────────────────────────────────────────────
    private function addPenutup(): void
    {
        $this->addSpacing();
        $this->addParagraf(
            'Demikian surat pemberitahuan ini dibuat dengan sebenar-benarnya. Atas perhatian dan kerja samanya, kami ucapkan terima kasih.'
        );
        $this->addSpacing();
    }

    // ── TANDA TANGAN ──────────────────────────────────────────────────────
   private function addTandaTangan(Surat $surat): void
    {
        $tanggal = Carbon::parse($surat->tanggal_surat)->locale('id')->translatedFormat('d F Y');
        $ttd     = $surat->ttds->first();
        $nama    = $ttd?->nama_penandatangan ?? $surat->nama_penandatangan ?? 'Ilman Sunaryo';
        $jabatan = $ttd?->jabatan ?? $surat->jabatan ?? 'Direktur';

        // ── Path file cap & TTD ──────────────────────────────────
        $capMedia  = $surat->getFirstMedia('cap');
        $capPath   = $capMedia ? str_replace('\\', '/', $capMedia->getPath()) : null;

        $ttdMedia  = $ttd?->getFirstMedia('ttd');
        $ttdPath   = $ttdMedia ? str_replace('\\', '/', $ttdMedia->getPath()) : null;

        $tableStyle = ['borderSize' => 0, 'borderColor' => 'FFFFFF', 'cellMargin' => 0];
        $table = $this->section->addTable($tableStyle);

        // ── Baris tanggal ────────────────────────────────────────
        $table->addRow();
        $table->addCell(4513, ['borderSize' => 0, 'borderColor' => 'FFFFFF'])->addText('');
        $table->addCell(4513, ['borderSize' => 0, 'borderColor' => 'FFFFFF'])
            ->addText("Karawang, {$tanggal}",
                ['size' => $this->fontSize, 'name' => $this->fontName],
                ['alignment' => Jc::CENTER]
            );

        // ── Baris label ──────────────────────────────────────────
        $table->addRow();
        $table->addCell(4513, ['borderSize' => 0, 'borderColor' => 'FFFFFF'])->addText('');
        $table->addCell(4513, ['borderSize' => 0, 'borderColor' => 'FFFFFF'])
            ->addText('Hormat Kami,',
                ['size' => $this->fontSize, 'name' => $this->fontName],
                ['alignment' => Jc::CENTER]
            );

        // ── Baris gambar cap + TTD ───────────────────────────────
        $table->addRow();
        $table->addCell(4513, ['borderSize' => 0, 'borderColor' => 'FFFFFF'])->addText('');

        $ttdCell = $table->addCell(4513, ['borderSize' => 0, 'borderColor' => 'FFFFFF']);

        if ($capPath && $ttdPath && file_exists($capPath) && file_exists($ttdPath)) {
            $mergedPath = $this->mergeCapTtd($capPath, $ttdPath);
            $ttdCell->addImage($mergedPath, [
                'width'         => 110,
                'height'        => 110,
                'alignment'     => Jc::CENTER,
                'wrappingStyle' => 'inline',
            ]);
            unlink($mergedPath);
        } elseif ($ttdPath && file_exists($ttdPath)) {
            $ttdCell->addImage($ttdPath, [
                'width'         => 80,
                'height'        => 80,
                'alignment'     => Jc::CENTER,
                'wrappingStyle' => 'inline',
            ]);
        } elseif ($capPath && file_exists($capPath)) {
            $ttdCell->addImage($capPath, [
                'width'         => 100,
                'height'        => 100,
                'alignment'     => Jc::CENTER,
                'wrappingStyle' => 'inline',
            ]);
        } else {
            // Padding jika tidak ada gambar sama sekali
            for ($i = 0; $i < 4; $i++) {
                $table->addRow();
                $table->addCell(4513, ['borderSize' => 0, 'borderColor' => 'FFFFFF'])->addText('');
                $table->addCell(4513, ['borderSize' => 0, 'borderColor' => 'FFFFFF'])->addText('');
            }
        }

        // ── Nama penandatangan ───────────────────────────────────
        $table->addRow();
        $table->addCell(4513, ['borderSize' => 0, 'borderColor' => 'FFFFFF'])->addText('');
        $table->addCell(4513, ['borderSize' => 0, 'borderColor' => 'FFFFFF'])
            ->addText($nama, [
                'bold'      => true,
                'size'      => $this->fontSize,
                'name'      => $this->fontName,
                'underline' => 'single',
            ], ['alignment' => Jc::CENTER]);

        // ── Jabatan ──────────────────────────────────────────────
        $table->addRow();
        $table->addCell(4513, ['borderSize' => 0, 'borderColor' => 'FFFFFF'])->addText('');
        $table->addCell(4513, ['borderSize' => 0, 'borderColor' => 'FFFFFF'])
            ->addText($jabatan,
                ['size' => $this->fontSize, 'name' => $this->fontName],
                ['alignment' => Jc::CENTER]
            );
    }

    // ── HELPERS ────────────────────────────────────────────────────────────

    private function addIdentitasTable(array $rows): void
    {
        $tableStyle = ['borderSize' => 0, 'borderColor' => 'FFFFFF', 'cellMargin' => 0];
        $table = $this->section->addTable($tableStyle);

        foreach ($rows as [$label, $value]) {
            $table->addRow();
            $table->addCell(2400, ['borderSize' => 0, 'borderColor' => 'FFFFFF'])
                ->addText($label, ['bold' => true, 'size' => $this->fontSize, 'name' => $this->fontName]);
            $table->addCell(200, ['borderSize' => 0, 'borderColor' => 'FFFFFF'])
                ->addText(':', ['size' => $this->fontSize, 'name' => $this->fontName]);
            $table->addCell(6426, ['borderSize' => 0, 'borderColor' => 'FFFFFF'])
                ->addText($value ?? '-', ['size' => $this->fontSize, 'name' => $this->fontName]);
        }
    }

    private function addParagraf(string $text): void
    {
        $this->section->addText($text, [
            'size' => $this->fontSize,
            'name' => $this->fontName,
        ], [
            'alignment'   => Jc::BOTH,
            'spaceBefore' => 80,
            'spaceAfter'  => 80,
        ]);
    }

    private function addSpacing(int $lines = 1): void
    {
        for ($i = 0; $i < $lines; $i++) {
            $this->section->addTextRun(['spaceAfter' => 0]);
        }
    }
}
