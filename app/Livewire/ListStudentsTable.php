<?php

namespace App\Livewire;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\MessageBag;
use Illuminate\View\View;
use Yungifez\AprilUI\Livewire\Columns\Column;
use Yungifez\AprilUI\Livewire\DataTableComponent;

class ListStudentsTable extends DataTableComponent
{
    public function mount(): void
    {
        parent::mount();

        $this->setErrorBag(session()->get('errors', new MessageBag)->getMessages());
    }

    protected function builder(): Builder
    {
        $query = User::query()
            ->students()
            ->ofSchool()
            ->activeStudents()
            ->with('studentRecord.academicCycleSection.academicLevel');

        if (auth()->user()->hasRole(Role::Parent)) {
            $query->whereRelation('parents', 'parent_records.user_id', auth()->id());
        }

        return $query;
    }

    /**
     * @return array<int, Column>
     */
    protected function columns(): array
    {
        return [
            Column::make('Name', 'name')->searchable()->sortable(),
            Column::make('Email', 'email')->searchable()->sortable(),
            Column::make('Admission #', 'student_record.admission_number')->searchable(function (Builder $query, string $term): void {
                $query->orWhereHas('studentRecord', function (Builder $studentRecord) use ($term): void {
                    $studentRecord->where('admission_number', 'like', "%{$term}%");
                });
            }),
            Column::make(school_term('class_level', 'Class'), 'student_record.academic_cycle_section.academic_level.name'),
            Column::make(school_term('section', 'Section'), 'student_record.academic_cycle_section.name'),
            Column::make('Enrollment', 'student_record.status'),
            Column::make('Account', 'account_status'),
        ];
    }

    /**
     * @return array{field: string, direction: string}
     */
    protected function defaultSort(): ?array
    {
        return ['field' => 'name', 'direction' => 'asc'];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function serializeRows(Collection $rows): array
    {
        return $rows->map(function (User $student): array {
            $row = $student->toArray();
            $row['view_url'] = route('students.show', $student);
            $row['manage_url'] = route('students.edit', $student);
            $row['delete_url'] = route('students.destroy', $student);

            return $row;
        })->values()->all();
    }

    public function render(): View
    {
        $rows = $this->rows();
        $columns = $this->columns();

        return view('livewire.list-students-table', [
            'columns' => $this->columnDefinitions(),
            'data' => $this->serializeRows(collect($rows->items())),
            'pagination' => [
                'mode' => 'controlled',
                'page' => $rows->currentPage(),
                'perPage' => $rows->perPage(),
                'total' => $rows->total(),
                'search' => $this->search,
                'sort' => $this->sort ? ['key' => $this->sort, 'direction' => $this->direction] : null,
            ],
            'id' => $this->tableId(),
            'perPageOptions' => $this->perPageOptions,
            'rowKey' => $this->primaryKey(),
            'searchable' => collect($columns)->contains(fn (Column $column): bool => $column->isSearchable()),
            'canManageStudents' => auth()->user()->can('update student'),
            'canDeleteStudents' => auth()->user()->can('delete student'),
        ]);
    }
}
