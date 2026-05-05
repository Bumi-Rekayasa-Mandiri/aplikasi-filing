<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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

    /**
     * Attribute virtual yang ikut serialize ke array/JSON.
     *
     * ✅ Pakai 'ttd_url' agar match dengan method getTtdUrlAttribute().
     *    Sebelumnya 'url' tidak punya method pasangannya → error.
     */
    protected $appends = ['ttd_url'];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('ttd')->singleFile();
    }

    public function surat(): BelongsTo
    {
        return $this->belongsTo(Surat::class);
    }

    /**
     * Accessor untuk attribute virtual 'ttd_url'.
     * Akan otomatis muncul di $ttd->ttd_url dan saat $ttd->toArray().
     */
    public function getTtdUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('ttd') ?: null;
    }

    /**
     * Helper: path absolut file TTD untuk dipakai DomPDF/PHPWord.
     * Tidak ikut serialize (tidak ada di $appends) — ini sengaja
     * karena path absolut bersifat internal dan tidak perlu dikirim ke frontend.
     */
    public function getTtdPathAttribute(): ?string
    {
        return $this->getFirstMediaPath('ttd') ?: null;
    }
}