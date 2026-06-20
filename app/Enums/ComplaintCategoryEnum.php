<?php

namespace App\Enums;

enum ComplaintCategoryEnum: string
{
    case INFRASTRUCTURE = 'INFRASTRUCTURE';
    case ADMINISTRATIVE = 'ADMINISTRATIVE';
    case SECURITY = 'SECURITY';
    case ENVIRONMENT = 'ENVIRONMENT';
    case UNCATEGORIZED = 'UNCATEGORIZED';
}
