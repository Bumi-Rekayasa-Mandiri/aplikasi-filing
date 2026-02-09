<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\NomorSuratLog;
use App\Models\SuratCap;
use App\Models\SuratTtd;
use App\Enums\SuratStatus;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Surat extends Model implements HasMedia
{
    use InteractsWithMedia, SoftDeletes;

    const STATUS_DRAFT = 'draft';
    const STATUS_SUBMITTED = 'submitted';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    protected $table = 'surat';

     protected $casts = [
        'status' => SuratStatus::class,
    ];

    protected $fillable = [
        'judul',
        'nomor_surat',
        'tanggal_surat',
        'perihal',
        'tujuan',
        'isi_surat',
        'status',
        'created_by',
        'jenis',
        'pdf_path',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('cap')
            ->singleFile();

        $this->addMediaCollection('ttd');
    }

    public function nomorSuratLogs()
    {
        return $this->hasMany (NomorSuratLog::class);
    }

    public function cap ()
    {
        return $this->hasOne(SuratCap::class);
    }

    public function ttds()
    {
        return $this->hasMany(SuratTtd::class);
    }

    public function finalize(): void
    {
        if ($this->status !== 'draft') {
            return;
        }

        // generate nomor surat jika belum ada
        if (!$this->nomor_surat) {
            $this->nomor_surat = app(\App\Services\NomorSuratGenerator::class)
                ->generate($this);
        }

        $this->status = 'final';
        $this->save();
    }

}