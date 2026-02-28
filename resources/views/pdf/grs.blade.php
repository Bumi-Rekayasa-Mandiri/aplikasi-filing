<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Pengajuan Garansi Material</title>

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

    .nomor-table td.colon {
        width: 10px;
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
        width: 100px;
    }

    table.identitas td.colon {
        width: 10px;
    }

    table.rincian {
        width: 100%;
        margin: 7px 0;
    }

    table.rincian td {
        vertical-align: top;
        padding: 1px 0;
    }

    table.rincian td.label {
        width: 100px;
    }

    table.rincian td.colon {
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
        text-decoration: underline;
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
    <div class="title" style="font-size: 14pt; margin-top: 20px;">SURAT PENGAJUAN GARANSI</div>

    {{-- NOMOR & TANGGAL --}}
    <table class="nomor-table">
    <tr>
        <td class="left">Nomor
        <td class="colon">:</td>
              <td>{{ $surat->nomor_surat ?? '—' }}</td>
        <td class="right">
            Karawang, {{ \Carbon\Carbon::parse($surat->tanggal_surat)->locale('id')->translatedFormat('d F Y') }}
        </td>
    </tr>
    <tr>
        <td class="left">Hal</td>
        <td class="colon">:</td>
            <td>{{ $surat->perihal ?? '-' }}</td>
        </td>
    </tr>
    </table>

    {{-- PEMBUKA --}}
    <div class="content">
        Saya yang bertanda tangan di bawah ini:
    </div>

    <table class="identitas">
        <tr>
            <td class="label">Nama</td>
            <td class="colon">:</td>
            <td>Ilman Sunaryo</td>
        </tr>
        <tr>
            <td class="label">Perusahaan</td>
            <td class="colon">:</td>
            <td>PT. Bumi Rekayasa Mandiri</td>
        </tr>
        <tr>
            <td class="label">Jabatan</td>
            <td class="colon">:</td>
            <td>Direktur Utama</td>
        </tr>
        <tr>
            <td class="label">Alamat</td>
            <td class="colon">:</td>
            <td>Ruko Dharmawangsa 1 Blok D8/DC Grand Taruma Telukjambe Karawang</td>
        </tr>
    </table>

    <div class="content">
        Dengan ini mengajukan garansi material kepada PT. BlueScope Steel Indonesia berupa :
    </div>

    <table class="rincian">
        <tr>
            <td class="label">Material</td>
            <td class="colon">:</td>
            <td>{{ $surat->material }}</td>
        </tr>

        <tr>
            <td class="label">Project</td>
            <td class="colon">:</td>
            <td>{{ $surat->project }}</td>
        </tr>

        <tr>
            <td class="label">Alamat</td>
            <td class="colon">:</td>
            <td>{{ $surat->alamat }}</td>
        </tr>
        
        <tr>
            <td class="label">Masa Garansi</td>
            <td class="colon">:</td>
            <td>{{ $surat->masa_garansi }}</td>
        </tr>
    </table>

    <div class="content" style="margin-top: 10px">
        Demikian Surat Pengajuan ini kami buat, atas perhatian dan kerja samanya kami ucapkan terima kasih.
    </div>


    <div class="content" style="text-align: right; margin-top: 25px; margin-bottom: 5px;">
        Karawang, {{ \Carbon\Carbon::parse($surat->tanggal_surat)->locale('id')->translatedFormat('d F Y') }}
    </div>

    <div style="page-break-inside: avoid;">

        @php
            $ttdMedia = $surat->getFirstMedia('ttd');
            $capMedia = $surat->getFirstMedia('cap');

            $ttdBase64 = $ttdMedia && file_exists($ttdMedia->getPath())
                ? 'data:' . $ttdMedia->mime_type . ';base64,' . base64_encode(file_get_contents($ttdMedia->getPath()))
                : null;

            $capBase64 = $capMedia && file_exists($capMedia->getPath())
                ? 'data:' . $capMedia->mime_type . ';base64,' . base64_encode(file_get_contents($capMedia->getPath()))
                : null;

            $namaTtd  = $surat->ttds->first()->nama ?? ($surat->nama_penandatangan ?? 'Ilman Sunaryo');
            $jabatanTtd = $surat->ttds->first()->jabatan ?? ($surat->jabatan ?? 'Direktur');
        @endphp

        {{-- BLOK TANDA TANGAN --}}
        <div style="
            position: relative;
            width: 100%;
            height: 140px;      /* ← tinggi container, sesuaikan jika teks terpotong */
            margin-top: 5px;
        ">

            {{-- CAP — paling bawah --}}
            @if($capBase64)
            <img src="{{ $capBase64 }}" style="
                position: absolute;
                top: 20px;
                right: 40px;
                width: 115px;
                height: auto;
                opacity: 0.75;
                z-index: 1;
            "/>
            @endif

            {{-- TTD — di atas cap --}}
            @if($ttdBase64)
            <img src="{{ $ttdBase64 }}" style="
                position: absolute;
                top: 28px;
                right: 50px;
                width: 100px;
                height: auto;
                opacity: 0.95;
                z-index: 2;
            "/>
            @endif

            {{-- TEKS: Hormat Kami, Nama, Jabatan — paling atas --}}
            <div style="
                position: absolute;
                top: 0;
                right: 0;
                width: 200px;
                text-align: center;
                z-index: 3;
            ">
                Yang Membuat Pernyataan,<br><br><br><br><br>
                <span style="text-decoration: underline;"><strong>{{ $namaTtd }}</strong></span><br>
                {{ $jabatanTtd }}
            </div>

        </div>

    </div>

</body>
</html>