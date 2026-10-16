<?php

namespace App\Http\Controllers;

use App\Models\StudentPayment;
use App\Services\Portal\PortalAccess;
use App\Services\Print\PrintService;
use Illuminate\Http\Response;

class StudentPaymentController extends Controller
{
    public function print(StudentPayment $studentPayment): Response
    {
        abort_unless(
            auth()->user()?->can('read fee invoice') === true
                && $studentPayment->school_id === current_school_id()
                && (!auth()->user()->isPortalOnly() || $this->isTheirOwn($studentPayment)),
            403,
        );

        $studentPayment->loadMissing(['studentRecord.user', 'allocations.feeInvoice', 'recordedBy', 'financialPeriod']);

        return PrintService::page('pages.fee.payment.print', compact('studentPayment'));
    }

    /**
     * Check whether a family member is reading a receipt for their own learner.
     *
     * Parents and learners hold "read fee invoice" for their own bills, and a
     * receipt must not open for another family by its number.
     */
    private function isTheirOwn(StudentPayment $studentPayment): bool
    {
        $enrollment = $studentPayment->studentRecord;

        return $enrollment !== null && app(PortalAccess::class)->canRead(auth()->user(), $enrollment);
    }
}
