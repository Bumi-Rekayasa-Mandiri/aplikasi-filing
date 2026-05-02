<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class ArsipSurat extends Model implements HasMedia
{
    use InteractsWithMedia, SoftDeletes;

    protected $fillable = [
        'surat_id',
        'tahun',
        'judul',
        'nomor_surat',
        'tujuan',
        'jenis_surat',
        'archived_at',
    ];

    protected $casts = [
        'tahun'       => 'integer',
        'archived_at' => 'datetime',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('arsip_surat_files')
            ->useDisk('public')
            ->singleFile();
    }

    public function surat(): BelongsTo
    {
        return $this->belongsTo(Surat::class);
    }

    public function scopeOfYear($query, int $tahun)
    {
        return $query->where('tahun', $tahun);
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