<?php

namespace App\Policies;

use App\Models\KartuKeluarga;
use App\Models\User;
use App\Enums\PermissionEnum;

class KartuKeluargaPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo(PermissionEnum::VIEW_RESIDENTS);
    }

    public function view(User $user, KartuKeluarga $kartuKeluarga): bool
    {
        return $user->hasPermissionTo(PermissionEnum::VIEW_RESIDENTS);
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo(PermissionEnum::CREATE_RESIDENTS);
    }

    public function update(User $user, KartuKeluarga $kartuKeluarga): bool
    {
        return $user->hasPermissionTo(PermissionEnum::EDIT_RESIDENTS);
    }

    public function delete(User $user, KartuKeluarga $kartuKeluarga): bool
    {
        return $user->hasPermissionTo(PermissionEnum::DELETE_RESIDENTS);
    }
}
