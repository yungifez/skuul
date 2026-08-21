<?php

namespace App\Actions\Finance;

use App\Exceptions\InvalidValueException;
use App\Models\LedgerTransaction;
use App\Models\StudentRecord;
use App\Models\User;
use App\Services\Finance\ChartOfAccounts;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;

/**
 * Take a charge off a student's account without money changing hands.
 *
 * A scholarship and a written-off debt look the same to the student and very
 * different to the school, so each one goes to its own expense account.
 */
class RelieveStudentFees
{
    public function __construct(
        private PostLedgerTransaction $post,
        private ChartOfAccounts $chart,
    ) {
    }

    /**
     * Give the student a scholarship or a waiver.
     */
    public function waive(
        StudentRecord $enrollment,
        float $amount,
        string $reason,
        ?Model $source = null,
        ?User $actor = null,
        ?CarbonInterface $date = null,
    ): LedgerTransaction {
        return $this->relieve($enrollment, $amount, 'scholarships', "Waiver: $reason", $source, $actor, $date);
    }

    /**
     * Give up on collecting the money.
     */
    public function writeOff(
        StudentRecord $enrollment,
        float $amount,
        string $reason,
        ?Model $source = null,
        ?User $actor = null,
        ?CarbonInterface $date = null,
    ): LedgerTransaction {
        return $this->relieve($enrollment, $amount, 'bad_debt', "Write-off: $reason", $source, $actor, $date);
    }

    /**
     * Post the relief against the given expense account.
     *
     * @throws InvalidValueException when the amount is not positive
     */
    private function relieve(
        StudentRecord $enrollment,
        float $amount,
        string $expensePurpose,
        string $description,
        ?Model $source,
        ?User $actor,
        ?CarbonInterface $date,
    ): LedgerTransaction {
        if ($amount <= 0) {
            throw new InvalidValueException('The amount must be more than nothing.');
        }

        $schoolId = $enrollment->school_id;

        return $this->post->post(
            description: $description,
            lines: [
                [
                    'account'           => $this->chart->account($expensePurpose, $schoolId),
                    'debit'             => $amount,
                    'student_record_id' => $enrollment->id,
                    'memo'              => $description,
                ],
                [
                    'account'           => $this->chart->account('fees_receivable', $schoolId),
                    'credit'            => $amount,
                    'student_record_id' => $enrollment->id,
                    'memo'              => $description,
                ],
            ],
            date: $date,
            source: $source,
            actor: $actor,
        );
    }
}
