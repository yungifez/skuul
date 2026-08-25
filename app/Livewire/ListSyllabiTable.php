<?php

namespace App\Livewire;

use App\Livewire\Concerns\InteractsWithAprilTable;
use App\Models\Syllabus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Yungifez\AprilUI\Livewire\Columns\Column;
use Yungifez\AprilUI\Livewire\DataTableComponent;

class ListSyllabiTable extends DataTableComponent
{
    use InteractsWithAprilTable;

    protected function builder(): Builder
    {
        return Syllabus::query()->inSchool()->with(['courseOffering.subject', 'courseOffering.academicLevel', 'courseOffering.academicPeriod'])->latest();
    }

    /** @return array<int, Column> */
    protected function columns(): array
    {
        return [
            Column::make('Name', 'name')->searchable()->sortable(),
            Column::make('Subject', 'subject_name'),
            Column::make('Academic level', 'academic_level_label'),
            Column::make('Academic period', 'academic_period_label'),
        ];
    }

    /** @return array<int, array<string, mixed>> */
    protected function serializeRows(Collection $rows): array
    {
        return $rows->map(function (Syllabus $syllabus): array {
            $row = $syllabus->toArray();
            $row['subject_name'] = $syllabus->courseOffering->subject->name;
            $row['academic_level_label'] = $syllabus->courseOffering->academicLevel->label;
            $row['academic_period_label'] = $syllabus->courseOffering->academicPeriod->label;
            $row['view_url'] = route('syllabi.show', $syllabus);
            $row['delete_url'] = route('syllabi.destroy', $syllabus);

            return $row;
        })->values()->all();
    }

    public function render(): View
    {
        return view('livewire.list-syllabi-table', array_merge(
            $this->aprilTablePayload(),
            [
                'canReadSyllabi' => auth()->user()->can('read syllabus'),
                'canDeleteSyllabi' => auth()->user()->can('delete syllabus'),
            ],
        ));
    }
}
