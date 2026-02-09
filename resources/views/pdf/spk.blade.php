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
            Karawang, {{ \Carbon\Carbon::parse($surat->tanggal_surat)->locale('id')->translatedFormat('d F Y') }}
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
        {{ \Carbon\Carbon::parse($surat->tanggal_surat)->locale('id')->translatedFormat('d F Y') }}</strong>,
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

    <div class="content" style="text-align: right; margin-top: 10px;">
        Karawang, {{ \Carbon\Carbon::parse($surat->tanggal_surat)->locale('id')->translatedFormat('d F Y') }}
    </div>

    <div style="page-break-inside: avoid;"
    
        <div class="signature-wrapper">

            {{-- CAP --}}
            @if($surat->getFirstMediaUrl('cap'))
                <img
                    src="{{ public_path($surat->getFirstMedia('cap')->getPath()) }}"
                    class="cap"
                >
            @endif

            {{-- TTD --}}
            @if($surat->ttds->count())
                <img
                    src="{{ public_path($surat->ttds->first()->getFirstMedia('ttd')->getPath()) }}"
                    class="ttd"
                >
            @endif

            {{-- NAMA --}}
            <div class="signature-name">
                Hormat Kami<br><br><br><br>
                {{ $surat->ttds->first()->nama_penandatangan ?? 'Ilman Sunaryo' }}<br>
                {{ $surat->ttds->first()->jabatan ?? 'Direktur' }}
            </div>
        </div>
    
    </div>

</body>
</html>