<?php

namespace App\Livewire;

use App\Models\AcademicYear;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Yungifez\AprilUI\Livewire\Columns\Column;
use Yungifez\AprilUI\Livewire\DataTableComponent;

class ListAcademicYearsTable extends DataTableComponent
{
    protected function builder(): Builder
    {
        return AcademicYear::query()->inSchool()->with('topLevelPeriods')->orderByDesc('starts_on')->orderByDesc('id');
    }

    /** @return array<int, Column> */
    protected function columns(): array
    {
        return [
            Column::make(school_term('academic_year', 'School year'), 'name'),
            Column::make('Dates', 'date_label'),
            Column::make('Structure', 'structure_label'),
            Column::make('Status', 'status_label'),
            Column::make('Working '.strtolower(school_term('academic_year', 'school year')), 'working_label'),
        ];
    }

    /** @return array{field: string, direction: string} */
    protected function defaultSort(): ?array
    {
        return ['field' => 'starts_on', 'direction' => 'desc'];
    }

    /** @return array<int, array<string, mixed>> */
    protected function serializeRows(Collection $rows): array
    {
        return $rows->map(function (AcademicYear $academicYear): array {
            $status = $academicYear->status;
            $row = $academicYear->toArray();
            $row['name'] = $academicYear->name;
            $row['date_label'] = $academicYear->starts_on !== null && $academicYear->ends_on !== null
                ? $academicYear->starts_on->format('M j, Y').' – '.$academicYear->ends_on->format('M j, Y')
                : 'Not scheduled';
            $periods = $academicYear->topLevelPeriods;
            $types = $periods->pluck('type')->unique();
            $row['structure_label'] = $periods->isEmpty()
                ? 'Not configured'
                : ($types->count() === 1 ? $periods->count().' '.$types->first()->label().($periods->count() === 1 ? '' : 's') : $periods->count().' reporting periods');
            $row['status'] = $status->value;
            $row['status_label'] = $status->label();
            $row['working_label'] = current_academic_year_id() === $academicYear->id ? 'Working '.strtolower(school_term('academic_year', 'school year')) : '—';
            $row['can_close'] = auth()->user()->can('close', $academicYear);
            $row['can_reopen'] = auth()->user()->can('reopen', $academicYear);
            $row['view_url'] = route('academic-years.show', $academicYear);
            $row['delete_url'] = route('academic-years.destroy', $academicYear);
            $row['begin_closing_url'] = route('academic-years.begin-closing', $academicYear);
            $row['close_url'] = route('academic-years.close', $academicYear);
            $row['reopen_url'] = route('academic-years.reopen', $academicYear);

            return $row;
        })->values()->all();
    }

    public function render(): View
    {
        $rows = $this->rows();
        $columns = $this->columns();

        return view('livewire.list-academic-years-table', [
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
            'canDeleteYears' => auth()->user()->can('delete academic year'),
        ]);
    }
}
