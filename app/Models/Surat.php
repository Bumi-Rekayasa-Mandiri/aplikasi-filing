<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\NomorSuratLog;
use App\Models\SuratCap;
use App\Models\SuratTtd;
use App\Enums\SuratStatus;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;  
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Surat extends Model implements HasMedia
{
    use InteractsWithMedia, SoftDeletes;

    const STATUS_DRAFT = 'draft';
    const STATUS_APPROVED = 'approved';

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
        'nama',
        'jabatan_terakhir',
        'departemen',
        'project',
        'material',
        'alamat',
        'masa_garansi',
        'no_ktp',
        'nominal',
        'nominal_bagihasil',
        'hasil_denda',
        'keringanan_denda',
        'lampiran',
        'item_pembelian',
        'merk',
        'warna',
        'rangka',
        'gambar_materai',
        'lokasi_kerja',
        'jenis_pekerjaan',
        'waktu',
        'jam_kerja',
        'jumlah_pekerja',
        'apd',
        'periode',
        'no_pekerja',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('cap')
            ->singleFile();

        // $this->addMediaCollection('ttd');
    }

    public function nomorSuratLogs()
    {
        return $this->hasMany (NomorSuratLog::class);
    }

    public function cap()
    {
        return $this->hasOne(SuratCap::class);
    }

    public function ttds(): HasMany
    {
        return $this->hasMany(SuratTtd::class)->orderBy('urutan');
    }

    public function finalize(): void
    {

        if (!$this->nomor_surat) {

            $generator = app(\App\Services\NomorSuratGenerator::class);

            $result = $generator->generateForSurat($this);

            $this->nomor_surat = $result['nomor_surat'];
        }

        $this->status = 'approved';
        $this->save();
    }
}