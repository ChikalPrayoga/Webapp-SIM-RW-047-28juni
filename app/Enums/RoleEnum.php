<?php

namespace App\Enums;

enum RoleEnum: string
{
    case SUPER_ADMIN = 'SUPER_ADMIN';
    case KETUA_RW = 'KETUA_RW';
    case SEKRETARIS_RW = 'SEKRETARIS_RW';
    case BENDAHARA_RW = 'BENDAHARA_RW';
    case KETUA_RT = 'KETUA_RT';
    case WARGA = 'WARGA';
}
