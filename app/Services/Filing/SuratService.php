<?php

namespace App\Services\Filing;

use App\Models\Surat;
use App\Models\NomorSuratLog;
use App\Services\NomorSuratGenerator;
use Illuminate\Support\Facades\DB;

class SuratService
{
    public function create(array $data): Surat
    {
        return DB::transaction(function () use ($data) {

            $generator = app(NomorSuratGenerator::class);
            $nomor     = $generator->generate($data['kode_jenis']);

            $surat = Surat::create([
                'user_id'       => auth()->id(),
                'judul'         => $data['judul'],
                'perihal'       => $data['perihal'],
                'tujuan'        => $data['tujuan'],
                'isi_surat'     => $data['isi_surat'],
                'tanggal_surat' => $data['tanggal_surat'],
                'nomor_surat'   => $nomor['nomor_surat'],
                'status'        => 'draft',
            ]);

            // 🔗 LINK surat_id ke log
            NomorSuratLog::where('id', $nomor['log_id'])
                ->update(['surat_id' => $surat->id]);

            return $surat;
        });
    }
}