<?php

namespace App\Reports;

use App\Contracts\Report;
use App\Models\ReportCardSnapshot;
use Illuminate\Support\Collection;

/**
 * Official report-card revisions retained by one campus.
 */
class ReportCardReport implements Report
{
    public function key(): string
    {
        return 'report-cards';
    }

    public function title(): string
    {
        return 'Report cards';
    }

    /**
     * @return array<int, string>
     */
    public function columns(): array
    {
        return ['Admission number', 'Student', 'Academic year', 'Period', 'Revision', 'Average', 'Published'];
    }

    /**
     * @param  array<string, mixed>  $parameters
     * @return Collection<int, array<int, mixed>>
     */
    public function rows(array $parameters = []): Collection
    {
        $cards = ReportCardSnapshot::query()
            ->inSchool($parameters['school_id'] ?? null)
            ->when(isset($parameters['student_record_id']), fn ($query) => $query->where('student_record_id', $parameters['student_record_id']))
            ->with(['studentRecord.user', 'academicYear', 'academicPeriod'])
            ->orderByDesc('published_at')
            ->get();

        return $cards->map(fn (ReportCardSnapshot $card): array => $this->row(
            $card->studentRecord?->admission_number,
            $card->studentRecord?->user?->name,
            $card->academicYear?->name,
            $card->academicPeriod->label ?? $card->academicPeriod->name,
            $card->revision,
            $card->average_percentage,
            $card->published_at->toDateString(),
        ))->values()->toBase();
    }

    /**
     * Widen a report row to the contract's mixed-value shape.
     *
     * @return array<int, mixed>
     */
    private function row(mixed ...$values): array
    {
        return $values;
    }
}
