<?php

namespace App\Enums;

enum TransactionType: string
{
    case INCOME = 'INCOME';
    case EXPENSE = 'EXPENSE';
}
