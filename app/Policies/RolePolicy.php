<?php

namespace App\Policies;

use App\Models\Role;
use App\Models\User;
use App\Enums\PermissionEnum;

class RolePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo(PermissionEnum::MANAGE_SYSTEM);
    }

    public function view(User $user, Role $role): bool
    {
        return $user->hasPermissionTo(PermissionEnum::MANAGE_SYSTEM);
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo(PermissionEnum::MANAGE_SYSTEM);
    }

    public function update(User $user, Role $role): bool
    {
        return $user->hasPermissionTo(PermissionEnum::MANAGE_SYSTEM);
    }

    public function delete(User $user, Role $role): bool
    {
        return $user->hasPermissionTo(PermissionEnum::MANAGE_SYSTEM);
    }
}
