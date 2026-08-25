<?php

namespace App\Livewire;

use App\Livewire\Concerns\InteractsWithAprilTable;
use App\Models\School;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Yungifez\AprilUI\Livewire\Columns\Column;
use Yungifez\AprilUI\Livewire\DataTableComponent;

class ListSchoolsTable extends DataTableComponent
{
    use InteractsWithAprilTable;

    protected function builder(): Builder
    {
        return School::query()->orderBy('name');
    }

    /** @return array<int, Column> */
    protected function columns(): array
    {
        return [
            Column::make('Name', 'name')->searchable()->sortable(),
            Column::make('Initials', 'initials'),
            Column::make('Address', 'address'),
            Column::make('Code', 'code')->searchable(),
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
        return $rows->map(function (School $school): array {
            $row = $school->toArray();
            $row['edit_url'] = route('schools.edit', $school);
            $row['view_url'] = route('schools.show', $school);
            $row['delete_url'] = route('schools.destroy', $school);

            return $row;
        })->values()->all();
    }

    public function render(): View
    {
        return view('livewire.list-schools-table', array_merge(
            $this->aprilTablePayload(),
            [
                'canEditSchools' => auth()->user()->can('update school'),
                'canDeleteSchools' => auth()->user()->can('delete school'),
            ],
        ));
    }
}
