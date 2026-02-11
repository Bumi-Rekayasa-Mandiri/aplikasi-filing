<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class ArsipSertifikat extends Model implements HasMedia
{
    use InteractsWithMedia, SoftDeletes;

    protected $fillable = [
        'nama_sertifikat',
        'nomor_sertifikat',
        'jenis_sertifikat',
        'instansi',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('arsip_sertifikat_files')
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