<?php

namespace App\Services;

use App\Models\Surat;
use App\Services\NomorSuratGenerator;
use Illuminate\Support\Facades\DB;

class SuratService
{
    public function create(array $data): Surat
    {
        return DB::transaction(function () use ($data) {
            $generator = app(NomorSuratGenerator::class);
            $nomor = $generator->generate($data['kode_jenis'] ?? null);

            return Surat::create([
                'judul'        => $data['judul'],
                'nomor_surat'  => $nomor['nomor_surat'],
                'tanggal_surat'=> now(),
                'perihal'      => $data['perihal'],
                'tujuan'       => $data['tujuan'],
                'isi_surat'    => $data['isi_surat'],
                'status'       => 'draft',
            ]);
        });
    }
}
