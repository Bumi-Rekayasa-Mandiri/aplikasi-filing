<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FileUpload extends Model
{
    protected $table = 'file_uploads';

    protected $fillable = [
        'original_name',
        'stored_name',
        'file_path',
        'path',
        'extension',
        'mime_type',
        'size',
        'source', // local | s3 | gdrive (future)
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    // Jika satu file bisa dipakai oleh banyak konteks
    // gunakan relasi polymorphic (opsional, future-proof)
    public function uploadable()
    {
        return $this->morphTo();
    }

}