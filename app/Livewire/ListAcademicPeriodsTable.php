<?php

namespace App\Livewire;

use App\Models\AcademicPeriod;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Yungifez\AprilUI\Livewire\Columns\Column;
use Yungifez\AprilUI\Livewire\DataTableComponent;

class ListAcademicPeriodsTable extends DataTableComponent
{
    protected function builder(): Builder
    {
        return AcademicPeriod::query()
            ->inSchool()
            ->where('academic_year_id', current_academic_year_id())
            ->ordered();
    }

    /**
     * @return array<int, Column>
     */
    protected function columns(): array
    {
        return [
            Column::make('Name', 'name')->searchable()->sortable(),
            Column::make('Type', 'type_label'),
            Column::make('Dates', 'date_label'),
            Column::make('Status', 'status_label'),
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
        return $rows->map(function (AcademicPeriod $period): array {
            $status = $period->status;
            $row = $period->toArray();
            $row['type_label'] = $period->type->label();
            $row['date_label'] = $period->starts_on !== null && $period->ends_on !== null
                ? $period->starts_on->format('M j, Y').' – '.$period->ends_on->format('M j, Y')
                : 'Not scheduled';
            $row['status'] = $status->value;
            $row['status_label'] = $status->label();
            $row['can_close'] = auth()->user()->can('close', $period);
            $row['can_reopen'] = auth()->user()->can('reopen', $period);
            $row['edit_url'] = route('academic-periods.edit', $period);
            $row['delete_url'] = route('academic-periods.destroy', $period);
            $row['begin_closing_url'] = route('academic-periods.begin-closing', $period);
            $row['close_url'] = route('academic-periods.close', $period);
            $row['reopen_url'] = route('academic-periods.reopen', $period);

            return $row;
        })->values()->all();
    }

    public function render(): View
    {
        $rows = $this->rows();
        $columns = $this->columns();

        return view('livewire.list-academic-periods-table', [
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
            'canEditPeriods' => auth()->user()->can('update academic period'),
            'canDeletePeriods' => auth()->user()->can('delete academic period'),
        ]);
    }
}
