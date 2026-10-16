<?php

namespace App\Policies;

use App\Models\FeeInvoiceRecord;
use App\Models\User;

class FeeInvoiceRecordPolicy
{
    public function viewAny(User $user)
    {
        if ($user->can('read fee invoice record')) {
            return true;
        }
    }

    public function view(User $user, FeeInvoiceRecord $feeInvoiceRecord)
    {
        if ($user->can('read fee invoice') && $feeInvoiceRecord->feeInvoice->school_id === current_school_id()) {
            return true;
        }
    }

    public function create(User $user)
    {
        if ($user->can('create fee invoice record')) {
            return true;
        }
    }

    public function update(User $user, FeeInvoiceRecord $feeInvoiceRecord)
    {
        if ($user->can('update fee invoice record') && $feeInvoiceRecord->feeInvoice->school_id === current_school_id()) {
            return true;
        }
    }

    public function delete(User $user, FeeInvoiceRecord $feeInvoiceRecord)
    {
        if ($user->can('delete fee invoice record') && $feeInvoiceRecord->feeInvoice->school_id === current_school_id()) {
            return true;
        }
    }

    public function restore(User $user, FeeInvoiceRecord $feeInvoiceRecord)
    {
        //
    }

    public function forceDelete(User $user, FeeInvoiceRecord $feeInvoiceRecord)
    {
        //
    }
}
