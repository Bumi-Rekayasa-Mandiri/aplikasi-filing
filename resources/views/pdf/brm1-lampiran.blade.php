<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Lampiran {{ $surat->nomor_surat }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10pt;
            line-height: 1.3;
            margin: 5mm 10mm 10mm 10mm;
        }

        /* ── Repeat kop di setiap halaman ── */
        @page { margin-top: 45mm; }

        header {
            position: fixed;
            top: -40mm;
            left: 0;
            right: 0;
            width: 100%;
        }

        header img {
            width: 100%;
            height: auto;
            display: block;
        }

        .page-break { page-break-after: always; }

        .section-title {
            font-size: 12pt;
            font-weight: bold;
            margin-bottom: 10px;
        }

        table.meta { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        table.meta td { padding: 2px 0; vertical-align: top; font-size: 10pt; }
        table.meta td.label { width: 130px; }
        table.meta td.colon { width: 10px; }

        table.jsa {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
            font-size: 9.5pt;
        }
        table.jsa th, table.jsa td {
            border: 1px solid #000;
            padding: 5px 7px;
            vertical-align: top;
        }
        table.jsa th {
            text-align: center;
            font-weight: bold;
            background: #f0f0f0;
        }

        table.ttd-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 16px;
        }
        table.ttd-table th {
            border: 1px solid #000;
            padding: 6px;
            text-align: center;
            font-size: 10pt;
            font-weight: bold;
            background: #f9f9f9;
        }
        table.ttd-table td {
            border: 1px solid #000;
            padding: 6px;
            text-align: center;
            vertical-align: bottom;
            height: 80px;
        }

        table.pekerja { width: 100%; border-collapse: collapse; font-size: 10pt; }
        table.pekerja th {
            border: 1px solid #000;
            padding: 6px 10px;
            font-weight: bold;
            background: #f0f0f0;
        }
        table.pekerja td {
            border: 1px solid #000;
            padding: 8px 10px;
            vertical-align: top;
        }
        table.pekerja td.no { text-align: center; width: 40px; }
        table.pekerja td.nama { width: 30%; font-weight: bold; }
        table.pekerja td.ktp { width: 65%; text-align: center; }
        table.pekerja td.ktp img {
            width: 100%;
            max-width: 380px;
            height: auto;
            display: block;
            margin: 0 auto;
        }
    </style>
</head>
<body>

    {{-- ✅ Kop lampiran repeat setiap halaman --}}
    <header>
        @if($kopLampiranBase64)
        <img src="{{ $kopLampiranBase64 }}" />
        @endif
    </header>

    {{-- ══════════════════════════════════════ --}}
    {{-- LAMPIRAN A: JSA                        --}}
    {{-- ══════════════════════════════════════ --}}

    <div class="section-title">A. JSA</div>

    {{-- ✅ Pengawas diambil dari daftar pekerja yang role-nya Pengawas --}}
    @php
        $pengawas = collect($pekerjaDaftar)
            ->firstWhere('role', 'Pengawas');
        $namaPengawas = $pengawas['nama'] ?? ($surat->nama ?? '—');
    @endphp

    <table class="meta">
        <tr>
            <td class="label">No JSA</td>
            <td class="colon">:</td>
            <td>{{ $surat->no_pekerja ?? '—' }}</td>
        </tr>
        <tr>
            <td class="label">Nama Pekerjaan</td>
            <td class="colon">:</td>
            <td><strong>{{ $surat->jenis_pekerjaan ?? '—' }}</strong></td>
        </tr>
        <tr>
            <td class="label">Pengawas</td>
            <td class="colon">:</td>
            <td>{{ $namaPengawas }}</td>
        </tr>
        <tr>
            <td class="label">APD</td>
            <td class="colon">:</td>
            <td>{{ $surat->apd ?? '—' }}</td>
        </tr>
        <tr>
            <td class="label">Lokasi Pekerjaan</td>
            <td class="colon">:</td>
            <td>{{ $surat->lokasi_kerja ?? '—' }}</td>
        </tr>
        <tr>
            <td class="label">Periode</td>
            <td class="colon">:</td>
            <td>{{ $surat->periode ?? '—' }}</td>
        </tr>
    </table>

    <table class="jsa">
        <thead>
            <tr>
                <th style="width:30px;">NO</th>
                <th style="width:22%;">Urutan Kerja</th>
                <th style="width:37%;">Potensi Bahaya</th>
                <th style="width:37%;">Upaya Pengendalian</th>
            </tr>
        </thead>
        <tbody>
            @forelse($jsaItems as $idx => $item)
            <tr>
                <td style="text-align:center;">{{ $idx + 1 }}.</td>
                <td>{{ $item['urutan_kerja'] ?? '—' }}</td>
                <td>
                    @foreach(array_filter(explode("\n", trim($item['potensi_bahaya'] ?? ''))) as $p)
                    ; {{ trim($p) }}<br>
                    @endforeach
                </td>
                <td>
                    @foreach(array_filter(explode("\n", trim($item['upaya_pengendalian'] ?? ''))) as $u)
                    ; {{ trim($u) }}<br>
                    @endforeach
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" style="text-align:center; color:#999; padding:12px;">
                    Belum ada data JSA
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- ✅ TTD JSA dikosongkan — hanya header label, sel kosong --}}
    <table class="ttd-table">
        <thead>
            <tr>
                <th>Disusun</th>
                <th>Diperiksa</th>
                <th>Disetujui 1</th>
                <th>Disetujui 2</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        </tbody>
    </table>

    {{-- ══════════════════════════════════════ --}}
    {{-- PAGE BREAK → LAMPIRAN B               --}}
    {{-- ══════════════════════════════════════ --}}
    <div class="page-break"></div>

    {{-- ══════════════════════════════════════ --}}
    {{-- LAMPIRAN B: DAFTAR PEKERJA             --}}
    {{-- ══════════════════════════════════════ --}}

    <div class="section-title">B. Daftar Pekerja</div>

    <table class="meta">
        <tr>
            <td class="label">No Daftar Pekerja</td>
            <td class="colon">:</td>
            <td>{{ $surat->no_pekerja ?? '—' }}</td>
        </tr>
        <tr>
            <td class="label">Nama Pekerjaan</td>
            <td class="colon">:</td>
            <td><strong>{{ $surat->jenis_pekerjaan ?? '—' }}</strong></td>
        </tr>
        <tr>
            <td class="label">Pengawas</td>
            <td class="colon">:</td>
            <td>{{ $namaPengawas }}</td>
        </tr>
        <tr>
            <td class="label">Lokasi Pekerjaan</td>
            <td class="colon">:</td>
            <td>{{ $surat->lokasi_kerja ?? '—' }}</td>
        </tr>
        <tr>
            <td class="label">Periode</td>
            <td class="colon">:</td>
            <td>{{ $surat->periode ?? '—' }}</td>
        </tr>
    </table>

    <table class="pekerja">
        <thead>
            <tr>
                <th style="width:40px;">NO</th>
                <th>NAMA PEKERJA</th>
                <th>ID CARD</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pekerjaDaftar as $idx => $pekerja)
            @php $ktpImg = $ktpBase64List[$idx] ?? null; @endphp
            <tr>
                <td class="no">{{ $idx + 1 }}</td>
                <td class="nama">
                    {{ $pekerja['nama'] ?? '—' }}
                    @if(!empty($pekerja['role']))({{ $pekerja['role'] }})@endif
                </td>
                <td class="ktp">
                    @if($ktpImg)
                    <img src="{{ $ktpImg }}" />
                    @else
                    <div style="height:100px; border:1px dashed #ccc;"></div>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="3" style="text-align:center; color:#999; padding:20px;">
                    Belum ada data pekerja
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>