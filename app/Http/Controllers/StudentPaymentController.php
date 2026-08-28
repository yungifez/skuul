<?php

namespace App\Http\Controllers;

use App\Models\StudentPayment;
use App\Services\Print\PrintService;
use Illuminate\Http\Response;

class StudentPaymentController extends Controller
{
    public function print(StudentPayment $studentPayment): Response
    {
        abort_unless(
            auth()->user()?->can('read fee invoice') === true
                && $studentPayment->school_id === current_school_id(),
            403,
        );

        $studentPayment->loadMissing(['studentRecord.user', 'allocations.feeInvoice', 'recordedBy', 'financialPeriod']);

        return PrintService::page('pages.fee.payment.print', compact('studentPayment'));
    }
}
