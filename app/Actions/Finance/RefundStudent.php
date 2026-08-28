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
    ): StudentPayment {
        if ($amount <= 0) {
            throw new InvalidValueException('A refund must be more than nothing.');
        }

        if (trim($reason) === '') {
            throw new InvalidValueException('Say why the money is being given back.');
        }

        $held = $this->credit->creditHeld($enrollment);

        if ($amount > $held) {
            throw new InvalidValueException('The school is not holding that much for this student.');
        }

        $channel = $this->channels->get($method);
        $major = round($amount / 100, 2);

        return DB::transaction(function () use ($enrollment, $amount, $major, $reason, $method, $channel, $reference, $refundedOn, $actor): StudentPayment {
            $transaction = $this->post->post(
                description: "Refund: $reason",
                lines: [
                    [
                        'account' => $this->chart->account('unapplied_credits', $enrollment->school_id),
                        'debit' => $major,
                        'student_record_id' => $enrollment->id,
                        'memo' => $reason,
                    ],
                    [
                        'account' => $this->chart->account($channel->accountPurpose(), $enrollment->school_id),
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
                'school_id' => $enrollment->school_id,
                'student_record_id' => $enrollment->id,
                'financial_period_id' => $transaction->financial_period_id,
                'amount' => BrickMoney::ofMinor(-$amount, config('app.currency')),
                'method' => $method,
                'reference' => $reference,
                'received_on' => $refundedOn ?? now(),
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
                $enrollment->school_id,
            );

            return $refund;
        });
    }
}
