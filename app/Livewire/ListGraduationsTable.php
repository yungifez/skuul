<?php

namespace App\Livewire;

use App\Livewire\Concerns\InteractsWithAprilTable;
use App\Models\Graduation;
use App\Models\User;
use App\Services\Student\StudentService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Yungifez\AprilUI\Livewire\Columns\Column;
use Yungifez\AprilUI\Livewire\DataTableComponent;

class ListGraduationsTable extends DataTableComponent
{
    use InteractsWithAprilTable;

    /** @return Builder<User> */
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

            return $row;
        })->values()->all();
    }

    /**
     * Put one graduated learner of this school back into attendance.
     */
    public function resetGraduation(int $studentId, StudentService $students): void
    {
        $student = $this->builder()->findOrFail($studentId);
        Gate::authorize('resetGraduation', [Graduation::class, $student]);

        $this->changeRow(fn () => $students->resetGraduation($student), "{$student->name} attends again.");
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
