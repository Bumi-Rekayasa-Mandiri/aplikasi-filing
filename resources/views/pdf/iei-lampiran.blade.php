<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Lampiran - {{ $surat->nomor_surat }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11pt;
            line-height: 1.5;
            margin: 5mm 15mm 10mm 15mm;
        }

        @page { margin-top: 42mm; }

        header {
            position: fixed;
            top: -37mm;
            left: 0;
            right: 0;
            width: 100%;
        }

        header img { width: 100%; height: auto; display: block; }

        .page-break { page-break-after: always; }

        .content { text-align: justify; margin-bottom: 12px; }

        .content1 { text-align: left; margin-top: 50px }

        .project-title {
            text-align: center;
            font-weight: bold;
            font-size: 12pt;
            margin: 24px 0 4px 0;
        }

        .project-subtitle {
            text-align: center;
            font-size: 11pt;
            margin-bottom: 20px;
        }

        ol.doc-list { margin: 10px 0 16px 0; }
        ol.doc-list li { margin-bottom: 4px; }

        /* TTD */
        table.ttd-wrapper {
            width: 100%;
            border-collapse: collapse;
            margin-top: 0px;
        }

        table.ttd-wrapper td {
            width: 50%;
            vertical-align: top;
            padding: 0 10px 0 0;
            text-align: center;  /* ✅ center align */
        }

        .ttd-img-wrap {
            position: relative;
            width: 140px;
            height: 80px;
            margin: 6px auto;  /* ✅ auto untuk center */
        }

        .ttd-name {
            font-weight: bold;
            text-decoration: underline;
            text-align: center;  /* ✅ center */
        }

        .ttd-jabatan {
            font-size: 10pt;
            text-align: center;  /* ✅ center */
        }
    </style>
</head>
<body>

    {{-- Kop repeat setiap halaman --}}
    <header>
        @if($kopLampiranBase64)
        <img src="{{ $kopLampiranBase64 }}" />
        @endif
    </header>

    {{-- ══════════════════════════════════════ --}}
    {{-- HALAMAN 1: GUARANTEE LETTER            --}}
    {{-- ══════════════════════════════════════ --}}

    @php
        $ttds        = $surat->ttds;
        $ttdKiri     = $ttds->get(0);   // PT BRM
        $ttdKanan    = $ttds->get(1);   // Penerima
        $ttdImgKiri  = $ttdBase64List[0] ?? null;
        $ttdImgKanan = $ttdBase64List[1] ?? null;
        $showCap     = $capBase64 && $ttdKiri;

        $tglInggris = \Carbon\Carbon::parse($surat->tanggal_surat)
            ->locale('en')->translatedFormat('M, d Y');

        $tglHandingOver = \Carbon\Carbon::parse($surat->tanggal_surat)
            ->locale('en')->translatedFormat('M jS Y');
    @endphp

    {{-- Penerima + Tanggal --}}
    <table style="width:100%; border-collapse:collapse; margin-bottom:20px;">
        <tr>
            <td style="width:60%; vertical-align:top; padding:0;">
                <strong>{{ $surat->tujuan ?? '—' }}</strong><br>
                @if($ttdKanan)
                Attn : {{ $ttdKanan->nama_penandatangan }}
                @endif
            </td>
            <td style="width:40%; text-align:right; vertical-align:top; padding:0;">
                {{ $tglInggris }}
            </td>
        </tr>
    </table>

    <div class="content">Dear Sirs,</div>

    {{-- Judul project --}}
    <div class="project-title">{{ strtoupper($surat->project ?? $surat->judul ?? '—') }}</div>
    <div class="project-subtitle">
        Official Handing Over on {{ $tglHandingOver }}
    </div>

    {{-- Paragraf 1 --}}
    <div class="content">
        We refer to above mention and would like take this opportunity to thank you for your
        full co-operation and support in the achievement of the completed project and the
        official handing over on {{ $tglHandingOver }}.
    </div>

    {{-- Paragraf 2 --}}
    <div class="content">
        We have the pleasure to submit below document for your perusal and acceptance.
    </div>

    {{-- ✅ Daftar dokumen dari meta --}}
    @if(!empty($dokumenList))
    <ol class="doc-list">
        @foreach($dokumenList as $dok)
        <li>{{ $dok }}</li>
        @endforeach
    </ol>
    @endif

    {{-- Penutup --}}
    <div class="content">
        Henceforth, once again, thank you for your precious time to be with me at the
        handing over. thank you.
    </div>

    <div class="content1">Your faithfully</div>
    {{-- TTD: 2 kolom, center align --}}
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                {{-- ── Kiri: PT BRM ── --}}
                <td style="width: 50%; vertical-align: top; padding: 0 10px 0 0; text-align: left;">

                    {{-- Label kiri — align left --}}

                    <div style="text-align: left;">PT. BUMI REKAYASA MANDIRI</div>

                    {{-- Gambar TTD + Cap kiri --}}
                    <table style="border-collapse: collapse; width: 140px; height: 80px; margin: 6px 0;">
                        <tr>
                            <td style="position: relative; padding: 0; width: 140px; height: 80px;">
                                @if($showCap && $capBase64)
                                <img src="{{ $capBase64 }}" style="
                                    position: absolute; top: 0; left: 45px;
                                    width: 110px; height: 80px;
                                    opacity: 0.75; z-index: 1;
                                "/>
                                @endif
                                @if($ttdImgKiri)
                                <img src="{{ $ttdImgKiri }}" style="
                                    position: absolute; top: 5px; left: 45px;
                                    width: 90px; height: 70px;
                                    opacity: 0.95; z-index: 2;
                                "/>
                                @endif
                            </td>
                        </tr>
                    </table>

                    {{-- Nama kiri — align left + margin-left 15px --}}
                    @if($ttdKiri)
                    <div style="font-weight: bold; text-align: left; margin-left: 25px;">
                        {{ $ttdKiri->nama_penandatangan }}
                    </div>
                    {{-- Jabatan kiri — center di bawah nama --}}
                    <div style="text-align: left; margin-left: 65px; font-size: 10pt;">
                        {{ $ttdKiri->jabatan }}
                    </div>
                    @endif

                </td>

                {{-- ── Kanan: Penerima ── --}}
                <td style="width: 50%; vertical-align: top; padding: 0 0 0 10px; text-align: right;">

                    {{-- Label kanan — align right --}}
                    <div style="text-align: right;">{{ $surat->tujuan ?? '—' }}</div>

                    {{-- Gambar TTD kanan — rata kanan --}}
                    <table style="border-collapse: collapse; width: 140px; height: 80px; margin: 6px 0 6px auto;">
                        <tr>
                            <td style="position: relative; padding: 0; width: 140px; height: 80px;">
                                @if($ttdImgKanan)
                                <img src="{{ $ttdImgKanan }}" style="
                                    position: absolute; top: 5px; right: 70px;
                                    width: 90px; height: 70px;
                                    opacity: 0.95; z-index: 2;
                                "/>
                                @endif
                            </td>
                        </tr>
                    </table>

                    {{-- Nama kanan — align right + margin-right 15px --}}
                    @if($ttdKanan)
                    <div style="font-weight: bold; text-align: right; margin-right: 40px;">
                        {{ $ttdKanan->nama_penandatangan }}
                    </div>
                    {{-- Jabatan kanan — center di bawah nama --}}
                    <div style="text-align: right; margin-right: 70px; font-size: 10pt;">
                        {{ $ttdKanan->jabatan }}
                    </div>
                    @endif

            </td>
        </tr>
    </table>

</body>
</html>