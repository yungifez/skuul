<?php

namespace App\Actions\Finance;

use App\Exceptions\InvalidValueException;
use App\Models\FinancialPeriod;
use App\Models\LedgerTransaction;
use App\Models\StudentRecord;
use App\Models\User;
use App\Services\Finance\ChartOfAccounts;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;

/**
 * Put a charge on a student's account.
 *
 * Raising an invoice means the school is owed money and has earned income, so
 * the entry debits fees receivable and credits the income account.
 */
class ChargeStudent
{
    public function __construct(
        private PostLedgerTransaction $post,
        private ChartOfAccounts $chart,
    ) {}

    /**
     * Charge the student.
     *
     * @throws InvalidValueException when the amount is not positive
     */
    public function charge(
        StudentRecord $enrollment,
        float $amount,
        string $description,
        ?Model $source = null,
        ?User $actor = null,
        ?CarbonInterface $date = null,
        string $incomePurpose = 'tuition_income',
        ?FinancialPeriod $period = null,
    ): LedgerTransaction {
        if ($amount <= 0) {
            throw new InvalidValueException('A charge must be more than nothing.');
        }

        $schoolId = $enrollment->school_id;

        return $this->post->post(
            description: $description,
            lines: [
                [
                    'account' => $this->chart->account('fees_receivable', $schoolId),
                    'debit' => $amount,
                    'student_record_id' => $enrollment->id,
                    'memo' => $description,
                ],
                [
                    'account' => $this->chart->account($incomePurpose, $schoolId),
                    'credit' => $amount,
                    'student_record_id' => $enrollment->id,
                    'memo' => $description,
                ],
            ],
            date: $date,
            source: $source,
            actor: $actor,
            period: $period,
        );
    }
}
