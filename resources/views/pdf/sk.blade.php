<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Permohonan Keringanan Denda</title>

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
    <div class="title" style="font-size: 14pt; margin-top: 20px;">PERMOHONAN KERINGANAN DENDA</div>

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
    <div class="content" style="margin-bottom: 15px;">
        Kepada Yth. {{ $surat->tujuan ?? '-' }} <br />
    </div>

    <div class="content">
        Dengan hormat, <br />
    </div>  

    {{-- ISI --}}
    <div class="content" style="margin-top: 15px;">
        Sehubungan dengan adanya evaluasi atas progres pekerjaan dan pengajuan tambahan pekerjaan 
        yang telah kami sampaikan sebelumnya, bersama ini kami dari PT Bumi Rekayasa Mandiri
        mengajukan permohonan agar nilai denda yang dikenakan dapat disesuaikan.
    </div>

    <div class="content" style="margin-top: 15px;">
        Adapun berdasarkan hasil perhitungan awal, total nilai denda sebesar:
    </div>

    <div class="content" style="margin-top: 15px;">
        <strong> {{ $surat->hasil_denda ?? '-' }} </strong>
    </div>

    <div class="content" style="margin-top: 15px;">
        Kami memohon keringanan denda agar nilai denda dikenakan sebesar :
    </div>

    <div class="content" style="margin-top: 15px;">
        <strong> {{ $surat->keringanan_denda ?? '-' }} </strong>
    </div>
    
    <div class="content" style="margin-top: 15px;">
        yang mana jumlah tersebut akan langsung dipotong dari nilai tagihan kami, sesuai dengan usulan 
        dan pertimbangan nilai pekerjaan tambahan yang telah kami laksanakan di luar addendum awal.
    </div>

    <div class="content" style="margin-top: 15px;">
        Demikian surat ini kami sampaikan. Besar harapan kami agar permohonan ini dapat diterima demi 
        kelancaran kerja sama antara kedua belah pihak.
    </div>

    <div class="content" style="margin-top: 15px;">
    Atas perhatian dan kerja samanya, kami ucapkan terima kasih.
    </div>

    <div class="content" style="text-align: right; margin-top: 20px;">
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
            margin-top: 10px;
        ">

            {{-- CAP — paling bawah --}}
            @if($capBase64)
            <img src="{{ $capBase64 }}" style="
                position: absolute;
                top: 25px;
                right: 45px;
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
                top: 30px;
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
                Hormat Kami,<br><br><br><br><br>
                <strong>{{ $namaTtd }}</strong><br>
                {{ $jabatanTtd }}
            </div>

        </div>

    </div>

</body>
</html>