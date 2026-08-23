<?php

namespace App\Reports;

use App\Contracts\Report;
use App\Models\AcademicYear;
use App\Services\Finance\BudgetComparison;
use App\Services\Finance\BudgetVersusActual;
use App\Traits\ReadsFinanceWindow;
use Illuminate\Support\Collection;

/**
 * Each plan beside the books, as a file somebody can take to a meeting.
 */
class BudgetVarianceReport implements Report
{
    use ReadsFinanceWindow;

    public function __construct(private BudgetVersusActual $comparison) {}

    /**
     * Get the name people choose the report by.
     */
    public function key(): string
    {
        return 'budget-variance';
    }

    /**
     * Get the title to print at the top.
     */
    public function title(): string
    {
        return 'Budget variance';
    }

    /**
     * Get the column headings, in order.
     *
     * @return array<int, string>
     */
    public function columns(): array
    {
        return ['Account', 'Covers', 'Narrowed to', 'Planned', 'Actual', 'Difference', 'Used %', 'Overspent'];
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

        $cycle = isset($parameters['academic_year_id'])
            ? AcademicYear::where('school_id', $schoolId)->find($parameters['academic_year_id'])
            : AcademicYear::where('school_id', $schoolId)->find(current_academic_year_id());

        if ($cycle === null) {
            return $this->asRows([]);
        }

        $rows = $this->comparison->forCycle($cycle)->map(fn (BudgetComparison $row): array => [
            $row->budget->account?->name,
            $row->budget->coverage(),
            $row->budget->narrowedTo(),
            $row->planned,
            $row->actual,
            $row->difference(),
            $row->used(),
            $row->isOverspent() ? 'Yes' : 'No',
        ])->values();

        return $this->asRows($rows->all());
    }
}
