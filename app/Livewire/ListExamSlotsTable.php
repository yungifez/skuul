<?php

namespace App\Livewire;

use App\Livewire\Concerns\InteractsWithAprilTable;
use App\Models\Exam;
use App\Models\ExamSlot;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Yungifez\AprilUI\Livewire\Columns\Column;
use Yungifez\AprilUI\Livewire\DataTableComponent;

class ListExamSlotsTable extends DataTableComponent
{
    use InteractsWithAprilTable;

    public Exam $exam;

    protected function builder(): Builder
    {
        return ExamSlot::query()->where('exam_id', $this->exam->id)->with('exam.academicPeriod')->orderBy('name');
    }

    /** @return array<int, Column> */
    protected function columns(): array
    {
        return [Column::make('Name', 'name')->searchable()->sortable(), Column::make('Period', 'period_label'), Column::make('Description', 'description'), Column::make('Total marks', 'total_marks')->sortable()];
    }

    /** @return array<int, array<string, mixed>> */
    protected function serializeRows(Collection $rows): array
    {
        return $rows->map(function (ExamSlot $slot): array {
            $row = $slot->toArray();
            $row['period_label'] = $slot->exam->academicPeriod->label ?? $slot->exam->academicPeriod->name ?? '—';
            $row['edit_url'] = route('exam-slots.edit', [$this->exam, $slot]);
            $row['delete_url'] = route('exam-slots.destroy', [$this->exam, $slot]);

            return $row;
        })->values()->all();
    }

    public function render(): View
    {
        return view('livewire.list-exam-slots-table', array_merge($this->aprilTablePayload(), ['canEditSlots' => auth()->user()->can('update exam slot'), 'canDeleteSlots' => auth()->user()->can('delete exam slot')]));
    }
}
