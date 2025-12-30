<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuratTtd extends Model
{
    protected $table = 'surat_ttd';

    protected $fillable = [
        'surat_id',
        'file_upload_id',
        'nama_penandatangan',
        'jabatan_penandatangan',
        'urutan',
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
