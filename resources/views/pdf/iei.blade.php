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

    <div class="content" style="text-align: left; margin-top: 40px;">
        Karawang, {{ \Carbon\Carbon::parse($surat->tanggal_surat)->locale('id')->translatedFormat('d F Y') }}
    </div>

    <div style="page-break-inside: avoid;">

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
                        {{-- Label --}}
                        {{ $ttd->label }}<br><br>

                        {{-- Cap rata kiri --}}
                        @if($showCap)
                        <img src="{{ $capBase64 }}" style="
                            position: absolute;
                            bottom: 52px;
                            left: 0px;
                            width: 110px;
                            opacity: 0.84;
                            z-index: 1;
                        "/>
                        @endif

                        {{-- TTD rata kiri --}}
                        @if($ttdBase64)
                        <img src="{{ $ttdBase64 }}" style="
                            position: relative;
                            bottom: 10px;
                            left: 0;
                            width: 90px;
                            height: auto;
                            opacity: 0.95;
                            z-index: 2;
                        "/><br>
                        @else
                        <br><br><br><br>
                        @endif

                        {{-- Nama & Jabatan --}}
                        <strong style="position: relative; text-decoration: underline; left: 10px; z-index: 3;">
                            {{ $ttd->nama_penandatangan }}
                        </strong><br>
                    </td>
                    @endforeach
                </tr>
            </table>

        </div>

</body>
</html>