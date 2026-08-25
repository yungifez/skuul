<?php

namespace App\Livewire;

use App\Livewire\Concerns\InteractsWithAprilTable;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Yungifez\AprilUI\Livewire\Columns\Column;
use Yungifez\AprilUI\Livewire\DataTableComponent;

class ListGraduationsTable extends DataTableComponent
{
    use InteractsWithAprilTable;

    protected function builder(): Builder
    {
        return User::query()->students()->ofSchool()->has('graduatedStudentRecord')->with('graduatedStudentRecord.academicCycleSection.academicLevel');
    }

    /** @return array<int, Column> */
    protected function columns(): array
    {
        return [Column::make('Name', 'name')->searchable()->sortable(), Column::make('Email', 'email'), Column::make('Admission number', 'admission_number'), Column::make('From '.strtolower(school_term('class_level', 'class')), 'from_class'), Column::make('From '.strtolower(school_term('section', 'section')), 'from_section')];
    }

    /** @return array<int, array<string, mixed>> */
    protected function serializeRows(Collection $rows): array
    {
        return $rows->map(function (User $student): array {
            $record = $student->graduatedStudentRecord;
            $row = $student->toArray();
            $row['admission_number'] = $record->admission_number;
            $row['from_class'] = $record->academicCycleSection->academicLevel->name;
            $row['from_section'] = $record->academicCycleSection->name;
            $row['edit_url'] = route('students.edit', $student);
            $row['view_url'] = route('students.show', $student);
            $row['reset_url'] = route('students.graduations.reset', $student);

            return $row;
        })->values()->all();
    }

    public function render(): View
    {
        return view('livewire.list-graduations-table', array_merge($this->aprilTablePayload(), [
            'canManageStudents' => auth()->user()->can('update student'),
            'canViewStudents' => auth()->user()->can('read student'),
            'canResetGraduations' => auth()->user()->can('reset graduation'),
        ]));
    }
}
