<?php

namespace App\Livewire;

use App\Enums\Role;
use App\Livewire\Concerns\InteractsWithAprilTable;
use App\Models\User;
use App\Services\Teacher\TeacherService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\MessageBag;
use Illuminate\View\View;
use Yungifez\AprilUI\Livewire\Columns\Column;
use Yungifez\AprilUI\Livewire\DataTableComponent;

class ListTeachersTable extends DataTableComponent
{
    use InteractsWithAprilTable;

    public function mount(): void
    {
        parent::mount();

        $this->setErrorBag(session()->get('errors', new MessageBag)->getMessages());
    }

    /**
     * @return Builder<User>
     */
    protected function builder(): Builder
    {
        return User::query()
            ->role(Role::Teacher)
            ->ofSchool();
    }

    /**
     * @return array<int, Column>
     */
    protected function columns(): array
    {
        return [
            Column::make('Name', 'name')->searchable()->sortable(),
            Column::make('Email', 'email')->searchable()->sortable(),
            Column::make('Gender', 'gender')->searchable(),
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
        return $rows->map(function (User $teacher): array {
            $row = $teacher->toArray();
            $row['view_url'] = route('teachers.show', $teacher);
            $row['manage_url'] = route('teachers.edit', $teacher);

            return $row;
        })->values()->all();
    }

    /**
     * Delete one teacher of this school.
     */
    public function deleteTeacher(int $userId, TeacherService $teachers): void
    {
        $teacher = $this->builder()->findOrFail($userId);
        Gate::authorize('delete', [$teacher, 'teacher']);

        $this->changeRow(fn () => $teachers->deleteTeacher($teacher), "{$teacher->name} was deleted.");
    }

    public function render(): View
    {
        $rows = $this->rows();
        $columns = $this->columns();

        return view('livewire.list-teachers-table', [
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
            'canManageTeachers' => auth()->user()->can('update teacher'),
            'canDeleteTeachers' => auth()->user()->can('delete teacher'),
        ]);
    }
}
