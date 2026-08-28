<?php

namespace App\Actions\Finance;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AuditAction;
use App\Exceptions\InvalidValueException;
use App\Models\PaymentAllocation;
use App\Models\StudentPayment;
use App\Models\User;
use App\Services\Finance\FinancialPeriodResolver;
use Illuminate\Support\Facades\DB;

/**
 * Take back a payment that should not have been recorded.
 *
 * A cheque that bounces or a receipt entered against the wrong child is not
 * erased. The school records the opposite of it, so the receipt already given
 * to a family still matches something in the books.
 */
class ReversePayment
{
    public function __construct(
        private ReverseLedgerTransaction $reverseEntry,
        private RecordAuditEvent $auditor,
        private FinancialPeriodResolver $periods,
    ) {}

    /**
     * Record the reversal of the payment.
     *
     * @throws InvalidValueException when the payment was already taken back
     */
    public function reverse(StudentPayment $payment, string $reason, ?User $actor = null): StudentPayment
    {
        if ($payment->isReversal()) {
            throw new InvalidValueException('A reversal cannot be reversed. Record the payment again instead.');
        }

        if ($payment->isReversed()) {
            throw new InvalidValueException('This payment was already taken back.');
        }

        if (trim($reason) === '') {
            throw new InvalidValueException('Say why the payment is being taken back.');
        }

        return DB::transaction(function () use ($payment, $reason, $actor): StudentPayment {
            $transaction = $payment->ledgerTransaction === null
                ? null
                : $this->reverseEntry->reverse($payment->ledgerTransaction, $reason, $actor);
            $period = $transaction?->financialPeriod ?? $this->periods->currentOpen($payment->school_id);

            $reversal = StudentPayment::create([
                'school_id' => $payment->school_id,
                'student_record_id' => $payment->student_record_id,
                'financial_period_id' => $period?->id,
                'amount' => $payment->amount->multipliedBy(-1),
                'method' => $payment->method,
                'reference' => $payment->reference,
                'received_on' => now(),
                'note' => "Payment taken back: $reason",
                'ledger_transaction_id' => $transaction?->id,
                'reversal_of_id' => $payment->id,
                'recorded_by' => $actor === null ? auth()->id() : $actor->id,
            ]);

            foreach ($payment->allocations as $allocation) {
                PaymentAllocation::create([
                    'student_payment_id' => $reversal->id,
                    'fee_invoice_id' => $allocation->fee_invoice_id,
                    'fee_invoice_record_id' => $allocation->fee_invoice_record_id,
                    'amount' => $allocation->amount->multipliedBy(-1),
                    'reversal_of_id' => $allocation->id,
                ]);
            }

            $this->auditor->record(
                AuditAction::PaymentReversed,
                $reversal,
                [
                    'payment_id' => $payment->id,
                    'amount' => $payment->amount->getMinorAmount()->toInt(),
                    'reason' => $reason,
                ],
                $actor,
                $payment->school_id,
            );

            return $reversal;
        });
    }
}
