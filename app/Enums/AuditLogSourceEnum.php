<?php

namespace App\Enums;

enum AuditLogSourceEnum: string
{
    case WEB = 'WEB';
    case TELEGRAM = 'TELEGRAM';
    case SYSTEM = 'SYSTEM';
    case AI = 'AI';
}
