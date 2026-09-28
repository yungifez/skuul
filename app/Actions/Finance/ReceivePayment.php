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
        private CarryBalanceToCampus $carry,
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
     * @param  int|null  $schoolId  the campus that is paid; the one the learner
     *                              attends when nobody says. A campus with its
     *                              own books still collects what it billed a
     *                              learner who has since moved on.
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
        ?int $schoolId = null,
    ): StudentPayment {
        if ($amount <= 0) {
            throw new InvalidValueException('A payment must be more than nothing.');
        }

        $channel = $this->channels->get($method);
        $schoolId ??= $enrollment->school_id;

        $reference = $reference === null || trim($reference) === '' ? null : trim($reference);

        return DB::transaction(function () use ($enrollment, $amount, $channel, $method, $allocations, $onlyInvoice, $reference, $note, $receivedOn, $actor, $source, $schoolId): StudentPayment {
            $this->refuseAReferenceAlreadyRecorded($enrollment, $reference, $schoolId);

            // The split is worked out under the lock, so two payments at the
            // same moment cannot both settle the same fee.
            $plan = $allocations === null
                ? $this->planner->spread($enrollment, $amount, $onlyInvoice, $schoolId)
                : $this->planner->check($enrollment, $amount, $allocations, $schoolId);

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
                schoolId: $schoolId,
            );

            $payment = StudentPayment::create([
                'school_id' => $schoolId,
                'student_record_id' => $enrollment->id,
                'financial_period_id' => $transaction->financial_period_id,
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
                $schoolId,
            );

            // Money a campus of the group takes for a learner who moved on
            // is theirs to spend where they now attend.
            $this->carry->carryToWhereTheyAttend($enrollment, $schoolId, $actor);

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

    /**
     * Refuse money that was already recorded under the same reference.
     *
     * A bank or card reference names one payment. Two cashiers, or one person
     * in two tabs, would otherwise count the same money twice. The learner's
     * record is locked, so two such payments cannot slip past each other.
     * One reference can still pay for two children, one payment each.
     *
     * @throws InvalidValueException when the learner already paid with that reference
     */
    private function refuseAReferenceAlreadyRecorded(StudentRecord $enrollment, ?string $reference, int $schoolId): void
    {
        StudentRecord::query()->whereKey($enrollment->getKey())->lockForUpdate()->first();

        if ($reference === null) {
            return;
        }

        $recorded = StudentPayment::query()
            ->where('school_id', $schoolId)
            ->where('student_record_id', $enrollment->id)
            ->where('amount', '>', 0)
            ->whereRaw('lower(reference) = ?', [mb_strtolower($reference)])
            ->stillStanding()
            ->first();

        if ($recorded !== null) {
            throw new InvalidValueException("A payment with reference {$recorded->reference} was already recorded on {$recorded->received_on->format('j M Y')}. Reverse that one first if it was wrong.");
        }
    }
}
