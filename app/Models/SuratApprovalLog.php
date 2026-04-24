<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuratApprovalLog extends Model
{
    protected $fillable = ['surat_id', 'user_id', 'action'];
}
