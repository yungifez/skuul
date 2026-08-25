<?php

namespace App\Livewire;

use App\Enums\Role;
use App\Livewire\Concerns\InteractsWithAprilTable;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Yungifez\AprilUI\Livewire\Columns\Column;
use Yungifez\AprilUI\Livewire\DataTableComponent;

class ListAdminsTable extends DataTableComponent
{
    use InteractsWithAprilTable;

    public int $totalAdmins = 0;

    public int $activeAdmins = 0;

    public int $invitedAdmins = 0;

    public int $suspendedAdmins = 0;

    public int $archivedAdmins = 0;

    public function mount(): void
    {
        parent::mount();

        $admins = User::query()->role(Role::Admin->value)->ofSchool();
        $this->totalAdmins = (clone $admins)->count();
        $this->activeAdmins = (clone $admins)->where('account_status', 'active')->count();
        $this->invitedAdmins = (clone $admins)->where('account_status', 'invited')->count();
        $this->suspendedAdmins = (clone $admins)->where('account_status', 'suspended')->count();
        $this->archivedAdmins = (clone $admins)->where('account_status', 'archived')->count();
    }

    protected function builder(): Builder
    {
        return User::query()->role(Role::Admin)->ofSchool()->orderBy('name');
    }

    /** @return array<int, Column> */
    protected function columns(): array
    {
        return [
            Column::make('Name', 'name')->searchable()->sortable(),
            Column::make('Email', 'email')->searchable()->sortable(),
            Column::make('Gender', 'gender')->searchable(),
            Column::make('Account', 'account_status'),
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
        return $rows->map(function (User $admin): array {
            $row = $admin->toArray();
            $row['view_url'] = route('admins.show', $admin);
            $row['edit_url'] = route('admins.edit', $admin);
            $row['delete_url'] = route('admins.destroy', $admin);

            return $row;
        })->values()->all();
    }

    public function render(): View
    {
        $rows = $this->rows();
        $columns = $this->columns();

        return view('livewire.list-admins-table', [
            'columns' => $this->columnDefinitions(),
            'data' => $this->serializeRows(collect($rows->items())),
            'pagination' => ['mode' => 'controlled', 'page' => $rows->currentPage(), 'perPage' => $rows->perPage(), 'total' => $rows->total(), 'search' => $this->search, 'sort' => $this->sort ? ['key' => $this->sort, 'direction' => $this->direction] : null],
            'id' => $this->tableId(),
            'perPageOptions' => $this->perPageOptions,
            'rowKey' => $this->primaryKey(),
            'searchable' => collect($columns)->contains(fn (Column $column): bool => $column->isSearchable()),
            'canManageAdmins' => auth()->user()->can('update admin'),
            'canDeleteAdmins' => auth()->user()->can('delete admin'),
        ]);
    }
}
