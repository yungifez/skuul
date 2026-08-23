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
use App\Services\Finance\ChartOfAccounts;
use Brick\Money\Money as BrickMoney;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Use money the school already holds to settle a new invoice.
 *
 * A family that paid ahead should not be asked to pay again. The credit is
 * simply the part of an earlier payment no invoice has used, so applying it
 * writes more allocations against that same payment.
 */
class ApplyStudentCredit
{
    public function __construct(
        private PostLedgerTransaction $post,
        private ChartOfAccounts $chart,
        private AllocationPlanner $planner,
        private RecordAuditEvent $auditor,
    ) {}

    /**
     * Put the credit the school holds against the oldest open bills.
     *
     * @param  int|null  $limit  the most to use, in minor units, or null for all of it
     * @return int the minor amount applied
     *
     * @throws InvalidValueException when the student holds no credit
     */
    public function apply(
        StudentRecord $enrollment,
        ?int $limit = null,
        ?int $onlyInvoice = null,
        ?User $actor = null,
    ): int {
        $credit = $this->creditHeld($enrollment);

        if ($credit <= 0) {
            throw new InvalidValueException('This student has no credit to use.');
        }

        $usable = $limit === null ? $credit : min($credit, $limit);

        if ($usable <= 0) {
            throw new InvalidValueException('There is nothing to apply.');
        }

        $plan = $this->planner->spread($enrollment, $usable, $onlyInvoice);
        $applied = array_sum($plan);

        if ($applied <= 0) {
            throw new InvalidValueException('This student owes nothing, so the credit stays where it is.');
        }

        return DB::transaction(function () use ($enrollment, $plan, $applied, $actor): int {
            $this->spendCredit($enrollment, $plan);

            $this->post->post(
                description: 'Credit used against fees owed',
                lines: [
                    [
                        'account' => $this->chart->account('unapplied_credits', $enrollment->school_id),
                        'debit' => round($applied / 100, 2),
                        'student_record_id' => $enrollment->id,
                        'memo' => 'Credit used',
                    ],
                    [
                        'account' => $this->chart->account('fees_receivable', $enrollment->school_id),
                        'credit' => round($applied / 100, 2),
                        'student_record_id' => $enrollment->id,
                        'memo' => 'Credit used',
                    ],
                ],
                actor: $actor,
            );

            $this->auditor->record(
                AuditAction::StudentCreditApplied,
                $enrollment,
                ['applied' => $applied],
                $actor,
                $enrollment->school_id,
            );

            return $applied;
        });
    }

    /**
     * Get the money the school holds for this student, in minor units.
     */
    public function creditHeld(StudentRecord $enrollment): int
    {
        return StudentPayment::query()
            ->where('student_record_id', $enrollment->id)
            ->stillStanding()
            ->get()
            ->sum(fn (StudentPayment $payment): int => $payment->unallocated()->getMinorAmount()->toInt());
    }

    /**
     * Write the allocations, taking from the oldest payment that still holds money.
     *
     * @param  array<int, int>  $plan
     */
    private function spendCredit(StudentRecord $enrollment, array $plan): void
    {
        $payments = $this->paymentsWithCredit($enrollment)->values();
        $invoiceIds = DB::table('fee_invoice_records')
            ->whereIn('id', array_keys($plan))
            ->pluck('fee_invoice_id', 'id');

        $index = 0;
        $left = $payments->isEmpty() ? 0 : $payments[0]->unallocated()->getMinorAmount()->toInt();

        foreach ($plan as $lineId => $share) {
            while ($share > 0) {
                while ($left <= 0) {
                    $index++;
                    $left = $payments[$index]->unallocated()->getMinorAmount()->toInt();
                }

                $take = min($share, $left);

                PaymentAllocation::create([
                    'student_payment_id' => $payments[$index]->id,
                    'fee_invoice_id' => $invoiceIds[$lineId],
                    'fee_invoice_record_id' => $lineId,
                    'amount' => BrickMoney::ofMinor($take, config('app.currency')),
                ]);

                $share -= $take;
                $left -= $take;
            }
        }
    }

    /**
     * Get the student's payments that still hold unused money, oldest first.
     *
     * @return Collection<int, StudentPayment>
     */
    private function paymentsWithCredit(StudentRecord $enrollment): Collection
    {
        return StudentPayment::query()
            ->where('student_record_id', $enrollment->id)
            ->withCreditLeft()
            ->orderBy('received_on')
            ->orderBy('id')
            ->get()
            ->toBase();
    }
}
