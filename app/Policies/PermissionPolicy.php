<?php

namespace App\Policies;

use App\Models\Permission;
use App\Models\User;
use App\Enums\PermissionEnum;

class PermissionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo(PermissionEnum::MANAGE_SYSTEM);
    }

    public function view(User $user, Permission $permission): bool
    {
        return $user->hasPermissionTo(PermissionEnum::MANAGE_SYSTEM);
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo(PermissionEnum::MANAGE_SYSTEM);
    }

    public function update(User $user, Permission $permission): bool
    {
        return $user->hasPermissionTo(PermissionEnum::MANAGE_SYSTEM);
    }

    public function delete(User $user, Permission $permission): bool
    {
        return $user->hasPermissionTo(PermissionEnum::MANAGE_SYSTEM);
    }
}
