<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Garansi Pekerjaan</title>

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

    .subtitle {
        text-align: center;
        font-weight: bold;
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

    table.project {
        width: 100%;
        margin: 7px 0;
    }

    table.project td {
        vertical-align: top;
        padding: 1px 0;
    }

    table.project td.label {
        width: 80px;
    }

    table.project td.colon {
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
    <div class="title" style="font-size: 20pt; margin-top: 20px;">SURAT GARANSI PEMASANGAN</div>

    {{-- NOMOR --}}
    <div class="nomor-table" style ="font-size: 16pt; text-align: center; margin-bottom: 20px">
            Nomor : {{ $surat->nomor_surat ?? '—' }}
    </div>

    {{-- PEMBUKA --}}
    <div class="content" style="margin-bottom: 10px">
        Yang bertanda tangan di bawah ini:
    </div>

    {{-- IDENTITAS PENANDATANG --}}
    <table class="identitas" style="margin-bottom: 10px">
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
    </table>

    <div class="content" style="margin-bottom: 10px">
        Bersama ini memberikan jaminan garansi untuk:
    </div>

    {{-- IDENTITAS KARYAWAN --}}
    <table class="project" style="margin-bottom: 10px">
        <tr>
            <td class="label"><strong>Project</strong></td>
            <td class="colon">:</td>
            <td>{{ $surat->project ?? '—' }}</td>
        </tr>
        <tr>
            <td class="label"><strong>Lokasi</strong></td>
            <td class="colon">:</td>
            <td>{{ $surat->lokasi_kerja ?? '-' }}</td>
        </tr>
    </table>

    {{-- ISI --}}
    <div class="content" style="text-align: center; margin-top: 10px; text-decoration: underline;">
        Garansi berlaku mulai {{ $surat->isi_surat }}, meliputi :
    </div>

    <div class="content" style="margin-left: 20px; margin-top: 10px; margin-bottom: 10px;">
        @php
            $jenis_pekerjaan = json_decode($surat->jenis_pekerjaan ?? '[]', true);
        @endphp

        @if(!empty($jenis_pekerjaan))
            <ol style="margin: 5px 0; padding-left: 20px; font-weight: bold;">
                @foreach($jenis_pekerjaan as $item)
                <li style="margin-bottom: 2px;">{{ $item }}</li>
                @endforeach
            </ol>
        @endif
    </div>

    <div class="content" style="margin-top: 10px;">
        Garansi tidak berlaku apabila kerusakan di akibatkan oleh :
    </div>

        <div class="content" style="text-color: navy; margin-left: 20px; margin-top: 10px; margin-bottom: 10px;">
            <strong> 1. Kesalahan Prosedur Pemakaian (Human Error) <br>
            2. Gangguan Bencana Alam (Force Majure) <br>
            3. Kerusakan karena Pekerjaan dari Pihak lain </strong> </div>
    
    <div class="content" style="margin-top: 10px;">
        Demikian Garansi ini kami buat untuk dapat dipergunakan sebagaimana mestinya.
    </div>

    <div class="content" style="text-align: left; margin-top: 20px;">
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
            height: 180px;
        ">

            {{-- CAP — paling bawah --}}
            @if($capBase64)
            <img src="{{ $capBase64 }}" style="
                position: absolute;
                top: 25px;
                left: 15px;
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
                top: 33px;
                left: 20px;
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
                width: 250px;
                text-align: left;
                z-index: 3;
                white-space: nowrap;
            ">
                PT. BUMI REKAYASA MANDIRI<br><br><br><br><br>
                <strong style="text-decoration: underline; text-transform: uppercase; margin-top: 7px;">{{ $namaTtd }}</strong><br>
            </div>

        </div>

    </div>

</body>
</html>