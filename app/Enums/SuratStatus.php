<?php

namespace App\Enums;

enum SuratStatus: string
{
    case DRAFT = 'draft';
    case APPROVED = 'approved';


    public function label (): string
    {
        return match ($this){
            self::DRAFT => 'Draft',
            self::APPROVED => 'Approved',
        };
    }
}