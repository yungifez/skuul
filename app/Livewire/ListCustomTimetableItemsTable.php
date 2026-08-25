<?php

namespace App\Livewire;

use App\Livewire\Concerns\InteractsWithAprilTable;
use App\Models\CustomTimetableItem;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Yungifez\AprilUI\Livewire\Columns\Column;
use Yungifez\AprilUI\Livewire\DataTableComponent;

class ListCustomTimetableItemsTable extends DataTableComponent
{
    use InteractsWithAprilTable;

    protected function builder(): Builder
    {
        return CustomTimetableItem::query()->inSchool()->orderBy('name');
    }

    /** @return array<int, Column> */
    protected function columns(): array
    {
        return [Column::make('Name', 'name')->searchable()->sortable()];
    }

    /** @return array{field: string, direction: string} */
    protected function defaultSort(): ?array
    {
        return ['field' => 'name', 'direction' => 'asc'];
    }

    /** @return array<int, array<string, mixed>> */
    protected function serializeRows(Collection $rows): array
    {
        return $rows->map(function (CustomTimetableItem $item): array {
            $row = $item->toArray();
            $row['edit_url'] = route('custom-timetable-items.edit', $item);
            $row['delete_url'] = route('custom-timetable-items.destroy', $item);

            return $row;
        })->values()->all();
    }

    public function render(): View
    {
        return view('livewire.list-custom-timetable-items-table', array_merge(
            $this->aprilTablePayload(),
            [
                'canEditItems' => auth()->user()->can('update custom timetable item'),
                'canDeleteItems' => auth()->user()->can('delete custom timetable item'),
            ],
        ));
    }
}
