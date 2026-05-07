<?php

namespace App\Policies;

use App\Models\Surat;
use App\Models\User;
use App\Enums\SuratStatus;

class SuratPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Surat $surat): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Allow membuka halaman Edit untuk DRAFT (full edit) dan APPROVED
     * (read-only mode — actual save tetap diblok di controller@update).
     */
    public function update(User $user, Surat $surat): bool
    {
        return in_array($surat->status, [
            SuratStatus::DRAFT,
            SuratStatus::APPROVED,
        ]);
    }

    public function uploadPdf(User $user, Surat $surat): bool
    {
        return $surat->status === SuratStatus::DRAFT;
    }

    public function delete(User $user, Surat $surat): bool
    {
        return true;
    }

    public function restore(User $user, Surat $surat): bool
    {
        return true;
    }

    public function forceDelete(User $user, Surat $surat): bool
    {
        return true;
    }

    /**
     * Single-user workflow: tidak ada role check.
     * Approve hanya valid kalau status DRAFT (mencegah double-approve).
     */
    public function approve(User $user, Surat $surat): bool
    {
        return $surat->status === SuratStatus::DRAFT;
    }

    /**
     * Single-user workflow: tidak ada role check.
     * Revert hanya valid kalau status APPROVED.
     */
    public function revertDraft(User $user, Surat $surat): bool
    {
        return $surat->status === SuratStatus::APPROVED;
    }
}