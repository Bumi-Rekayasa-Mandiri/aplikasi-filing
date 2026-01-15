<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\NomorSuratLog;
use App\Models\SuratCap;
use App\Models\SuratTtd;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Surat extends Model implements HasMedia
{

    use InteractsWithMedia;
    use SoftDeletes;

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

    /**
     * Koleksi khusus untuk pdf surat
     */

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('pdf')
            ->useDisk('public')
            ->acceptsMimeTypes(['application/pdf'])
            ->singleFile();
        }

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


public function files()
{
    return $this->hasMany(FileUpload::class);
}
 
}
