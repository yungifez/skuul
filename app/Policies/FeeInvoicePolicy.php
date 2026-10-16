<?php

namespace App\Policies;

use App\Models\FeeInvoice;
use App\Models\User;

class FeeInvoicePolicy
{
    public function viewAny(User $user)
    {
        if ($user->can('read fee invoice') && !$this->isPortalOnly($user)) {
            return true;
        }
    }

    public function view(User $user, FeeInvoice $feeInvoice)
    {
        if (!$user->can('read fee invoice') || $feeInvoice->school_id !== current_school_id()) {
            return false;
        }

        if ($user->readsLearnersOnlyAsThemself()) {
            return $feeInvoice->student_record_id === $user->studentRecord?->id;
        }

        if ($user->readsLearnersOnlyAsGuardian()) {
            return $feeInvoice->studentRecord?->user?->parents()
                ->where('parent_records.user_id', $user->id)
                ->exists() === true;
        }

        if ($feeInvoice->school_id === current_school_id()) {
            return true;
        }

        return false;
    }

    public function create(User $user)
    {
        if ($user->can('create fee invoice')) {
            return true;
        }
    }

    public function update(User $user, FeeInvoice $feeInvoice)
    {
        if ($user->can('update fee invoice') && $feeInvoice->school_id === current_school_id()) {
            return true;
        }
    }

    public function delete(User $user, FeeInvoice $feeInvoice)
    {
        if ($user->can('delete fee invoice') && $feeInvoice->school_id === current_school_id()) {
            return true;
        }
    }

    public function restore(User $user, FeeInvoice $feeInvoice)
    {
        //
    }

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
