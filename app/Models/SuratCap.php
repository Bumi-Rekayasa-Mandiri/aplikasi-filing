<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class SuratCap extends Model implements HasMedia
{

    use InteractsWithMedia;

    protected $table = 'surat_cap';

    protected $fillable = [
        'surat_id',
        'file_upload_id',
    ];

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('cap')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 
                                'image/png', 
                                'application/pdf',
                            ]);
    }

    public function surat()
    {
        return $this->belongsTo(Surat::class);
    }

    public function file()
    {
        return $this->belongsTo(FileUpload::class, 'file_upload_id');
    }
}