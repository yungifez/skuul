<?php

namespace App\Livewire;

use App\Livewire\Concerns\InteractsWithAprilTable;
use App\Models\Exam;
use App\Services\Exam\ExamService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
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

            return $row;
        })->values()->all();
    }

    /**
     * Delete one exam of the working school.
     */
    public function deleteExam(int $examId, ExamService $examService): void
    {
        $exam = Exam::query()->whereRelation('academicPeriod', 'school_id', current_school_id())->findOrFail($examId);
        Gate::authorize('delete', $exam);

        $this->changeRow(fn () => $examService->deleteExam($exam), "{$exam->name} was deleted.");
    }

    public function render(): View
    {
        return view('livewire.list-exams-table', array_merge($this->aprilTablePayload(), ['canUpdateExam' => auth()->user()->can('update exam'), 'canDeleteExam' => auth()->user()->can('delete exam')]));
    }
}
