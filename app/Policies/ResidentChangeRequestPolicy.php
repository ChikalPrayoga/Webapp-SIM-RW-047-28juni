<?php

namespace App\Policies;

use App\Models\ResidentChangeRequest;
use App\Models\User;
use App\Enums\PermissionEnum;

class ResidentChangeRequestPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo(PermissionEnum::VIEW_RESIDENTS);
    }

    public function view(User $user, ResidentChangeRequest $residentChangeRequest): bool
    {
        return $user->hasPermissionTo(PermissionEnum::VIEW_RESIDENTS);
    }

    public function create(User $user): bool
    {
        return true; // Warga can create
    }

    public function update(User $user, ResidentChangeRequest $residentChangeRequest): bool
    {
        return $user->hasPermissionTo(PermissionEnum::APPROVE_RESIDENT_CHANGES);
    }
}
