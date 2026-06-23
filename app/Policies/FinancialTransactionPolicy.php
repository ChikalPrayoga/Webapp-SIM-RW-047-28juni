<?php

namespace App\Policies;

use App\Models\FinancialTransaction;
use App\Models\User;
use App\Enums\PermissionEnum;
use App\Enums\TransactionCategory;

class FinancialTransactionPolicy
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
    public function view(User $user, FinancialTransaction $transaction): bool
    {
        if (!$user->hasPermissionTo(PermissionEnum::VIEW_FINANCES)) {
            return false;
        }

        $userArea = $user->position?->area_code;
        if (empty($userArea)) {
            // Bendahara RW, Ketua RW, dll. (Global access)
            return true;
        }

        // Ketua RT: Hanya transaksi RT sendiri atau Kas RW (rt_code === null)
        return $transaction->rt_code === $userArea || $transaction->rt_code === null;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo(PermissionEnum::MANAGE_FINANCES);
    }

    /**
     * Determine whether the user can reverse the transaction.
     */
    public function reverse(User $user, FinancialTransaction $transaction): bool
    {
        if (!$user->hasPermissionTo(PermissionEnum::MANAGE_FINANCES)) {
            return false;
        }

        $userArea = $user->position?->area_code;
        if (!empty($userArea) && $transaction->rt_code !== $userArea) {
            // Ketua RT hanya bisa mengoreksi transaksi milik RT sendiri
            return false;
        }

        // Transaksi hanya bisa dikoreksi jika adjusted_transaction_id bernilai NULL
        // dan kategori transaksi bukan ADJUSTMENT
        return $transaction->adjusted_transaction_id === null 
            && $transaction->category !== TransactionCategory::ADJUSTMENT;
    }

    /**
     * Determine whether the user can update the model.
     * Immutable: Disabled globally.
     */
    public function update(User $user, FinancialTransaction $transaction): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     * Immutable: Disabled globally.
     */
    public function delete(User $user, FinancialTransaction $transaction): bool
    {
        return false;
    }
}
