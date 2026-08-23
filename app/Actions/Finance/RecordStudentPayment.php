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
    ) {}

    /**
     * Record the payment.
     *
     * @param  string  $into  the purpose of the account the money went into
     * @param  float|null  $applied  how much of it settles what is owed; worked
     *                               out from the balance when nobody says
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
        ?float $applied = null,
    ): LedgerTransaction {
        if ($amount <= 0) {
            throw new InvalidValueException('A payment must be more than nothing.');
        }

        $schoolId = $enrollment->school_id;

        if ($applied === null) {
            $owed = max($this->ledger->balance($enrollment), 0.0);
            $applied = min($amount, $owed);
        }

        $applied = round($applied, 2);
        $overpaid = round($amount - $applied, 2);

        if ($applied < 0 || $overpaid < 0) {
            throw new InvalidValueException('A payment cannot settle more than it is worth.');
        }
        $description ??= 'Payment received';

        $lines = [[
            'account' => $this->chart->account($into, $schoolId),
            'debit' => $amount,
            'student_record_id' => $enrollment->id,
            'memo' => $description,
        ]];

        if ($applied > 0) {
            $lines[] = [
                'account' => $this->chart->account('fees_receivable', $schoolId),
                'credit' => $applied,
                'student_record_id' => $enrollment->id,
                'memo' => $description,
            ];
        }

        if ($overpaid > 0) {
            $lines[] = [
                'account' => $this->chart->account('unapplied_credits', $schoolId),
                'credit' => $overpaid,
                'student_record_id' => $enrollment->id,
                'memo' => 'Money held for a later invoice',
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
