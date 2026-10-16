<?php

namespace App\Policies;

use App\Models\LibraryLoan;
use App\Models\User;

/**
 * Who may lend, take back, and renew.
 */
class LibraryLoanPolicy
{
    /**
     * Determine whether the user can see the loans.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('read library');
    }

    /**
     * Determine whether the user can see the loan.
     */
    public function view(User $user, LibraryLoan $loan): bool
    {
        return $user->can('read library') && $loan->school_id === current_school_id();
    }

    /**
     * Determine whether the user can work the lending desk.
     */
    public function create(User $user): bool
    {
        return $user->can('lend library item');
    }

    /**
     * Determine whether the user can take a copy back or renew it.
     */
    public function update(User $user, LibraryLoan $loan): bool
    {
        return $user->can('lend library item') && $loan->school_id === current_school_id();
    }
}
