<?php

namespace App\Reports;

use App\Contracts\Report;
use App\Enums\EnrollmentStatus;
use App\Models\StudentRecord;
use App\Services\Finance\StudentLedger;
use Illuminate\Support\Collection;

/**
 * What each student still owes the school.
 */
class StudentBalancesReport implements Report
{
    public function __construct(private StudentLedger $ledger)
    {
    }

    /**
     * Get the name people choose the report by.
     */
    public function key(): string
    {
        return 'student-balances';
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
     * @param array<string, mixed> $parameters
     *
     * @return Collection<int, array<int, mixed>>
     */
    public function rows(array $parameters = []): Collection
    {
        $enrollments = StudentRecord::query()
            ->inSchool($parameters['school_id'] ?? null)
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
            $this->ledger->balance($enrollment),
            $this->ledger->unappliedCredit($enrollment),
        ])->values()->toBase();

        return $rows;
    }
}
