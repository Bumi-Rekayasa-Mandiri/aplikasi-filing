<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NomorSuratLog extends Model
{
    protected $table = 'nomor_surat_logs';

    protected $fillable = [
        'surat_id',
        'running_number',
        'nomor_surat',
        'tahun',
        'bulan',
        'kode_jenis',
    ];

    public function surat()
    {
        return $this->belongsTo(Surat::class);
    }
}
