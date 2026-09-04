<?php

namespace App\Livewire;

use App\Enums\CourseOfferingStatus;
use App\Enums\Role;
use App\Enums\RosterMode;
use App\Enums\SyllabusStatus;
use App\Livewire\Concerns\InteractsWithAprilTable;
use App\Models\Syllabus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Yungifez\AprilUI\Livewire\Columns\Column;
use Yungifez\AprilUI\Livewire\DataTableComponent;

class ListSyllabiTable extends DataTableComponent
{
    use InteractsWithAprilTable;

    protected function builder(): Builder
    {
        $user = auth()->user();
        $query = Syllabus::query()
            ->inSchool()
            ->with(['courseOffering.subject', 'courseOffering.academicLevel', 'courseOffering.academicPeriod'])
            ->latest();

        if ($user->hasRole(Role::Student)) {
            $enrollment = $user->studentRecord()->attending()->first();

            if ($enrollment === null) {
                return $query->whereKey(-1);
            }

            $query
                ->where('status', SyllabusStatus::Published)
                ->whereHas('courseOffering', function (Builder $offering) use ($enrollment): void {
                    $offering
                        ->where('status', CourseOfferingStatus::Active)
                        ->where(function (Builder $roster) use ($enrollment): void {
                            $roster
                                ->whereIn('roster_mode', [RosterMode::HomeSection->value, RosterMode::CombinedHomeSections->value])
                                ->whereHas('cycleSections', fn (Builder $sections): Builder => $sections->whereKey($enrollment->academic_cycle_section_id))
                                ->orWhere(function (Builder $roster) use ($enrollment): void {
                                    $roster
                                        ->where('roster_mode', RosterMode::AcademicLevel->value)
                                        ->where('academic_level_id', $enrollment->academicCycleSection?->academic_level_id);
                                })
                                ->orWhere(function (Builder $roster) use ($enrollment): void {
                                    $roster
                                        ->where('roster_mode', RosterMode::IndividualRoster->value)
                                        ->whereHas('studentRecords', fn (Builder $students): Builder => $students->whereKey($enrollment->id));
                                });
                        });
                });
        }

        return $query;
    }

    /** @return array<int, Column> */
    protected function columns(): array
    {
        return [
            Column::make('Name', 'name')->searchable()->sortable(),
            Column::make('Subject', 'subject_name'),
            Column::make(school_term('class_level', 'Class'), 'academic_level_label'),
            Column::make(school_term('period', 'Period'), 'academic_period_label'),
        ];
    }

    /** @return array<int, array<string, mixed>> */
    protected function serializeRows(Collection $rows): array
    {
        return $rows->map(function (Syllabus $syllabus): array {
            $row = $syllabus->toArray();
            $row['subject_name'] = $syllabus->courseOffering->subject->name;
            $row['academic_level_label'] = $syllabus->courseOffering->academicLevel->name;
            $row['academic_period_label'] = $syllabus->courseOffering->academicPeriod->label;
            $row['view_url'] = route('syllabi.show', $syllabus);
            $row['delete_url'] = route('syllabi.destroy', $syllabus);

            return $row;
        })->values()->all();
    }

    public function render(): View
    {
        return view('livewire.list-syllabi-table', array_merge(
            $this->aprilTablePayload(),
            [
                'canReadSyllabi' => auth()->user()->can('read syllabus'),
                'canDeleteSyllabi' => auth()->user()->can('delete syllabus'),
            ],
        ));
    }
}
