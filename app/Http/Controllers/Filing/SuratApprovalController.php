<?php

namespace App\Http\Controllers\Filing;

use App\Http\Controllers\Controller;
use App\Models\Surat;
use App\Models\SuratApprovalLog;
use App\Enums\SuratStatus;
use Illuminate\Support\Facades\DB;

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

        DB::transaction(function () use ($surat) {
            $surat->update(['status' => SuratStatus::APPROVED]);
        });

        SuratApprovalLog::create([
            'surat_id' => $surat->id,
            'action' => 'approved',
            'user_id' => auth()->id(),
        ]);

        return back()->with('success', 'Surat disetujui');
    }

    public function reject(Surat $surat)
    {
        $this->authorize('reject', $surat);
        
        DB::transaction(function () use ($surat) {
            $surat->update(['status' => SuratStatus::REJECTED]);
        });

        SuratApprovalLog::create([
            'surat_id' => $surat->id,
            'action' => 'rejected',
            'user_id' => auth()->id(),
        ]);
        
        return back()->with('success', 'Surat ditolak');
    }

    public function revertDraft(Surat $surat)
    {
        $this->authorize('revertDraft', $surat);

        $surat->update(['status' => SuratStatus::DRAFT]);

        return back()->with('success', 'Status surat dikembalikan ke draft.');
    }

}