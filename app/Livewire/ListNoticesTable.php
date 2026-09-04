<?php

namespace App\Livewire;

use App\Models\Notice;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Yungifez\AprilUI\Livewire\Columns\Column;
use Yungifez\AprilUI\Livewire\DataTableComponent;

class ListNoticesTable extends DataTableComponent
{
    protected function builder(): Builder
    {
        $user = auth()->user();
        $query = Notice::query()->inSchool();

        if ($user->isPortalOnly()) {
            return $query
                ->published()
                ->active()
                ->whereHas('recipients', fn (Builder $recipients): Builder => $recipients->where('user_id', $user->id));
        }

        if (!$user->can('update notice') && !$user->can('delete notice')) {
            $query->active();
        }

        return $query;
    }

    /** @return array<int, Column> */
    protected function columns(): array
    {
        return [
            Column::make('Title', 'title')->searchable()->sortable(),
            Column::make('Start date', 'start_date_for_humans'),
            Column::make('Stop date', 'stop_date_for_humans'),
        ];
    }

    /** @return array{field: string, direction: string} */
    protected function defaultSort(): ?array
    {
        return ['field' => 'title', 'direction' => 'asc'];
    }

    /** @return array<int, array<string, mixed>> */
    protected function serializeRows(Collection $rows): array
    {
        return $rows->map(function (Notice $notice): array {
            $row = $notice->toArray();
            $row['start_date_for_humans'] = $notice->start_date_for_humans;
            $row['stop_date_for_humans'] = $notice->stop_date_for_humans;
            $row['view_url'] = route('notices.show', $notice);
            $row['delete_url'] = route('notices.destroy', $notice);

            return $row;
        })->values()->all();
    }

    public function render(): View
    {
        $rows = $this->rows();
        $columns = $this->columns();

        return view('livewire.list-notices-table', [
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
            'canDeleteNotices' => auth()->user()->can('delete notice'),
        ]);
    }
}
