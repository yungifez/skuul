<?php

namespace App\Livewire;

use App\Livewire\Concerns\InteractsWithAprilTable;
use App\Models\Subject;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Yungifez\AprilUI\Livewire\Columns\Column;
use Yungifez\AprilUI\Livewire\DataTableComponent;

class ListSubjectsTable extends DataTableComponent
{
    use InteractsWithAprilTable;

    protected function builder(): Builder
    {
        return Subject::query()->inSchool()->withCount('courseOfferings')->orderBy('name');
    }

    /** @return array<int, Column> */
    protected function columns(): array
    {
        return [
            Column::make('Name', 'name')->searchable()->sortable(),
            Column::make('Short name', 'short_name'),
            Column::make('Course offerings', 'course_offerings_count')->sortable(),
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
        return $rows->map(function (Subject $subject): array {
            $row = $subject->toArray();
            $row['edit_url'] = route('subjects.edit', $subject);
            $row['delete_url'] = route('subjects.destroy', $subject);

            return $row;
        })->values()->all();
    }

    public function render(): View
    {
        return view('livewire.list-subjects-table', array_merge(
            $this->aprilTablePayload(),
            [
                'canEditSubjects' => auth()->user()->can('update subject'),
                'canDeleteSubjects' => auth()->user()->can('delete subject'),
            ],
        ));
    }
}
