<?php

namespace App\Policies;

use App\Models\CatatanIuranWarga;
use App\Models\User;
use App\Enums\PermissionEnum;

class CatatanIuranWargaPolicy
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
    public function view(User $user, CatatanIuranWarga $catatan): bool
    {
        if (!$user->hasPermissionTo(PermissionEnum::VIEW_FINANCES)) {
            return false;
        }

        $userArea = $user->position?->area_code;
        if (empty($userArea)) {
            // Bendahara RW, Ketua RW (Global access)
            return true;
        }

        // Ketua RT: Hanya iuran milik KK di RT sendiri
        return $catatan->kartuKeluarga && $catatan->kartuKeluarga->rt_code === $userArea;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo(PermissionEnum::MANAGE_FINANCES);
    }

    /**
     * Determine whether the user can verify (approve/reject) the contribution.
     * Exclusive for Bendahara RW (global write with no area code scope).
     */
    public function verify(User $user, CatatanIuranWarga $catatan): bool
    {
        return $user->hasPermissionTo(PermissionEnum::MANAGE_FINANCES) 
            && empty($user->position?->area_code);
    }

    /**
     * Determine whether the user can download the payment proof receipt.
     * Supports guest citizens via session.
     */
    public function downloadReceipt(?User $user, CatatanIuranWarga $catatan): bool
    {
        if ($user === null) {
            // Guest access via Portal Warga session check
            return session('verified_no_kk') === $catatan->no_kk;
        }

        if (!$user->hasPermissionTo(PermissionEnum::VIEW_FINANCES)) {
            return false;
        }

        $userArea = $user->position?->area_code;
        if (empty($userArea)) {
            return true; // Bendahara RW, Ketua RW
        }

        return $catatan->kartuKeluarga && $catatan->kartuKeluarga->rt_code === $userArea;
    }

    /**
     * Determine whether the user can update the model.
     * Immutable: Disabled globally.
     */
    public function update(User $user, CatatanIuranWarga $catatan): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     * Immutable: Disabled globally.
     */
    public function delete(User $user, CatatanIuranWarga $catatan): bool
    {
        return false;
    }
}
