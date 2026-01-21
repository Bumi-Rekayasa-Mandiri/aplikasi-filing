<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class ArsipSurat extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'judul',
        'nomor_surat',
        'tahun',
        'kategori',
        'keterangan',
    ];
}