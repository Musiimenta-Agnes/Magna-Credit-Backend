<?php

namespace App\Policies;

use App\Models\User;
use App\Models\LoanApplication;

class LoanApplicationPolicy
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
    public function view(User $user, LoanApplication $loanApplication): bool
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
    public function update(User $user, LoanApplication $loanApplication): bool
    {
        return $user->hasAnyRole(['super_admin', 'admin']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, LoanApplication $loanApplication): bool
    {
        return $user->hasAnyRole(['super_admin', 'admin']);
    }
}
