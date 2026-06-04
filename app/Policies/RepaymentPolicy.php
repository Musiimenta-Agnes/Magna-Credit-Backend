<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Repayment;

class RepaymentPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'admin', 'loans_officer']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Repayment $repayment): bool
    {
        return $user->hasAnyRole(['super_admin', 'admin', 'loans_officer']);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'admin']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Repayment $repayment): bool
    {
        return $user->hasAnyRole(['super_admin', 'admin']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Repayment $repayment): bool
    {
        return $user->hasAnyRole(['super_admin', 'admin']);
    }
}
