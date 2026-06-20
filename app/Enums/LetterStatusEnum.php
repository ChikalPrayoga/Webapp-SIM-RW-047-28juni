<?php

namespace App\Enums;

enum LetterStatusEnum: string
{
    case SUBMITTED = 'SUBMITTED';
    case RT_REVIEW = 'RT_REVIEW';
    case RW_REVIEW = 'RW_REVIEW';
    case COMPLETED = 'COMPLETED';
    case REJECTED = 'REJECTED';
}
