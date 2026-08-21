<?php

namespace App\Actions\Finance;

use App\Exceptions\InvalidValueException;
use App\Models\LedgerTransaction;
use App\Models\StudentRecord;
use App\Models\User;
use App\Services\Finance\ChartOfAccounts;
use App\Services\Finance\StudentLedger;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;

/**
 * Take money from a student or a guardian.
 *
 * The payment first pays down what is owed. Anything left over is held as an
 * unapplied credit instead of being lost or turning the balance negative, so
 * the next invoice can use it.
 */
class RecordStudentPayment
{
    public function __construct(
        private PostLedgerTransaction $post,
        private ChartOfAccounts $chart,
        private StudentLedger $ledger,
    ) {
    }

    /**
     * Record the payment.
     *
     * @param string $into the purpose of the account the money went into
     *
     * @throws InvalidValueException when the amount is not positive
     */
    public function record(
        StudentRecord $enrollment,
        float $amount,
        string $into = 'cash',
        ?string $description = null,
        ?Model $source = null,
        ?User $actor = null,
        ?CarbonInterface $date = null,
        ?string $reference = null,
    ): LedgerTransaction {
        if ($amount <= 0) {
            throw new InvalidValueException('A payment must be more than nothing.');
        }

        $schoolId = $enrollment->school_id;
        $owed = max($this->ledger->balance($enrollment), 0.0);
        $applied = round(min($amount, $owed), 2);
        $overpaid = round($amount - $applied, 2);
        $description ??= 'Payment received';

        $lines = [[
            'account'           => $this->chart->account($into, $schoolId),
            'debit'             => $amount,
            'student_record_id' => $enrollment->id,
            'memo'              => $description,
        ]];

        if ($applied > 0) {
            $lines[] = [
                'account'           => $this->chart->account('fees_receivable', $schoolId),
                'credit'            => $applied,
                'student_record_id' => $enrollment->id,
                'memo'              => $description,
            ];
        }

        if ($overpaid > 0) {
            $lines[] = [
                'account'           => $this->chart->account('unapplied_credits', $schoolId),
                'credit'            => $overpaid,
                'student_record_id' => $enrollment->id,
                'memo'              => 'Money held for a later invoice',
            ];
        }

        return $this->post->post(
            description: $description,
            lines: $lines,
            date: $date,
            source: $source,
            actor: $actor,
            reference: $reference,
        );
    }
}
