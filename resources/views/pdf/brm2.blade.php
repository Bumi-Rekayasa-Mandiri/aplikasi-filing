<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Pelepasan Hak</title>

<style>
    .kop {
        width: 100%;
        scale: 0.50;
    }

    .kop img {
        width: 100%;
        scale: 0.50;
        height: auto;
        display: block;
    }

    body {
        font-family: "Times New Roman", serif;
        font-size: 12pt;
        line-height: 1.15;
        margin: 5mm 10mm 10mm 10mm;
    }

    .header {
        text-align: center;
        font-size: 10pt;
        margin-bottom: 20px;
    }

    .title {
        text-align: center;
        font-weight: bold;
        text-decoration: underline;
        text-transform: uppercase;
        margin-bottom: 20px;
    }

    .nomor-table {
        width: 100%;
        margin-bottom: 20px;
    }

    .nomor-table td.left {
        text-align: left;
    }

    .nomor-table td.right {
        text-align: right;
    }

    .content {
        text-align: justify;
        margin-bottom: 1px;
    }

    table.identitas {
        width: 100%;
        margin: 7px 0;
    }

    table.identitas td {
        vertical-align: top;
        padding: 1px 0;
    }

    table.identitas td.label {
        width: 170px;
    }

    table.identitas td.colon {
        width: 10px;
    }

    table.identitas1 {
        width: 100%;
        margin: 7px 0;
    }

    table.identitas1 td {
        vertical-align: top;
        padding: 1px 0;
    }

    table.identitas1 td.label {
        width: 170px;
    }

    table.identitas1 td.colon {
        width: 10px;
    }

    .signature-wrapper {
        position: relative;
        width: 100%;
        margin-top: 0px;
    }

    .cap {
        position: absolute;
        top: 0;
        right: 70px;
        top: 0;
        width: 120px;
        opacity: 0.35;
        z-index: 1;
    }

    .ttd {
        position: absolute;
        top: 0;
        right: 100px;
        width: 90px;
        z-index: 2;
    }

    .signature-name {
        position: absolute;
        top: 0;
        right: 90px;
        text-align: center;
        width: 200px;
        float: right;
    }

</style>
</head>
<body>

    {{-- KOP SURAT --}}
    <div class="kop">
        <img src="{{ public_path('assets/kop.png') }}">
    </div>

    {{-- HEADER --}}
    <div class="header">
        e-mail : bumirekayasa.mandiri@gmail.com
        Phone : 0267-8639-837 / Fax: 0267-8639-837
    </div>

    {{-- TITLE --}}
    <div class="title" style="font-size: 14pt; margin-top: 20px;">SURAT PELEPASAN HAK</div>

    {{-- PEMBUKA --}}
    <div class="content">
        Yang bertanda tangan di bawah ini:
    </div>

    {{-- IDENTITAS PENANDATANG --}}
    <table class="identitas">
        <tr>
            <td class="label"><strong>Nama</strong></td>
            <td class="colon">:</td>
            <td>Ilman Sunaryo</td>
        </tr>
        <tr>
            <td class="label"><strong>Jabatan</strong></td>
            <td class="colon">:</td>
            <td>Direktur</td>
        </tr>
        <tr>
            <td class="label"><strong>NIK</strong></td>
            <td class="colon">:</td>
            <td>3215030103870006</td>
        </tr>
        <tr>
            <td class="label"><strong>Tempat/Tanggal Lahir</strong></td>
            <td class="colon">:</td>
            <td>Karawang, 01 Maret 1987</td>
        </tr>
        <tr>
            <td class="label"><strong>Alamat</strong></td>
            <td class="colon">:</td>
            <td>
                Dusun Bobojong RT 005 RW 003, Purwadana, Telukjambe Timur, Karawang
            </td>
        </tr>
    </table>

    <div class="content">
        Dengan ini menerangkan bahwa data kendaraan tersebut di bawah ini :
    </div>

    {{-- IDENTITAS KENDARAAN --}}
    <table class="identitas1">
        <tr>
            <td class="label"><strong>Merk/Jenis</strong></td>
            <td class="colon">:</td>
            <td>{{ $surat->merk ?? '—' }}</td>
        </tr>
        <tr>
            <td class="label"><strong>Warna/Tahun</strong></td>
            <td class="colon">:</td>
            <td>{{ $surat->warna ?? '—' }}</td>
        </tr>
        <tr>
            <td class="label"><strong>Rangka/Mesin</strong></td>
            <td class="colon">:</td>
            <td>{{ $surat->rangka ?? '—' }}</td>
        </tr>
    </table>

    {{-- ISI --}}
    <div class="content" style="margin-top: 10px;">
        Dan telah melepaskan haknya atas kendaraan tersebut di atas.<br>
        Demikian Surat Pelepasan Hak ini dibuat untuk dipergunakan sebagaimana mestinya.
    </div>

    <div class="content" style="text-align: right; margin-top: 25px; margin-bottom: 7px;">
        Karawang, {{ \Carbon\Carbon::parse($surat->tanggal_surat)->locale('id')->translatedFormat('d F Y') }}
    </div>

    <div style="page-break-inside: avoid; margin-top: 10px;">
            @php
                $ttds   = $surat->ttds;
                $jumlah = $ttds->count() ?: 1;

                $capMedia  = $surat->getFirstMedia('cap');
                $capPath   = $capMedia ? str_replace('\\', '/', $capMedia->getPath()) : null;
                $capBase64 = $capPath && file_exists($capPath)
                    ? 'data:' . $capMedia->mime_type . ';base64,' . base64_encode(file_get_contents($capPath))
                    : null;
            @endphp

            <table style="width: 50%; margin-left: auto;">  {{-- ← 50% dan dorong ke kanan --}}
                <tr>
                    @foreach($ttds as $i => $ttd)
                    @php
                        $ttdMedia  = $ttd->getFirstMedia('ttd');
                        $ttdPath   = $ttdMedia ? str_replace('\\', '/', $ttdMedia->getPath()) : null;
                        $ttdBase64 = $ttdPath && file_exists($ttdPath)
                            ? 'data:' . $ttdMedia->mime_type . ';base64,' . base64_encode(file_get_contents($ttdPath))
                            : null;
                        $showCap = $capBase64 && ($i === 0);
                    @endphp
                    <td style="
                        text-align: center;
                        width: {{ round(100 / $jumlah) }}%;
                        vertical-align: bottom;
                        padding: 0 15px 5px 15px;
                        position: relative;
                        height: 160px;
                    ">

                        {{-- Cap --}}
                        @if($showCap)
                        <img src="{{ $capBase64 }}" style="
                            position: absolute;
                            top: 0px;
                            left: 100%;
                            transform: translateX(-100%);
                            width: 100px;
                            height: auto;
                            opacity: 0.75;
                            z-index: 1;
                        "/>
                        @endif

                        {{-- TTD --}}
                        @if($ttdBase64)
                        <img src="{{ $ttdBase64 }}" style="
                            position: absolute;
                            top: 20px;
                            left: 95%;
                            transform: translateX(-100%);
                            width: 100px;
                            height: auto;
                            opacity: 0.95;
                            z-index: 2;
                        "/>
                        @endif

                        {{-- Nama & Jabatan --}}

                        <div style="
                            position: absolute;
                            top: 0;
                            right: 0;
                            width: 200px;
                            text-align: center;
                            z-index: 3;
                        ">
                            {{ $ttd->label }},<br><br><br><br><br>
                            {{$ttd->nama_penandatangan }}<br />
                            {{ $ttd->jabatan }}
                        </div>
                                          
                    </div>
                    </td>
                    @endforeach
                </tr>
            </table>
        </div>

</body>
</html>