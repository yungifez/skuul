<?php

namespace App\Reports;

use App\Contracts\Report;
use App\Models\TranscriptSnapshot;
use Illuminate\Support\Collection;

/**
 * Subjects captured in each immutable transcript revision.
 */
class TranscriptReport implements Report
{
    public function key(): string
    {
        return 'transcripts';
    }

    public function title(): string
    {
        return 'Transcripts';
    }

    /**
     * @return array<int, string>
     */
    public function columns(): array
    {
        return ['Admission number', 'Student', 'Revision', 'Academic year', 'Period', 'Subject', 'Percentage', 'Issued'];
    }

    /**
     * @param  array<string, mixed>  $parameters
     * @return Collection<int, array<int, mixed>>
     */
    public function rows(array $parameters = []): Collection
    {
        $transcripts = TranscriptSnapshot::query()
            ->inSchool($parameters['school_id'] ?? null)
            ->when(isset($parameters['student_record_id']), fn ($query) => $query->where('student_record_id', $parameters['student_record_id']))
            ->with('studentRecord.user')
            ->orderByDesc('issued_at')
            ->get();

        return $transcripts->flatMap(function (TranscriptSnapshot $transcript): Collection {
            $results = $this->resultsOf($transcript);

            if ($results->isEmpty()) {
                return collect([$this->row(
                    $transcript->studentRecord?->admission_number,
                    $transcript->studentRecord?->user?->name,
                    $transcript->revision,
                    null,
                    null,
                    null,
                    null,
                    $transcript->issued_at->toDateString(),
                )]);
            }

            return $results->map(fn (array $result): array => $this->row(
                $transcript->studentRecord?->admission_number,
                $transcript->studentRecord?->user?->name,
                $transcript->revision,
                $result['academic_year'] ?? null,
                $result['academic_period'] ?? null,
                $result['subject'] ?? null,
                $result['percentage'] ?? null,
                $transcript->issued_at->toDateString(),
            ));
        })->values()->toBase();
    }

    /**
     * Read only row-shaped result payloads.
     *
     * @return Collection<int, array<mixed, mixed>>
     */
    private function resultsOf(TranscriptSnapshot $transcript): Collection
    {
        $payload = $transcript->payload['results'] ?? [];

        if (!is_array($payload)) {
            return collect();
        }

        $results = [];

        foreach ($payload as $result) {
            if (is_array($result)) {
                $results[] = $result;
            }
        }

        return collect($results);
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
