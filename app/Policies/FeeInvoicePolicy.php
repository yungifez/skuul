<?php

namespace App\Policies;

use App\Models\FeeInvoice;
use App\Models\User;

class FeeInvoicePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user)
    {
        if ($user->can('read fee invoice') && !$this->isPortalOnly($user)) {
            return true;
        }
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, FeeInvoice $feeInvoice)
    {
        if (!$user->can('read fee invoice') || $feeInvoice->school_id !== current_school_id()) {
            return false;
        }

        if ($user->hasRole('student')) {
            return $feeInvoice->student_record_id === $user->studentRecord?->id;
        }

        if ($user->hasRole('parent')) {
            return $feeInvoice->studentRecord?->user?->parents()
                ->where('parent_records.user_id', $user->id)
                ->exists() === true;
        }

        if ($feeInvoice->school_id === current_school_id()) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user)
    {
        if ($user->can('create fee invoice')) {
            return true;
        }
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, FeeInvoice $feeInvoice)
    {
        if ($user->can('update fee invoice') && $feeInvoice->school_id === current_school_id()) {
            return true;
        }
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, FeeInvoice $feeInvoice)
    {
        if ($user->can('delete fee invoice') && $feeInvoice->school_id === current_school_id()) {
            return true;
        }
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, FeeInvoice $feeInvoice)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, FeeInvoice $feeInvoice)
    {
        //
    }

    /**
     * Portal roles read invoices from a child-scoped portal screen.
     */
    private function isPortalOnly(User $user): bool
    {
        $roles = collect($user->getRoleNames());

        return $roles->intersect(['parent', 'student'])->isNotEmpty()
            && $roles->diff(['parent', 'student'])->isEmpty();
    }
}
