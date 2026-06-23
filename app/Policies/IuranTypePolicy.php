<?php

namespace App\Policies;

use App\Models\IuranType;
use App\Models\User;
use App\Enums\PermissionEnum;

class IuranTypePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo(PermissionEnum::VIEW_FINANCES);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, IuranType $iuranType): bool
    {
        return $user->hasPermissionTo(PermissionEnum::VIEW_FINANCES);
    }

    /**
     * Determine whether the user can configure global iuran settings.
     * Centralized for Bendahara RW only (empty area_code).
     */
    public function manageGlobal(User $user): bool
    {
        return $user->hasPermissionTo(PermissionEnum::MANAGE_FINANCES) 
            && empty($user->position?->area_code);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $this->manageGlobal($user);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, IuranType $iuranType): bool
    {
        return $this->manageGlobal($user);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, IuranType $iuranType): bool
    {
        return $this->manageGlobal($user);
    }
}
