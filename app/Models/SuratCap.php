<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuratCap extends Model
{
    protected $table = 'surat_cap';

    protected $fillable = [
        'surat_id',
        'file_upload_id',
    ];

    public function surat()
    {
        return $this->belongsTo(Surat::class);
    }

    public function file()
    {
        return $this->belongsTo(FileUpload::class, 'file_upload_id');
    }
}
