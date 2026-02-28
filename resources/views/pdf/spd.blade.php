<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Pengembalian Dana</title>

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
        margin-bottom: 10px;
    }

    .sub-title {
        text-align: center;
        margin-bottom: 30px;
    }

    .nomor-table {
        width: 100%;
        margin-bottom: 20px;
    }

    .nomor-table td.left {
        text-align: left;
        width: 80px;
    }

    .nomor-table td.right {
        text-align: right;
    }

    .nomor-table td.colon {
        width: 10px;
    }

    .content {
        text-align: justify;
        margin-bottom: 5px;
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
        text-align: left;
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
    <div class="title" style="font-size: 14pt; margin-top: 20px;">SURAT PERMOHONAN</div>
    <div class="sub-title"> Nomor : {{ $surat->nomor_surat ?? '—' }} </div>


    {{-- NOMOR & TANGGAL --}}
    <table class="nomor-table" style="margin-top: 20px">
    <tr>
        <td class="left">Lampiran</td>
        <td class="colon">:</td>
              <td>{{ $surat->lampiran ?? '-' }}</td>
        
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
    <div class="content" style="margin-bottom:10px;">
        Kepada Yth. <br>
        {{ $surat->tujuan ?? '-' }}<br>
        {{ $surat->alamat ?? '-' }}<br>
        Di Tempat
    </div>

    <div class="content" style="margin-top: 15px;">
        Berdasarkan Invoice tersebut di atas terkait pembelian {{ $surat->item_pembelian ?? '-' }}. Maka dengan ini Kami mohon 
        pengembalian transfer pembelian material tersebut sebesar {{ $surat->nominal ?? '-' }} Kiranya dapat dibayarkan melalui 
        rekening sbb:
    </div>

    {{-- RINCIAN BANK --}}
    <table class="identitas" style="margin-top:15px; margin-bottom:15px;">
        <tr>
            <td class="label">Bank</td>
            <td class="colon">:</td>
            <td>{{ $surat->nama ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Cabang</td>
            <td class="colon">:</td>
            <td>{{ $surat->isi_surat ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Nomor Rekening</td>
            <td class="colon">:</td>
            <td>{{ $surat->no_ktp ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Atas Nama</td>
            <td class="colon">:</td>
            <td>
                PT. Bumi Rekayasa Mandiri
            </td>
        </tr>
    </table>

    <div class="content">
        Demikian yang dapat kami sampaikan, atas perhatian dan kerjasamanya disampaikan terima kasih.
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
            margin-top: 15px;
        ">

            {{-- CAP — paling bawah --}}
            @if($capBase64)
            <img src="{{ $capBase64 }}" style="
                position: absolute;
                top: 40px;
                left: 0px;
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
                top: 40px;
                left: 0px;
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
                left: 0;
                width: 200px;
                text-align: left;
                z-index: 3;
            ">
                Diajukan Oleh,<br>
                PT. Bumi Rekayasa Mandiri<br><br><br><br><br>
                {{ $namaTtd }}<br>
                {{ $jabatanTtd }}
            </div>

        </div>

    </div>

</body>
</html>