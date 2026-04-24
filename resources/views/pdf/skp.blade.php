<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Pemberitahuan PHK</title>

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
        width: 80px;
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
        width: 150px;
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
    <div class="title" style="font-size: 14pt; margin-top: 20px;">SURAT PEMBERITAHUAN</div>

    {{-- NOMOR & TANGGAL --}}
    <table class="nomor-table">
    <tr>
        <td class="left">
            Nomor : {{ $surat->nomor_surat ?? '—' }}
        </td>
        <td class="right">
            Karawang, {{ \Carbon\Carbon::createFromFormat('Y-m-d', $surat['tanggal_surat'])->locale('id')->translatedFormat('d F Y') }}
        </td>
    </tr>
    </table>

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
            <td class="label"><strong>Perusahaan</strong></td>
            <td class="colon">:</td>
            <td>PT. Bumi Rekayasa Mandiri</td>
        </tr>
        <tr>
            <td class="label"><strong>Alamat</strong></td>
            <td class="colon">:</td>
            <td>
                Ruko Grand Taruma Blok D8/DC, Telukjambe Timur,
                Karawang, Indonesia
            </td>
        </tr>
    </table>

    <div class="content">
        Dengan ini menerangkan bahwa:
    </div>

    {{-- IDENTITAS KARYAWAN --}}
    <table class="identitas1">
        <tr>
            <td class="label"><strong>Nama</strong></td>
            <td class="colon">:</td>
            <td>{{ $surat->nama ?? '—' }}</td>
        </tr>
        <tr>
            <td class="label"><strong>Jabatan Terakhir</strong></td>
            <td class="colon">:</td>
            <td>{{ $surat->jabatan_terakhir ?? '—' }}</td>
        </tr>
        <tr>
            <td class="label"><strong>Departemen / Bagian</strong></td>
            <td class="colon">:</td>
            <td>{{ $surat->departemen ?? '—' }}</td>
        </tr>
    </table>

    {{-- ISI --}}
    <div class="content" style="margin-top: 10px;">
        Telah <strong>tidak lagi bekerja di PT. Bumi Rekayasa Mandiri terhitung sejak tanggal
        {{ \Carbon\Carbon::createFromFormat('Y-m-d', $surat['tanggal_surat'])->locale('id')->translatedFormat('d F Y') }}</strong>,
        dengan alasan mengundurkan diri.
    </div>

    <div class="content" style="margin-top: 10px;">
        Segala tindakan yang dilakukan setelah berakhirnya hubungan kerja sepenuhnya
        menjadi tanggung jawab pribadi yang bersangkutan dan tidak menjadi tanggung
        jawab perusahaan.
    </div>

    <div class="content" style="margin-top: 10px;">
        Apabila terdapat hal-hal yang perlu dikonfirmasi atau terkait dengan pekerjaan
        yang bersangkutan, dapat menghubungi Ilman Sunaryo di nomor +62811964060
        atau email berikut:
    </div>

        <div class="content" style="text-color: navy; margin-left: 20px; margin-top: 10px; margin-bottom: 10px;">
            • bumirekayasa.mandiri@gmail.com <br>
            • info@bumirekayasamandiri.co.id <br>
            • info@bumirekamandiri.id
        </div>
    
    <div class="content" style="margin-top: 10px;">
        Demikian surat pemberitahuan ini dibuat dengan sebenar-benarnya. Atas perhatian
        dan kerja samanya, kami ucapkan terima kasih.
    </div>

    <div class="content" style="text-align: right; margin-top: 20px;">
        Karawang, {{ \Carbon\Carbon::createFromFormat('Y-m-d', $surat['tanggal_surat'])->locale('id')->translatedFormat('d F Y') }}
    </div>

    <div style="page-break-inside: avoid; margin-top: 10px">

            @php
                $ttds   = $surat->ttds;
                $jumlah = $ttds->count() ?: 1;

                $capMedia  = $surat->getFirstMedia('cap');
                $capBase64 = $capMedia && file_exists($capMedia->getPath())
                    ? 'data:' . $capMedia->mime_type . ';base64,' . base64_encode(file_get_contents($capMedia->getPath()))
                    : null;
            @endphp

            {{-- TABEL TTD --}}
            <table style="width: 100%; position: relative;">
                <tr>
                    @foreach($ttds as $i => $ttd)
                    @php
                        $ttdMedia  = $ttd->getFirstMedia('ttd');
                        $ttdBase64 = $ttdMedia && file_exists($ttdMedia->getPath())
                            ? 'data:' . $ttdMedia->mime_type . ';base64,' . base64_encode(file_get_contents($ttdMedia->getPath()))
                            : null;

                        // Cap hanya di kolom paling kanan (penandatangan terakhir)
                        $showCap = $capBase64 && ($i === $ttds->count() - 1);
                    @endphp
                    <td style="
                        text-align: left;
                        width: {{ round(100 / $jumlah) }}%;
                        vertical-align: bottom;
                        padding: 0 15px 5px 0;
                        position: relative;
                    ">

                        {{-- Cap rata kiri --}}
                        @if($showCap)
                        <img src="{{ $capBase64 }}" style="
                            position: absolute;
                            top: 8px;
                            right: 45px;
                            width: 115px;
                            height: auto;
                            opacity: 0.75;
                            z-index: 1;
                        "/>
                        @endif

                        {{-- TTD rata kiri --}}
                        @if($ttdBase64)
                        <img src="{{ $ttdBase64 }}" style="
                            position: absolute;
                            top: 16px;
                            right: 50px;
                            width: 100px;
                            height: auto;
                            opacity: 0.95;
                            z-index: 2;
                        "/><br>
                        @else
                        <br><br><br><br>
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
                            {{$ttd->nama_penandatangan }}<br>
                            {{ $ttd->jabatan }}
                        </div>
                    </td>
                    @endforeach
                </tr>
            </table>

        </div>

</body>
</html>