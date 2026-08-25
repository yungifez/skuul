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

class ListTeachersTable extends DataTableComponent
{
    public function mount(): void
    {
        parent::mount();

        $this->setErrorBag(session()->get('errors', new MessageBag)->getMessages());
    }

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
            $row['delete_url'] = route('teachers.destroy', $teacher);

            return $row;
        })->values()->all();
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
