<?php

namespace App\Enums;

enum ComplaintStatusEnum: string
{
    case SUBMITTED = 'SUBMITTED';
    case CLASSIFIED = 'CLASSIFIED';
    case IN_PROGRESS = 'IN_PROGRESS';
    case RESOLVED = 'RESOLVED';
    case CLOSED = 'CLOSED';
}
