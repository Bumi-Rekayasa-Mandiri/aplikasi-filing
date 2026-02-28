<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
// use Spatie\MediaLibrary\HasMedia;
// use Spatie\MediaLibrary\InteractsWithMedia;

class SuratRecentView extends Model //implements HasMedia
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'surat_id',
        'last_viewed_at',
    ];

    protected $casts = [
        'last_viewed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function surat()
    {
        return $this->belongsTo(Surat::class)->withTrashed();
    }
}