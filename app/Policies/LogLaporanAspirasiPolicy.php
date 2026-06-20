<?php

namespace App\Policies;

use App\Models\LogLaporanAspirasi;
use App\Models\User;
use App\Enums\PermissionEnum;
use Illuminate\Auth\Access\Response;

class LogLaporanAspirasiPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo(PermissionEnum::VIEW_COMPLAINTS);
    }

    public function view(User $user, LogLaporanAspirasi $complaint): bool
    {
        return $user->hasPermissionTo(PermissionEnum::VIEW_COMPLAINTS);
    }

    public function create(User $user): bool
    {
        // Actually citizens create complaints via their NIK without needing system user account.
        // But if an admin inputs it on their behalf:
        return $user->hasPermissionTo(PermissionEnum::UPDATE_COMPLAINTS);
    }

    public function update(User $user, LogLaporanAspirasi $complaint): bool
    {
        return $user->hasPermissionTo(PermissionEnum::UPDATE_COMPLAINTS);
    }

    public function delete(User $user, LogLaporanAspirasi $complaint): bool
    {
        return $user->hasPermissionTo(PermissionEnum::UPDATE_COMPLAINTS);
    }
}
