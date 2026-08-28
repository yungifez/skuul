<?php

namespace App\Reports;

use App\Contracts\Report;
use App\Enums\EnrollmentStatus;
use App\Models\FeeInvoice;
use App\Models\StudentRecord;
use App\Traits\ReadsFinanceWindow;
use Illuminate\Support\Collection;

/**
 * What each family owes, and how long it has owed it.
 *
 * A balance on its own does not say whether to send a reminder or to ring
 * somebody. How old the debt is does.
 */
class StudentAgingReport implements Report
{
    use ReadsFinanceWindow;

    /**
     * The buckets, in days overdue. The last bucket has no end.
     *
     * @var array<int, array{0: string, 1: int|null}>
     */
    private const BUCKETS = [
        ['Not yet due', 0],
        ['1 to 30 days', 30],
        ['31 to 60 days', 60],
        ['61 to 90 days', 90],
        ['Over 90 days', null],
    ];

    /**
     * Get the name people choose the report by.
     */
    public function key(): string
    {
        return 'student-aging';
    }

    /**
     * Get the title to print at the top.
     */
    public function title(): string
    {
        return 'Student balances by age';
    }

    /**
     * Get the column headings, in order.
     *
     * @return array<int, string>
     */
    public function columns(): array
    {
        $buckets = array_map(fn (array $bucket): string => $bucket[0], self::BUCKETS);

        return array_merge(['Admission number', 'Student', 'Level', 'Section'], $buckets, ['Total owed']);
    }

    /**
     * Build the rows of the report.
     *
     * @param  array<string, mixed>  $parameters
     * @return Collection<int, array<int, mixed>>
     */
    public function rows(array $parameters = []): Collection
    {
        $schoolId = $this->schoolId($parameters);
        $asAt = isset($parameters['as_at']) && is_string($parameters['as_at'])
            ? now()->parse($parameters['as_at'])
            : now();

        $enrollments = StudentRecord::query()
            ->inSchool($schoolId)
            ->when(
                ($parameters['only_attending'] ?? true) === true,
                fn ($query) => $query->where('status', EnrollmentStatus::Active),
            )
            ->with(['user', 'academicCycleSection.academicLevel'])
            ->get();

        $rows = [];

        foreach ($enrollments as $enrollment) {
            $buckets = array_fill(0, count(self::BUCKETS), 0.0);
            $total = 0.0;

            $invoices = FeeInvoice::query()
                ->ofSchool($enrollment->school_id)
                ->where('student_record_id', $enrollment->id)
                ->with(['feeInvoiceRecords.allocations', 'allocations'])
                ->get();

            foreach ($invoices as $invoice) {
                $owed = $invoice->balance->getAmount()->toFloat();

                if ($owed <= 0) {
                    continue;
                }

                $total += $owed;
                $buckets[$this->bucketFor($invoice, $asAt)] += $owed;
            }

            if ($total <= 0) {
                continue;
            }

            $rows[] = array_merge(
                [
                    $enrollment->admission_number,
                    $enrollment->user?->name,
                    $enrollment->academicCycleSection?->academicLevel?->name,
                    $enrollment->academicCycleSection?->name,
                ],
                array_map(fn (float $amount): float => round($amount, 2), $buckets),
                [round($total, 2)],
            );
        }

        return $this->asRows($rows);
    }

    /**
     * Work out which bucket one invoice falls into.
     */
    private function bucketFor(FeeInvoice $invoice, mixed $asAt): int
    {
        $dueDate = $invoice->due_date;

        if ($dueDate->greaterThanOrEqualTo($asAt)) {
            return 0;
        }

        $daysOverdue = $dueDate->diffInDays($asAt);

        foreach (self::BUCKETS as $index => [$label, $limit]) {
            if ($index === 0) {
                continue;
            }

            if ($limit === null || $daysOverdue <= $limit) {
                return $index;
            }
        }

        return count(self::BUCKETS) - 1;
    }
}
