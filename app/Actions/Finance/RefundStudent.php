<?php

namespace App\Actions\Finance;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AuditAction;
use App\Exceptions\InvalidValueException;
use App\Models\StudentPayment;
use App\Models\StudentRecord;
use App\Models\User;
use App\Services\Finance\ChartOfAccounts;
use App\Services\Finance\PaymentChannelRegistry;
use Brick\Money\Money as BrickMoney;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;

/**
 * Give money back to a student or a guardian.
 *
 * Only money the school is actually holding can be given back. A family that
 * owes fees is not refunded by mistake, because the credit is what is left
 * after every invoice has taken its share.
 */
class RefundStudent
{
    public function __construct(
        private PostLedgerTransaction $post,
        private ChartOfAccounts $chart,
        private ApplyStudentCredit $credit,
        private PaymentChannelRegistry $channels,
        private RecordAuditEvent $auditor,
    ) {}

    /**
     * Hand the money back.
     *
     * @param  int  $amount  what to give back, in minor units
     * @param  int|null  $schoolId  the campus giving the money back; the one
     *                              the learner attends when nobody says
     *
     * @throws InvalidValueException when the school does not hold that much
     */
    public function refund(
        StudentRecord $enrollment,
        int $amount,
        string $reason,
        string $method = 'cash',
        ?string $reference = null,
        ?CarbonInterface $refundedOn = null,
        ?User $actor = null,
        ?int $schoolId = null,
    ): StudentPayment {
        $schoolId ??= $enrollment->school_id;

        if ($amount <= 0) {
            throw new InvalidValueException('A refund must be more than nothing.');
        }

        if (trim($reason) === '') {
            throw new InvalidValueException('Say why the money is being given back.');
        }

        $channel = $this->channels->get($method);
        $major = round($amount / 100, 2);

        return DB::transaction(function () use ($enrollment, $amount, $major, $reason, $method, $channel, $reference, $refundedOn, $actor, $schoolId): StudentPayment {
            // Every change to a learner's money locks their record first, so
            // two refunds cannot both spend the same credit.
            StudentRecord::query()->whereKey($enrollment->getKey())->lockForUpdate()->first();

            if ($amount > $this->credit->creditHeld($enrollment, $schoolId)) {
                throw new InvalidValueException('The school is not holding that much for this student.');
            }

            $transaction = $this->post->post(
                description: "Refund: $reason",
                lines: [
                    [
                        'account' => $this->chart->account('unapplied_credits', $schoolId),
                        'debit' => $major,
                        'student_record_id' => $enrollment->id,
                        'memo' => $reason,
                    ],
                    [
                        'account' => $this->chart->account($channel->accountPurpose(), $schoolId),
                        'credit' => $major,
                        'student_record_id' => $enrollment->id,
                        'memo' => $reason,
                    ],
                ],
                date: $refundedOn,
                actor: $actor,
                reference: $reference,
            );

            // A refund is money leaving, so it is recorded as the opposite of
            // a payment. The credit the school holds falls by the same amount.
            $refund = StudentPayment::create([
                'school_id' => $schoolId,
                'student_record_id' => $enrollment->id,
                'financial_period_id' => $transaction->financial_period_id,
                'amount' => BrickMoney::ofMinor(-$amount, config('app.currency')),
                'method' => $method,
                'reference' => $reference,
                'received_on' => $refundedOn ?? school_today($schoolId),
                'note' => "Refund: $reason",
                'ledger_transaction_id' => $transaction->id,
                'recorded_by' => $actor === null ? auth()->id() : $actor->id,
            ]);

            $this->auditor->record(
                AuditAction::StudentRefunded,
                $refund,
                [
                    'amount' => $amount,
                    'reason' => $reason,
                    'method' => $method,
                    'reference' => $reference,
                ],
                $actor,
                $schoolId,
            );

            return $refund;
        });
    }
}
