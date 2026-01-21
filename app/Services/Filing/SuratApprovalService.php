<?php

namespace App\Service\Filing;

use App\Models\Surat;
use Illuminate\Support\Facades\DB;

class SuratApprovalService
{
    public function submit(Surat $surat): Surat
    {
        return DB::transaction(function () use ($surat)
        { $surat->update(['status' => Surat::STATUS_SUBMITTED,
        ]);

        return $surat;

        });
    }

    public function approve(Surat $surat): Surat
    {
        return DB::transaction(function () use ($surat)
        { $surat->update(['status' => Surat::STATUS_APPROVED,
        ]);

        return $surat;

    });
    }

    public function reject(Surat $surat): Surat
    {
        return DB::transaction(function () use ($surat)
        { $surat->update(['status' => Surat::STATUS_REJECTED,
        ]);

        return $surat;

        });
    }
}