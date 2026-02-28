<?php

namespace App\SuratTypes\Contracts;

use App\Models\Surat;

interface SuratTypeInterface
{
    public function rules(): array;

    public function viewCreate(): string;

    public function viewPreview(): string;

    public function generatePdf(Surat $surat);
}