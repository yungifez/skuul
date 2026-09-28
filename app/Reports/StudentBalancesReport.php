<?php

namespace App\Reports;

use App\Contracts\Report;
use App\Enums\EnrollmentStatus;
use App\Models\LedgerLine;
use App\Models\StudentRecord;
use App\Services\Finance\ChartOfAccounts;
use App\Services\Finance\StudentLedger;
use Illuminate\Support\Collection;

/**
 * What each student still owes the school.
 *
 * A learner who moved to another campus can still owe this one. That debt
 * stays on this campus's books, so the learner stays on this report.
 */
class StudentBalancesReport implements Report
{
    public function __construct(private StudentLedger $ledger, private ChartOfAccounts $chart) {}

    /**
     * Get the name people choose the report by.
     */
    public function key(): string
    {
        return 'student-balances';
    }

    /**
     * Get the permission a person needs to ask for and read this report.
     */
    public function permission(): string
    {
        return 'read fee invoice';
    }

    /**
     * Get the title to print at the top.
     */
    public function title(): string
    {
        return 'Student balances';
    }

    /**
     * Get the column headings, in order.
     *
     * @return array<int, string>
     */
    public function columns(): array
    {
        return ['Admission number', 'Student', 'Level', 'Section', 'Status', 'Balance', 'Unapplied credit'];
    }

    /**
     * Build the rows of the report.
     *
     * @param  array<string, mixed>  $parameters
     * @return Collection<int, array<int, mixed>>
     */
    public function rows(array $parameters = []): Collection
    {
        $schoolId = (int) ($parameters['school_id'] ?? current_school_id());

        $enrollments = StudentRecord::query()
            ->inSchool($schoolId)
            ->when(
                ($parameters['only_attending'] ?? true) === true,
                fn ($query) => $query->where('status', EnrollmentStatus::Active)
            )
            ->with(['user', 'academicCycleSection.academicLevel'])
            ->get();

        /** @var Collection<int, array<int, mixed>> $rows */
        $rows = $enrollments->map(fn (StudentRecord $enrollment): array => [
            $enrollment->admission_number,
            $enrollment->user?->name,
            $enrollment->academicCycleSection?->academicLevel?->name,
            $enrollment->academicCycleSection?->name,
            $enrollment->status->label(),
            $this->ledger->balance($enrollment, $schoolId),
            $this->ledger->unappliedCredit($enrollment, $schoolId),
        ])->values()->toBase();

        return $rows->concat($this->movedAwayWithMoneyHere($schoolId));
    }

    /**
     * Get the learners now enrolled elsewhere whose money this campus still holds or is owed.
     *
     * @return Collection<int, array<int, mixed>>
     */
    private function movedAwayWithMoneyHere(int $schoolId): Collection
    {
        $accountIds = [
            $this->chart->account('fees_receivable', $schoolId)->id,
            $this->chart->account('unapplied_credits', $schoolId)->id,
        ];

        $rows = collect();

        StudentRecord::query()
            ->where(fn ($elsewhere) => $elsewhere->whereNull('school_id')->orWhere('school_id', '!=', $schoolId))
            ->whereIn('id', LedgerLine::query()->select('student_record_id')->whereIn('ledger_account_id', $accountIds))
            ->with(['user', 'school'])
            ->get()
            ->each(function (StudentRecord $enrollment) use ($schoolId, $rows): void {
                $balance = $this->ledger->balance($enrollment, $schoolId);
                $credit = $this->ledger->unappliedCredit($enrollment, $schoolId);

                if ($balance === 0.0 && $credit === 0.0) {
                    return;
                }

                $rows->push([
                    $enrollment->admission_number,
                    $enrollment->user?->name,
                    null,
                    null,
                    $enrollment->school === null ? $enrollment->status->label() : "Moved to {$enrollment->school->name}",
                    $balance,
                    $credit,
                ]);
            });

        return $rows;
    }
}
