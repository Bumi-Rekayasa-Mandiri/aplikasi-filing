<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\NomorSuratLog;
use App\Models\SuratCap;
use App\Models\SuratTtd;

class Surat extends Model
{
    protected $table = 'surat';

    protected $fillable = [
        'judul',
        'nomor_surat',
        'tanggal_surat',
        'perihal',
        'tujuan',
        'isi_surat',
        'status',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

public function nomorSuratLogs()
{
    return $this->hasMany(NomorSuratLog::class);
}

public function cap()
{
    return $this->hasOne(SuratCap::class);
}

public function ttds()
{
    return $this->hasMany(SuratTtd::class);
}

/* ini komen
public function files()
{
    return $this->hasMany(FileUpload::class);
}
samapi sini */ 

}
