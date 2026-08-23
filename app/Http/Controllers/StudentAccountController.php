<?php

namespace App\Http\Controllers;

use App\Actions\Finance\ApplyStudentCredit;
use App\Actions\Finance\RefundStudent;
use App\Actions\Finance\ReversePayment;
use App\Http\Requests\RefundStudentRequest;
use App\Http\Requests\ReversePaymentRequest;
use App\Models\StudentPayment;
use App\Models\StudentRecord;
use App\Services\Finance\PaymentChannelRegistry;
use App\Services\Finance\StudentLedger;
use Brick\Money\Money as BrickMoney;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * One student's money: what they owe, what they paid, what is held for them.
 *
 * The office needs one page that answers a parent at the counter, rather than
 * a list of invoices that each answer a piece of the question.
 */
class StudentAccountController extends Controller
{
    public function __construct(
        private StudentLedger $ledger,
        private ApplyStudentCredit $credit,
    ) {}

    /**
     * Show everything the school knows about one student's account.
     */
    public function show(StudentRecord $studentRecord, PaymentChannelRegistry $channels): View
    {
        $this->mustBeAllowedTo('read fee invoice', $studentRecord);

        $studentRecord->loadMissing(['user', 'academicCycleSection.academicLevel']);

        $payments = StudentPayment::query()
            ->where('student_record_id', $studentRecord->id)
            ->with(['allocations.feeInvoice', 'recordedBy', 'reversals'])
            ->orderByDesc('received_on')
            ->orderByDesc('id')
            ->get();

        $invoices = $studentRecord->user === null
            ? collect()
            : $studentRecord->user->feeInvoices()
                ->with(['feeInvoiceRecords.fee', 'feeInvoiceRecords.allocations'])
                ->orderByDesc('due_date')
                ->get();

        return view('pages.fee.account.show', [
            'enrollment' => $studentRecord,
            'balance' => $this->ledger->balance($studentRecord),
            'credit' => BrickMoney::ofMinor($this->credit->creditHeld($studentRecord), config('app.currency')),
            'payments' => $payments,
            'invoices' => $invoices,
            'channels' => $channels->all(),
            'canTakeMoney' => auth()->user()?->can('update fee invoice') === true,
            'canRefund' => auth()->user()?->can('refund student payment') === true,
        ]);
    }

    /**
     * Put the money the school holds against the oldest bills.
     */
    public function applyCredit(StudentRecord $studentRecord): RedirectResponse
    {
        $this->mustBeAllowedTo('update fee invoice', $studentRecord);

        $applied = $this->credit->apply($studentRecord);

        return back()->with(
            'success',
            BrickMoney::ofMinor($applied, config('app.currency'))->formatToLocale(app()->getLocale()).' of credit was used against fees owed.',
        );
    }

    /**
     * Give money back to the family.
     */
    public function refund(StudentRecord $studentRecord, RefundStudentRequest $request, RefundStudent $refund): RedirectResponse
    {
        $this->mustBeAllowedTo('refund student payment', $studentRecord);

        $refund->refund(
            enrollment: $studentRecord,
            amount: $request->minorAmount(),
            reason: $request->validated('reason'),
            method: $request->validated('method'),
            reference: $request->validated('reference'),
        );

        return back()->with('success', 'The refund was recorded.');
    }

    /**
     * Take back a payment that should not have been recorded.
     */
    public function reverse(StudentPayment $studentPayment, ReversePaymentRequest $request, ReversePayment $reverse): RedirectResponse
    {
        $this->mustBeAllowedTo('refund student payment', $studentPayment->studentRecord);

        $reverse->reverse($studentPayment, $request->validated('reason'));

        return back()->with('success', 'The payment was taken back.');
    }

    /**
     * Refuse anybody without the permission, or from another school.
     */
    private function mustBeAllowedTo(string $permission, ?StudentRecord $enrollment): void
    {
        abort_unless(
            auth()->user()?->can($permission) === true
                && $enrollment !== null
                && $enrollment->school_id === current_school_id(),
            403,
        );
    }
}
