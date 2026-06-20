<?php

namespace App\Enums;

enum LetterTypeEnum: string
{
    case SURAT_PENGANTAR = 'SURAT_PENGANTAR';
    case SURAT_KETERANGAN_DOMISILI = 'SURAT_KETERANGAN_DOMISILI';

    public function requiresRwApproval(): bool
    {
        return match($this) {
            self::SURAT_PENGANTAR => true,
            self::SURAT_KETERANGAN_DOMISILI => false,
        };
    }
}
