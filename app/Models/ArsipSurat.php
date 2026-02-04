<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class ArsipSurat extends Model implements HasMedia
{
    use InteractsWithMedia, SoftDeletes;

    protected $fillable = [
        'judul',
        'nomor_surat',
        'tujuan',
        'jenis_surat',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('arsip_surat_files')
            ->useDisk('public')
            ->singleFile();
    }

    protected static function booted()
    {
    static::deleting(function ($model) {
        if ($model->isForceDeleting()) {
            $model->clearMediaCollection();
        }
    });
    }
}