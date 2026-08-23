<?php

namespace App\Actions\Finance;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AuditAction;
use App\Exceptions\InvalidValueException;
use App\Models\PaymentAllocation;
use App\Models\StudentPayment;
use App\Models\StudentRecord;
use App\Models\User;
use App\Services\Finance\AllocationPlanner;
use App\Services\Finance\PaymentChannelRegistry;
use Brick\Money\Money as BrickMoney;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Take money from a family and say which fees it settles.
 *
 * One payment can clear several invoices, part of one invoice, or arrive
 * before any invoice at all. What it does not settle stays as credit against
 * the payment, so the school still holds the money and the next invoice can
 * use it.
 */
class ReceivePayment
{
    public function __construct(
        private RecordStudentPayment $post,
        private AllocationPlanner $planner,
        private PaymentChannelRegistry $channels,
        private RecordAuditEvent $auditor,
    ) {}

    /**
     * Record the payment and write its allocations.
     *
     * @param  int  $amount  what arrived, in minor units
     * @param  string  $method  the way the money reached the school
     * @param  array<int|string, int|string>|null  $allocations  the minor amount for each
     *                                                           invoice line, or null to
     *                                                           clear the oldest bills first
     * @param  int|null  $onlyInvoice  limit automatic spreading to one invoice
     *
     * @throws InvalidValueException when the amount is not positive or a plan is wrong
     */
    public function receive(
        StudentRecord $enrollment,
        int $amount,
        string $method = 'cash',
        ?array $allocations = null,
        ?int $onlyInvoice = null,
        ?string $reference = null,
        ?string $note = null,
        ?CarbonInterface $receivedOn = null,
        ?User $actor = null,
        ?Model $source = null,
    ): StudentPayment {
        if ($amount <= 0) {
            throw new InvalidValueException('A payment must be more than nothing.');
        }

        $channel = $this->channels->get($method);

        $plan = $allocations === null
            ? $this->planner->spread($enrollment, $amount, $onlyInvoice)
            : $this->planner->check($enrollment, $amount, $allocations);

        return DB::transaction(function () use ($enrollment, $amount, $channel, $method, $plan, $reference, $note, $receivedOn, $actor, $source): StudentPayment {
            $applied = array_sum($plan);

            // The books are written first, so the payment record can name the
            // entry behind it and neither one can exist without the other.
            $transaction = $this->post->record(
                enrollment: $enrollment,
                amount: round($amount / 100, 2),
                into: $channel->accountPurpose(),
                description: $note ?? 'Payment received',
                source: $source,
                actor: $actor,
                date: $receivedOn,
                reference: $reference,
                applied: round($applied / 100, 2),
            );

            $payment = StudentPayment::create([
                'school_id' => $enrollment->school_id,
                'student_record_id' => $enrollment->id,
                'amount' => BrickMoney::ofMinor($amount, config('app.currency')),
                'method' => $method,
                'reference' => $reference,
                'received_on' => $receivedOn ?? now(),
                'note' => $note,
                'ledger_transaction_id' => $transaction->id,
                'recorded_by' => $actor === null ? auth()->id() : $actor->id,
            ]);

            $this->writeAllocations($payment, $plan);

            $this->auditor->record(
                AuditAction::PaymentReceived,
                $payment,
                [
                    'amount' => $amount,
                    'applied' => $applied,
                    'credit' => $amount - $applied,
                    'method' => $method,
                    'reference' => $reference,
                ],
                $actor,
                $enrollment->school_id,
            );

            return $payment;
        });
    }

    /**
     * Write one allocation row for each line the payment settles.
     *
     * @param  array<int, int>  $plan
     */
    private function writeAllocations(StudentPayment $payment, array $plan): void
    {
        if ($plan === []) {
            return;
        }

        $invoiceIds = DB::table('fee_invoice_records')
            ->whereIn('id', array_keys($plan))
            ->pluck('fee_invoice_id', 'id');

        foreach ($plan as $lineId => $share) {
            PaymentAllocation::create([
                'student_payment_id' => $payment->id,
                'fee_invoice_id' => $invoiceIds[$lineId],
                'fee_invoice_record_id' => $lineId,
                'amount' => BrickMoney::ofMinor($share, config('app.currency')),
            ]);
        }
    }
}
