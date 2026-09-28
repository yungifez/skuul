<?php

namespace App\Livewire;

use App\Actions\Finance\ApplyStudentCredit;
use App\Actions\Finance\RefundStudent;
use App\Actions\Finance\ReversePayment;
use App\Exceptions\InvalidValueException;
use App\Livewire\Concerns\DispatchesStatusNotifications;
use App\Models\FeeInvoice;
use App\Models\StudentPayment;
use App\Models\StudentRecord;
use App\Services\Finance\PaymentChannelRegistry;
use App\Services\Finance\StudentLedger;
use Brick\Money\Money as BrickMoney;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;

/**
 * One student's money: what they owe, what they paid, what is held for them.
 *
 * Every figure is worked out from the payments and the books. Nothing on this
 * screen edits a payment; a mistake is taken back with a reversal.
 *
 * The screen reads the books of the campus it is opened at. A campus with its
 * own books keeps a learner's account after they move on, so it can still
 * collect what it is owed and give back what it holds.
 */
class ShowStudentAccount extends Component
{
    use DispatchesStatusNotifications;

    public StudentRecord $enrollment;

    #[Locked]
    public int $campusId;

    public ?int $reversingPaymentId = null;

    public string $reverseReason = '';

    public bool $isRefunding = false;

    public string $refundAmount = '';

    public string $refundMethod = 'cash';

    public string $refundReason = '';

    public string $refundReference = '';

    public function mount(StudentRecord $enrollment): void
    {
        $this->campusId = current_school_id();
        $this->mustBeAllowedTo('read fee invoice', $enrollment);

        $this->enrollment = $enrollment;
    }

    /**
     * Put the money the school holds against the oldest bills.
     */
    public function applyCredit(ApplyStudentCredit $credit): void
    {
        $this->mustBeAllowedTo('update fee invoice', $this->enrollment);

        try {
            $applied = $credit->apply($this->enrollment, actor: auth()->user(), schoolId: $this->campusId);
        } catch (InvalidValueException $exception) {
            $this->addError('credit', $exception->getMessage());

            return;
        }

        $this->notify(BrickMoney::ofMinor($applied, config('app.currency'))->formatToLocale(app()->getLocale()).' of credit was used against fees owed.');
    }

    public function startReversing(int $paymentId): void
    {
        $this->reversingPaymentId = $this->payment($paymentId)->id;
        $this->reverseReason = '';
        $this->resetValidation();
    }

    /**
     * Take back a payment that should not have been recorded.
     */
    public function reversePayment(ReversePayment $reverse): void
    {
        $this->mustBeAllowedTo('refund student payment', $this->enrollment);

        $this->validate(
            ['reverseReason' => ['required', 'string', 'min:5', 'max:500']],
            ['reverseReason.required' => 'Say why the payment is being taken back.', 'reverseReason.min' => 'Give a reason somebody can understand later.'],
        );

        try {
            $reverse->reverse($this->payment((int) $this->reversingPaymentId), $this->reverseReason, auth()->user());
        } catch (InvalidValueException $exception) {
            $this->addError('reverseReason', $exception->getMessage());

            return;
        }

        $this->reset('reversingPaymentId', 'reverseReason');
        $this->notify('The payment was taken back.');
    }

    /**
     * Give money the school holds back to the family.
     */
    public function refund(RefundStudent $refund, PaymentChannelRegistry $channels): void
    {
        $this->mustBeAllowedTo('refund student payment', $this->enrollment);

        $this->validate([
            'refundAmount' => ['required', 'decimal:0,2', 'min:0.01', 'max:100000000'],
            'refundReason' => ['required', 'string', 'min:5', 'max:500'],
            'refundMethod' => ['required', 'string', Rule::in($channels->keys())],
            'refundReference' => ['nullable', 'string', 'max:100'],
        ], [
            'refundReason.required' => 'Say why the money is being given back.',
            'refundReason.min' => 'Give a reason somebody can understand later.',
        ]);

        try {
            $refund->refund(
                enrollment: $this->enrollment,
                amount: BrickMoney::of($this->refundAmount, config('app.currency'))->getMinorAmount()->toInt(),
                reason: $this->refundReason,
                method: $this->refundMethod,
                reference: $this->refundReference === '' ? null : $this->refundReference,
                actor: auth()->user(),
                schoolId: $this->campusId,
            );
        } catch (InvalidValueException $exception) {
            $this->addError('refundAmount', $exception->getMessage());

            return;
        }

        $this->reset('isRefunding', 'refundAmount', 'refundReason', 'refundReference');
        $this->notify('The refund was recorded.');
    }

    public function cancel(): void
    {
        $this->reset('reversingPaymentId', 'reverseReason', 'isRefunding');
        $this->resetValidation();
    }

    public function render(StudentLedger $ledger, ApplyStudentCredit $credit, PaymentChannelRegistry $channels): View
    {
        $this->enrollment->loadMissing(['user', 'academicCycleSection.academicLevel']);

        return view('livewire.show-student-account', [
            'balance' => $ledger->balance($this->enrollment, $this->campusId),
            'elsewhere' => $ledger->balancesByCampus($this->enrollment)
                ->reject(fn (array $row): bool => $row['school']->id === $this->campusId),
            'credit' => BrickMoney::ofMinor($credit->creditHeld($this->enrollment, $this->campusId), config('app.currency')),
            'payments' => StudentPayment::query()
                ->where('student_record_id', $this->enrollment->id)
                ->with(['allocations.feeInvoice', 'recordedBy', 'reversals'])
                ->orderByDesc('received_on')
                ->orderByDesc('id')
                ->get(),
            'invoices' => FeeInvoice::query()
                ->ofSchool($this->campusId)
                ->where('student_record_id', $this->enrollment->id)
                ->with(['feeInvoiceRecords.fee', 'feeInvoiceRecords.allocations'])
                ->orderByDesc('due_date')
                ->get(),
            'channels' => $channels->all(),
            'canTakeMoney' => auth()->user()?->can('update fee invoice') === true,
            'canRefund' => auth()->user()?->can('refund student payment') === true,
        ]);
    }

    private function payment(int $paymentId): StudentPayment
    {
        // A campus only takes back money it took itself. A payment made at a
        // campus the learner has left stays in that campus's books.
        return StudentPayment::query()
            ->where('student_record_id', $this->enrollment->id)
            ->where('school_id', $this->campusId)
            ->findOrFail($paymentId);
    }

    /**
     * Refuse anybody without the permission, or from a school with no account.
     *
     * A campus the learner left still holds their account when it billed or
     * took money from them. Any other school sees nothing.
     */
    private function mustBeAllowedTo(string $permission, StudentRecord $enrollment): void
    {
        abort_unless(
            auth()->user()?->can($permission) === true
                && $this->campusId === current_school_id()
                && $enrollment->hasAccountInSchool(),
            403,
        );
    }
}
