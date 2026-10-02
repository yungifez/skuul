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
use App\Services\Finance\StudentLedger;
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
        private BringInvoiceIntoTheBooks $bringIn,
        private RecordAuditEvent $auditor,
        private StudentLedger $ledger,
    ) {}

    /**
     * Put the credit the school holds against the oldest open bills.
     *
     * @param  int|null  $limit  the most to use, in minor units, or null for all of it
     * @param  int|null  $schoolId  the campus whose credit and bills to use; the
     *                              one the learner attends when nobody says
     * @return int the minor amount applied
     *
     * @throws InvalidValueException when the student holds no credit
     */
    public function apply(
        StudentRecord $enrollment,
        ?int $limit = null,
        ?int $onlyInvoice = null,
        ?User $actor = null,
        ?int $schoolId = null,
    ): int {
        $schoolId ??= $enrollment->school_id;

        return DB::transaction(function () use ($enrollment, $limit, $onlyInvoice, $actor, $schoolId): int {
            // Every change to a learner's money locks their record first, so
            // the same credit cannot be spent twice at the same moment.
            StudentRecord::query()->whereKey($enrollment->getKey())->lockForUpdate()->first();
            $this->bringIn->forLearner($enrollment, $schoolId, $actor);

            $credit = $this->creditHeld($enrollment, $schoolId);

            if ($credit <= 0) {
                throw new InvalidValueException('This student has no credit to use.');
            }

            $usable = $limit === null ? $credit : min($credit, $limit);

            if ($usable <= 0) {
                throw new InvalidValueException('There is nothing to apply.');
            }

            $plan = $this->planner->spread($enrollment, $usable, $onlyInvoice, $schoolId);
            $applied = array_sum($plan);

            if ($applied <= 0) {
                throw new InvalidValueException('This student owes nothing, so the credit stays where it is.');
            }

            $this->spendCredit($enrollment, $plan, $schoolId);

            $this->post->post(
                description: 'Credit used against fees owed',
                lines: [
                    [
                        'account' => $this->chart->account('unapplied_credits', $schoolId),
                        'debit' => round($applied / 100, 2),
                        'student_record_id' => $enrollment->id,
                        'memo' => 'Credit used',
                    ],
                    [
                        'account' => $this->chart->account('fees_receivable', $schoolId),
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
                $schoolId,
            );

            return $applied;
        });
    }

    /**
     * Get the money the school holds for this student, in minor units.
     *
     * The campus's own books say what it holds. A campus with separate books
     * never counts money another campus took, and credit carried inside a
     * billing group stays where the move carried it, even after the group
     * changes. The learner's unused payments cap the figure, so the books
     * and the receipts can never disagree upwards.
     */
    public function creditHeld(StudentRecord $enrollment, ?int $schoolId = null): int
    {
        $inTheBooks = (int) round($this->ledger->unappliedCredit($enrollment, $schoolId) * 100);

        $unusedPayments = StudentPayment::query()
            ->where('student_record_id', $enrollment->id)
            ->stillStanding()
            ->get()
            ->sum(fn (StudentPayment $payment): int => $payment->unallocated()->getMinorAmount()->toInt());

        return max(0, min($inTheBooks, $unusedPayments));
    }

    /**
     * Write the allocations, taking from the oldest payment that still holds money.
     *
     * @param  array<int, int>  $plan
     */
    private function spendCredit(StudentRecord $enrollment, array $plan, int $schoolId): void
    {
        $payments = $this->paymentsWithCredit($enrollment, $schoolId)->values();
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
     * Get the student's payments that still hold unused money.
     *
     * Money this campus took is used first, oldest first. A payment taken at
     * another campus is only reached for credit the books carried here.
     *
     * @return Collection<int, StudentPayment>
     */
    private function paymentsWithCredit(StudentRecord $enrollment, int $schoolId): Collection
    {
        return StudentPayment::query()
            ->where('student_record_id', $enrollment->id)
            ->withCreditLeft()
            ->orderByRaw('case when school_id = ? then 0 else 1 end', [$schoolId])
            ->orderBy('received_on')
            ->orderBy('id')
            ->get()
            ->toBase();
    }
}
