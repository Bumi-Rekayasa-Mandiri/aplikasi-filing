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
        'urutan',
        'label',
    ];

    public function registerMediaCollections():void
    {
        $this->addMediaCollection('ttd')->singleFile();
    }

    protected $appends = ['url'];

    public function surat(): BelongsTo
    {
        return $this->belongsTo(Surat::class);
    }

    // Helper: ambil URL gambar TTD
    public function getTtdUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('ttd') ?: null;
    }

    // Helper: ambil path untuk DomPDF
    public function getTtdPathAttribute(): ?string
    {
        return $this->getFirstMediaPath('ttd') ?: null;
    }
}