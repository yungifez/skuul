<?php

namespace App\Reports;

use App\Contracts\Report;
use App\Enums\EnrollmentStatus;
use App\Models\StudentRecord;
use Illuminate\Support\Collection;

/**
 * Who sits in each class and section right now.
 */
class ClassListReport implements Report
{
    /**
     * Get the name people choose the report by.
     */
    public function key(): string
    {
        return 'class-list';
    }

    /**
     * Get the title to print at the top.
     */
    public function title(): string
    {
        return 'Class list';
    }

    /**
     * Get the column headings, in order.
     *
     * @return array<int, string>
     */
    public function columns(): array
    {
        return ['Class', 'Section', 'Admission number', 'Student', 'Status'];
    }

    /**
     * Build the rows of the report.
     *
     * @param  array<string, mixed>  $parameters
     * @return Collection<int, array<int, mixed>>
     */
    public function rows(array $parameters = []): Collection
    {
        $enrollments = StudentRecord::query()
            ->inSchool($parameters['school_id'] ?? null)
            ->when(isset($parameters['my_class_id']), fn ($query) => $query->where('my_class_id', $parameters['my_class_id']))
            ->when(isset($parameters['section_id']), fn ($query) => $query->where('section_id', $parameters['section_id']))
            ->where('status', EnrollmentStatus::Active)
            ->with(['user', 'myClass', 'section'])
            ->get();

        /** @var Collection<int, array<int, mixed>> $rows */
        $rows = $enrollments->map(fn (StudentRecord $enrollment): array => [
            $enrollment->myClass?->name,
            $enrollment->section?->name,
            $enrollment->admission_number,
            $enrollment->user?->name,
            $enrollment->status->label(),
        ])->values()->toBase();

        return $rows;
    }
}
