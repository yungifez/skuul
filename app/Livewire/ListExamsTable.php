<?php

namespace App\Livewire;

use App\Livewire\Concerns\InteractsWithAprilTable;
use App\Models\Exam;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Yungifez\AprilUI\Livewire\Columns\Column;
use Yungifez\AprilUI\Livewire\DataTableComponent;

class ListExamsTable extends DataTableComponent
{
    use InteractsWithAprilTable;

    protected function builder(): Builder
    {
        return Exam::query()->where('academic_period_id', current_school()?->academicPeriod?->id)->with('academicPeriod')->orderByDesc('start_date');
    }

    /** @return array<int, Column> */
    protected function columns(): array
    {
        return [Column::make('Name', 'name')->searchable()->sortable(), Column::make(school_term('period', 'Period'), 'period_label'), Column::make('Starts', 'start_date_label'), Column::make('Ends', 'stop_date_label'), Column::make('Status', 'active_label')];
    }

    /** @return array<int, array<string, mixed>> */
    protected function serializeRows(Collection $rows): array
    {
        return $rows->map(function (Exam $exam): array {
            $row = $exam->toArray();
            $row['period_label'] = $exam->academicPeriod->label ?? $exam->academicPeriod->name ?? '—';
            $row['start_date_label'] = $exam->start_date->format('M j, Y');
            $row['stop_date_label'] = $exam->stop_date->format('M j, Y');
            $row['active_label'] = $exam->active ? 'Active' : 'Inactive';
            $row['edit_url'] = route('exams.edit', $exam);
            $row['delete_url'] = route('exams.destroy', $exam);

            return $row;
        })->values()->all();
    }

    public function render(): View
    {
        return view('livewire.list-exams-table', array_merge($this->aprilTablePayload(), ['canUpdateExam' => auth()->user()->can('update exam'), 'canDeleteExam' => auth()->user()->can('delete exam')]));
    }
}
