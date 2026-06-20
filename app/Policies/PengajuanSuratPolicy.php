<?php

namespace App\Policies;

use App\Models\PengajuanSurat;
use App\Models\User;
use App\Enums\PermissionEnum;
use App\Enums\RoleEnum;
use App\Enums\LetterStatusEnum;

class PengajuanSuratPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo(PermissionEnum::VIEW_LETTERS);
    }

    public function view(User $user, PengajuanSurat $pengajuanSurat): bool
    {
        if (!$user->hasPermissionTo(PermissionEnum::VIEW_LETTERS)) {
            return false;
        }

        // Super Admin, RW, and Sekretaris can view any letter
        if (
            $user->role->role_name === RoleEnum::SUPER_ADMIN->value ||
            $user->role->role_name === RoleEnum::KETUA_RW->value ||
            $user->role->role_name === RoleEnum::SEKRETARIS_RW->value
        ) {
            return true;
        }

        // RT can only view letters from their own RT
        if ($user->role->role_name === RoleEnum::KETUA_RT->value) {
            $userArea = $user->position ? $user->position->area_code : null;
            $letterArea = $pengajuanSurat->pemohon && $pengajuanSurat->pemohon->kartuKeluarga
                ? $pengajuanSurat->pemohon->kartuKeluarga->rt_code
                : null;
            
            return $userArea !== null && $userArea === $letterArea;
        }

        return false;
    }

    public function process(User $user, PengajuanSurat $pengajuanSurat): bool
    {
        if (!$user->hasPermissionTo(PermissionEnum::APPROVE_RT_LETTERS)) {
            return false;
        }
        
        if ($pengajuanSurat->current_status !== LetterStatusEnum::SUBMITTED) {
            return false;
        }
        
        // Ensure it's for their RT
        $userArea = $user->position ? $user->position->area_code : null;
        $letterArea = $pengajuanSurat->pemohon && $pengajuanSurat->pemohon->kartuKeluarga
            ? $pengajuanSurat->pemohon->kartuKeluarga->rt_code
            : null;
            
        return $userArea !== null && $userArea === $letterArea;
    }

    public function forwardToRw(User $user, PengajuanSurat $pengajuanSurat): bool
    {
        if (!$user->hasPermissionTo(PermissionEnum::APPROVE_RT_LETTERS)) {
            return false;
        }
        
        if ($pengajuanSurat->current_status !== LetterStatusEnum::RT_REVIEW) {
            return false;
        }
        
        // Ensure it's for their RT
        $userArea = $user->position ? $user->position->area_code : null;
        $letterArea = $pengajuanSurat->pemohon && $pengajuanSurat->pemohon->kartuKeluarga
            ? $pengajuanSurat->pemohon->kartuKeluarga->rt_code
            : null;
            
        return $userArea !== null && $userArea === $letterArea;
    }

    public function complete(User $user, PengajuanSurat $pengajuanSurat): bool
    {
        if (!$user->hasPermissionTo(PermissionEnum::COMPLETE_LETTERS)) {
            return false;
        }

        // Valid endpoints for completion depending on letter type routing:
        // - RT_REVIEW for RT-only letters
        // - RW_REVIEW for RT+RW letters
        $isValidState = in_array($pengajuanSurat->current_status, [
            LetterStatusEnum::RT_REVIEW,
            LetterStatusEnum::RW_REVIEW,
        ]);

        if (!$isValidState) {
            return false;
        }

        // If it's RT_REVIEW, the user must be the RT
        if ($pengajuanSurat->current_status === LetterStatusEnum::RT_REVIEW) {
            if ($user->role->role_name !== RoleEnum::KETUA_RT->value) {
                return false;
            }
            $userArea = $user->position ? $user->position->area_code : null;
            $letterArea = $pengajuanSurat->pemohon && $pengajuanSurat->pemohon->kartuKeluarga
                ? $pengajuanSurat->pemohon->kartuKeluarga->rt_code
                : null;
            return $userArea !== null && $userArea === $letterArea;
        }

        // If it's RW_REVIEW, the user must be the RW
        if ($pengajuanSurat->current_status === LetterStatusEnum::RW_REVIEW) {
            return $user->role->role_name === RoleEnum::KETUA_RW->value;
        }

        return false;
    }

    public function reject(User $user, PengajuanSurat $pengajuanSurat): bool
    {
        // Can only reject if it's not completed and not already rejected
        $isRejectable = in_array($pengajuanSurat->current_status, [
            LetterStatusEnum::SUBMITTED,
            LetterStatusEnum::RT_REVIEW,
            LetterStatusEnum::RW_REVIEW,
        ]);

        if (!$isRejectable) {
            return false;
        }

        // If it's SUBMITTED or RT_REVIEW, RT or RW can reject, but RT must own it
        if (in_array($pengajuanSurat->current_status, [LetterStatusEnum::SUBMITTED, LetterStatusEnum::RT_REVIEW])) {
            if ($user->role->role_name === RoleEnum::KETUA_RT->value) {
                $userArea = $user->position ? $user->position->area_code : null;
                $letterArea = $pengajuanSurat->pemohon && $pengajuanSurat->pemohon->kartuKeluarga
                    ? $pengajuanSurat->pemohon->kartuKeluarga->rt_code
                    : null;
                return $userArea !== null && $userArea === $letterArea;
            }
            // RW can theoretically reject early, though UI usually hides it
            return $user->role->role_name === RoleEnum::KETUA_RW->value;
        }

        // If it's RW_REVIEW, only RW can reject
        if ($pengajuanSurat->current_status === LetterStatusEnum::RW_REVIEW) {
            return $user->role->role_name === RoleEnum::KETUA_RW->value;
        }

        return false;
    }
}
