<?php

namespace App\Policies;

use App\Models\ComplaintAssignment;
use App\Models\User;
use App\Enums\PermissionEnum;

class ComplaintAssignmentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo(PermissionEnum::VIEW_COMPLAINTS);
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo(PermissionEnum::UPDATE_COMPLAINTS);
    }

    public function delete(User $user, ComplaintAssignment $complaintAssignment): bool
    {
        return $user->hasPermissionTo(PermissionEnum::UPDATE_COMPLAINTS);
    }
}
