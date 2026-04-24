<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Perjanjian Investasi</title>

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
    }

    .nomor-table {
        width: 100%;
    }

    .nomor-table td.left {
        text-align: left;
    }

    .nomor-table td.right {
        text-align: right;
    }

    .content {
        text-align: justify;
        margin-bottom: 10px;
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
        width: 100px;
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
        width: 100px;
    }

    table.identitas1 td.colon {
        width: 10px;
    }

    table.content {
        width: 100%;
        margin: 7px 0;
    }

    table.content td {
        vertical-align: top;
        padding: 1px 0;
    }

    table.content td.label {
        width: 90px;
        margin-left: 10px;
    }

    table.content td.colon {
        width: 10px;
        mar
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

    .ttd-table {
        width: 100%;
        margin-bottom: 20px;
    }

    .ttd-table td.left {
        text-align: left;
    }

    .ttd-table td.right {
        text-align: right;
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
    <div class="title" style="font-size: 14pt; margin-top: 15px;">SURAT PERJANJIAN INVESTASI</div><br>

    {{-- NOMOR & TANGGAL --}}
    <table class="nomor-table">
    <tr>
        <td class="left">
            Nomor : {{ $surat->nomor_surat ?? '—' }}
        </td>
        <td class="right">
            Karawang, {{ \Carbon\Carbon::parse($surat->tanggal_surat)->locale('id')->translatedFormat('d F Y') }}
        </td>
    </tr>
    </table>

    {{-- PEMBUKA --}}
    <div class="content" style="margin-bottom: 10px;">
        Bismillahirrohmanirrohim.
    </div>

    <div class="content" style="margin-bottom: 10px;">
        Yang bertanda tangan di bawah ini:
    </div>

    {{-- IDENTITAS INVESTOR--}}
    <table class="identitas">
        <tr>
            <td class="label">Nama</td>
            <td class="colon">:</td>
            <td>{{ $surat->nama }}</td>
        </tr>
        <tr>
            <td class="label">Alamat</td>
            <td class="colon">:</td>
            <td>{{ $surat->alamat }}</td>
        </tr>
        <tr>
            <td class="label">No KTP</td>
            <td class="colon">:</td>
            <td>{{$surat->no_ktp }}</td>
        </tr>
    </table>

    <div class="content" style="margin-bottom: 10px;">
        Yang Menjadi <i> Pihak Pertama </i>
    </div>

    {{-- IDENTITAS PIHAK KEDUA --}}
    <table class="identitas1">
        <tr>
            <td class="label">Nama</td>
            <td class="colon">:</td>
            <td>Ilman Sunaryo</td>
        </tr>

        <tr>
            <td class="label">Alamat</td>
            <td class="colon">:</td>
            <td>Ruko Dharmawangsa 1 Blok D8/DC Grand Taruma Karawang</td>
        </tr>

        <tr>
            <td class="label">Jabatan</td>
            <td class="colon">:</td>
            <td>Direktur PT. Bumi Rekayasa Mandiri</td>
        </tr>
    </table>

    <div class="content" style="margin-bottom: 15px;">
        Yang Menjadi <i> Pihak Kedua </i>
    </div>

    <div class="content" style="margin-bottom: 10px;">
        ISI PERJANJIAN
    </div>

     <table class="content" style="margin-top: 10px; margin-left:20px">
        <tr>
            <td class="colon" style="margin-left:20px">•</td>
            <td>Perjanjian ini bersifat Mudhorobah, Pihak Pertama Sebagai Shohibul Maal atau Investor, dan Pihak Kedua Sebagai Mudhorib atau Penerima</td>
        </tr>

        <tr>
            <td class="colon">•</td>
            <td>Pihak Kedua diberikan dana investasi oleh Pihak Pertama</td>
        </tr>

        <tr>
            <td class="colon">•</td>
            <td>Nominal Investasi berdasarkan kesepakatan yaitu sebesar {{ $surat->nominal }}</td>
        </tr>

        <tr>
            <td class="colon">•</td>
            <td>Pihak Kedua akan membayar kepada Pihak Pertama selambat lambatnya tanggal 01 November 2025</td>
        </tr>

        <tr>
            <td class="colon">•</td>
            <td>Pihak Pertama Insya Allah akan mendapatkan hak bagi hasil sebesar {{ $surat->nominal_bagihasil }}</td>
        </tr>

        <tr>
            <td class="colon">•</td>
            <td>Apabila timbul perselisihan antara kedua belah pihak maka diselesaikan dengan cara kekeluargaan.</td>
        </tr>
    </table>

    <div class="content" style="text-align: right; margin-top: 10px;">
        Karawang, {{ \Carbon\Carbon::parse($surat->tanggal_surat)->locale('id')->translatedFormat('d F Y') }}
    </div>

        <div style="page-break-inside: avoid; margin-top: 10px;">

            @php
                $ttds   = $surat->ttds;
                $jumlah = $ttds->count() ?: 1;

                $capMedia  = $surat->getFirstMedia('cap');
                $capBase64 = $capMedia && file_exists($capMedia->getPath())
                    ? 'data:' . $capMedia->mime_type . ';base64,' . base64_encode(file_get_contents($capMedia->getPath()))
                    : null;
            @endphp

            {{-- TABEL TTD --}}
            <table style="width: 100%; margin-top: 5px; position: relative;">
                <tr>
                    @foreach($ttds as $i => $ttd)
                        @php
                            $ttdMedia  = $ttd->getFirstMedia('ttd');
                            $ttdBase64 = $ttdMedia && file_exists($ttdMedia->getPath())
                                ? 'data:' . $ttdMedia->mime_type . ';base64,' . base64_encode(file_get_contents($ttdMedia->getPath()))
                                : null;

                            $showCap = $capBase64 && ($i === $ttds->count() - 1);
                        @endphp
                        <td style="
                            text-align: center;
                            width: {{ round(100 / $jumlah) }}%;
                            vertical-align: top;
                            padding: 0 15px;
                        ">

                            {{-- Label --}}
                            <div style="margin-bottom: 6px;">{{ $ttd->label }}</div>

                            {{-- Container sejajar untuk cap + TTD --}}
                            {{-- width 140px, height 90px → keduanya pakai left: 50%, top: 50% + transform --}}
                            <table style="margin: 0 auto; border-collapse: collapse; width: 140px; height: 90px;">
                                <tr>
                                    <td style="position: relative; width: 140px; height: 90px; padding: 0;">

                                        {{-- Cap (layer bawah) --}}
                                        @if($showCap)
                                        <img src="{{ $capBase64 }}" style="
                                            position: absolute;
                                            top: 50%;
                                            left: 50%;
                                            margin-top: -45px;
                                            margin-left: -55px;
                                            width: 110px;
                                            height: 90px;
                                            opacity: 0.80;
                                            z-index: 1;
                                        "/>
                                        @endif

                                        {{-- TTD (layer atas) --}}
                                        @if($ttdBase64)
                                        <img src="{{ $ttdBase64 }}" style="
                                            position: absolute;
                                            top: 50%;
                                            left: 50%;
                                            margin-top: -40px;
                                            margin-left: -45px;
                                            width: 90px;
                                            height: 80px;
                                            opacity: 0.95;
                                            z-index: 2;
                                        "/>
                                        @endif

                                    </td>
                                </tr>
                            </table>

                            {{-- Nama & Jabatan --}}
                            <div style="margin-top: 4px; font-size: 11pt;">
                                {{ $ttd->nama_penandatangan }}
                            </div>

                        </td>
                    @endforeach
                </tr>
            </table>

        </div>

</body>
</html>