<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Izin Kerja dan LK3</title>

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
        line-height: 1.5;
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
        width: 150px;
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
        left: 70px;
        top: 0;
        width: 120px;
        opacity: 0.35;
        z-index: 1;
    }

    .ttd {
        position: absolute;
        top: 0;
        left: 100px;
        width: 90px;
        z-index: 2;
    }

    .signature-name {
        position: absolute;
        top: 0;
        left: 90px;
        text-align: center;
        width: 200px;
        float: left;
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
    <div class="title" style="font-size: 14pt; margin-top: 20px;">SURAT PEMBERITAHUAN KERJA</div>

    {{-- NOMOR & TANGGAL --}}
    <table class="nomor-table">
    <tr>
        <td class="left">
            <strong> 
            Kepada : <br>
            Yth. {{ $surat->tujuan ?? '-' }} </strong> <br>
            Up. Dept. {{ $surat->departemen ?? '-' }} <br>
            Bp. {{ $surat->nama ?? '-' }} <br>
            Di Tempat <br>
        </td>
    </table>

    {{-- PEMBUKA --}}
    <div class="content" style="margin-bottom: 5px">
        Dengan Hormat,<br>
        Bersama ini kami mengajukan pelaksanaan kerja :
    </div>

    {{-- DATA DETAIL PEKERJAAN --}}
    <table class="identitas">
        <tr>
            <td class="label"><strong>Lokasi Kerja</strong></td>
            <td class="colon">:</td>
            <td>{{ $surat->lokasi_kerja ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label"><strong>Jenis Pekerjaan</strong></td>
            <td class="colon">:</td>
            <td>{{ $surat->jenis_pekerjaan ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label"><strong>Waktu</strong></td>
            <td class="colon">:</td>
            <td>{{ $surat->waktu ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label"><strong>Jam Kerja</strong></td>
            <td class="colon">:</td>
            <td>
                {{ $surat->jam_kerja ?? '-' }}
            </td>
        </tr>
        <br>
        <tr>
            <td class="label"><strong>Penanggung Jawab</strong></td>
            <td class="colon">:</td>
            <td>
                Ilman Sunaryo
            </td>
        </tr>
        <tr>
            <td class="label"><strong>Jumlah Pekerja</strong></td>
            <td class="colon">:</td>
            <td>
                {{ $surat->jumlah_pekerja ?? '-' }}
            </td>
        </tr>
    </table>

    <div class="content" style="margin-top: 15px;">
        Atas kerja samanya, kami mengucapkan terima kasih.
    </div>

    <div class="content" style="text-align: left; margin-top: 15px;">
        Karawang, {{ \Carbon\Carbon::parse($surat->tanggal_surat)->locale('id')->translatedFormat('d F Y') }}
    </div>

      <div style="page-break-inside: avoid; margin-top: 5px;">

            @php
                $ttds   = $surat->ttds;
                $jumlah = $ttds->count() ?: 1;

                $capMedia  = $surat->getFirstMedia('cap');
                $capBase64 = $capMedia && file_exists($capMedia->getPath())
                    ? 'data:' . $capMedia->mime_type . ';base64,' . base64_encode(file_get_contents($capMedia->getPath()))
                    : null;
            @endphp

            {{-- TABEL TTD --}}
            <table style="width: 110%; position: relative;">
                <tr>
                    @foreach($ttds as $i => $ttd)
                    @php
                        $ttdMedia  = $ttd->getFirstMedia('ttd');
                        $ttdBase64 = $ttdMedia && file_exists($ttdMedia->getPath())
                            ? 'data:' . $ttdMedia->mime_type . ';base64,' . base64_encode(file_get_contents($ttdMedia->getPath()))
                            : null;

                        // Cap hanya di kolom paling kanan (penandatangan terakhir)
                        $showCap = $capBase64 && ($i === 0);
                    @endphp
                    <td style="
                        text-align: center;
                        width: {{ round(100 / $jumlah) }}%;
                        vertical-align: bottom;
                        padding: 0;
                        position: relative;
                    ">
                        {{-- Label: tampil jika ada, spasi jika kosong --}}
                        @if(!empty($ttd->label))
                            {{ $ttd->label }}
                        @else
                            &nbsp;
                        @endif
                        <br><br>

                        {{-- Cap hanya di kolom pertama (index 0) --}}
                        @if($showCap)
                        <img src="{{ $capBase64 }}" style="
                            position: absolute;
                            bottom: 132px;
                            left: 50%;
                            transform: translateX(-50%);
                            width: 110px;
                            opacity: 0.70;
                            z-index: 1;
                        "/>
                        @endif

                        @if($ttdBase64)
                        <img src="{{ $ttdBase64 }}" style="
                            position: relative;
                            bottom: 10px;
                            width: 90px;
                            height: auto;
                            opacity: 0.95;
                            z-index: 2;
                        "/><br>
                        @else
                        <br><br><br><br><br>
                        @endif

                        <strong style="position: relative; z-index: 3;">
                            {{ $ttd->nama_penandatangan }}
                        </strong><br>
                    </td>
                    @endforeach
                </tr>
            </table>

        </div>

</body>
</html>