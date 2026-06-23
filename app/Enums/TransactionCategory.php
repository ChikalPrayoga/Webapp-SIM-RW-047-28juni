<?php

namespace App\Enums;

enum TransactionCategory: string
{
    case IURAN = 'IURAN';
    case DONASI = 'DONASI';
    case OPERASIONAL = 'OPERASIONAL';
    case ADJUSTMENT = 'ADJUSTMENT';
    case LAINNYA = 'LAINNYA';
}
