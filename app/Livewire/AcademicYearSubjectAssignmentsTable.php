<?php

namespace App\Livewire;

use App\Livewire\Concerns\InteractsWithAprilTable;
use App\Models\AcademicYear;
use App\Models\CourseOffering;
use App\Models\Subject;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use LogicException;
use Yungifez\AprilUI\Livewire\Columns\Column;
use Yungifez\AprilUI\Livewire\DataTableComponent;

class AcademicYearSubjectAssignmentsTable extends DataTableComponent
{
    use InteractsWithAprilTable;

    public AcademicYear $academicYear;

    public bool $setupMode = false;

    public function mount(?AcademicYear $academicYear = null): void
    {
        parent::mount();
        if ($academicYear === null) {
            throw new LogicException('An academic year is required.');
        }

        $this->academicYear = $academicYear;
        $this->setupMode = request()->boolean('setup');
    }

    protected function builder(): Builder
    {
        return Subject::query()
            ->inSchool($this->academicYear->school_id)
            ->with([
                'courseOfferings' => function (Relation $query): mixed {
                    $query->where('academic_year_id', $this->academicYear->id)->with([
                        'academicLevel:id,name,is_group',
                        'academicPeriod:id,name,label',
                        'cycleSections:id,name,label',
                    ]);

                    return null;
                },
            ])
            ->withCount([
                'courseOfferings as offering_count' => fn (Builder $query): Builder => $query
                    ->where('academic_year_id', $this->academicYear->id),
            ])
            ->orderBy('name');
    }

    /** @return array<int, Column> */
    protected function columns(): array
    {
        return [
            Column::make('Subject', 'name')->searchable()->sortable(),
            Column::make('Short name', 'short_name'),
            Column::make('Assigned classes', 'assigned_classes'),
            Column::make('Periods', 'periods'),
            Column::make('Offerings', 'offering_count')->sortable(),
        ];
    }

    /** @return array{field: string, direction: string} */
    protected function defaultSort(): ?array
    {
        return ['field' => 'name', 'direction' => 'asc'];
    }

    /** @return array<int, array<string, mixed>> */
    protected function serializeRows(Collection $rows): array
    {
        return $rows->map(function (Subject $subject): array {
            $offerings = $subject->courseOfferings;
            $classes = $offerings->map(function (CourseOffering $offering): string {
                $className = $offering->academicLevel->name;
                $sections = $offering->cycleSections
                    ->map(fn ($section): string => $section->label ?: $section->name)
                    ->join(', ');

                return $sections === ''
                    ? $className.' · '.$offering->roster_mode->label()
                    : $className.' · '.$sections;
            })->unique()->values();
            $periods = $offerings
                ->map(fn (CourseOffering $offering): ?string => $offering->academicPeriod?->displayName)
                ->filter()
                ->unique()
                ->values();
            $row = [
                'id' => $subject->id,
                'name' => $subject->name,
                'short_name' => $subject->short_name,
                'assigned_classes' => $classes->isEmpty() ? 'Not assigned to a class yet' : $classes->join(' · '),
                'periods' => $periods->isEmpty() ? 'Not planned yet' : $periods->join(', '),
                'offering_count' => $offerings->count(),
            ];
            $row['manage_url'] = route('course-offerings.index', ['academic_year_id' => $this->academicYear->id, 'subject_id' => $subject->id]);
            $row['bulk_url'] = route('course-offerings.bulk-create.form', array_filter([
                'academic_year_id' => $this->academicYear->id,
                'subject_id' => $subject->id,
                'setup' => $this->setupMode ? 1 : null,
            ]));
            $row['edit_url'] = route('subjects.edit', $subject);

            return $row;
        })->values()->all();
    }

    public function render(): View
    {
        return view('livewire.academic-year-subject-assignments-table', array_merge(
            $this->aprilTablePayload(),
            [
                'canManageOfferings' => auth()->user()->can('read subject'),
                'canCreateOfferings' => auth()->user()->can('create subject'),
                'canEditSubjects' => auth()->user()->can('update subject'),
            ],
        ));
    }
}
