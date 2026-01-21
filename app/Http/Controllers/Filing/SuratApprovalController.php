<?php

namespace App\Http\Controllers\Filing;

use App\Http\Controllers\Controller;
use App\Models\Surat;

class SuratApprovalController extends Controller
{
      
   public function submit(Surat $surat)
    {
        $this->authorize('submit', $surat);

        app(\App\Services\Filing\SuratApprovalService::class)->submit($surat);

        return back()->with('success', 'Surat diajukan untuk approval');
    }

    public function approve(Surat $surat)
    {
        $this->authorize('approve', $surat);

        app(\App\Services\Filing\SuratApprovalService::class)->approve($surat);

        return back()->with('success', 'Surat disetujui');
    }

    public function reject(Surat $surat)
    {
        $this->authorize('reject', $surat);
        
        app(\App\Services\Filing\SuratApprovalService::class)->reject($surat);

        return back()->with('success', 'Surat ditolak');
    }

}