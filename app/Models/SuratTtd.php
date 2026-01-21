<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class SuratTtd extends Model implements HasMedia
{

    use InteractsWithMedia;
    
    protected $fillable = [
        'surat_id',
        'nama_penandatangan',
        'jabatan',
    ];

    public function surat()
    {
        return $this->belongsTo(Surat::class);
    }

    public function getUrlAttribute()
    {
        return $this->getFirstMediaUrl('ttd');
    }
}