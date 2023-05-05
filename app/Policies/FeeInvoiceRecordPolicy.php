<?php

namespace App\Policies;

use App\Models\FeeInvoiceRecord;
use App\Models\User;

class FeeInvoiceRecordPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user)
    {
        if ($user->can('read fee invoice record')) {
            return true;
        }
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, FeeInvoiceRecord $feeInvoiceRecord)
    {
        if ($user->can('read fee invoice') && $feeInvoiceRecord->feeInvoice->user->school_id == auth()->user()->school_id) {
            return true;
        }
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user)
    {
        if ($user->can('create fee invoice record')) {
            return true;
        }
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, FeeInvoiceRecord $feeInvoiceRecord)
    {
        if ($user->can('update fee invoice record') && $feeInvoiceRecord->feeInvoice->user->school_id == auth()->user()->school_id) {
            return true;
        }
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, FeeInvoiceRecord $feeInvoiceRecord)
    {
        if ($user->can('delete fee invoice record') && $feeInvoiceRecord->feeInvoice->user->school_id == auth()->user()->school_id) {
            return true;
        }
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, FeeInvoiceRecord $feeInvoiceRecord)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, FeeInvoiceRecord $feeInvoiceRecord)
    {
        //
    }
}
