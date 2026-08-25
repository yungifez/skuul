<?php

namespace App\Livewire;

use App\Actions\Academic\PublishAcademicCalendar;
use App\Enums\AcademicPeriodStatus;
use App\Exceptions\InvalidValueException;
use App\Livewire\Concerns\InteractsWithAprilTable;
use App\Models\AcademicYear;
use App\Models\Exam;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use LogicException;
use Yungifez\AprilUI\Livewire\Columns\Column;
use Yungifez\AprilUI\Livewire\DataTableComponent;

class ShowAcademicYear extends DataTableComponent
{
    use InteractsWithAprilTable;

    public AcademicYear $academicYear;

    public function mount(?AcademicYear $academicYear = null): void
    {
        parent::mount();
        if ($academicYear === null) {
            throw new LogicException('An academic year is required.');
        }
        $this->academicYear = $academicYear->loadMissing('academicPeriods');
    }

    public function publishCalendar(PublishAcademicCalendar $publishAcademicCalendar): void
    {
        abort_unless(auth()->user()?->can('update', $this->academicYear), 403);

        try {
            $this->academicYear = $publishAcademicCalendar->publish($this->academicYear, auth()->user());
        } catch (InvalidValueException $exception) {
            $this->addError('calendar', $exception->getMessage());
        }
    }

    protected function builder(): Builder
    {
        return Exam::query()->whereRelation('academicPeriod', 'academic_year_id', $this->academicYear->id)->with('academicPeriod');
    }

    /** @return array<int, Column> */
    protected function columns(): array
    {
        return [Column::make('Name', 'name')->searchable()->sortable(), Column::make('Academic period', 'academic_period_name')];
    }

    /** @return array<int, array<string, mixed>> */
    protected function serializeRows(Collection $rows): array
    {
        return $rows->map(function (Exam $exam): array {
            $row = $exam->toArray();
            $row['academic_period_name'] = $exam->academicPeriod->name;
            $row['edit_url'] = route('exams.edit', $exam);
            $row['view_url'] = route('exams.show', $exam);
            $row['delete_url'] = route('exams.destroy', $exam);

            return $row;
        })->values()->all();
    }

    public function render(): View
    {
        $topLevelPeriods = $this->academicYear->academicPeriods
            ->whereNull('parent_id')
            ->values();

        return view('livewire.show-academic-year', array_merge($this->aprilTablePayload(), [
            'canEditExams' => auth()->user()->can('update exam'),
            'canDeleteExams' => auth()->user()->can('delete exam'),
            'canEditCalendar' => auth()->user()->can('update', $this->academicYear),
            'isDraft' => $this->academicYear->status === AcademicPeriodStatus::Draft,
            'topLevelPeriods' => $topLevelPeriods,
        ]));
    }
}
