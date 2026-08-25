<?php

namespace App\Livewire;

use App\Livewire\Concerns\InteractsWithAprilTable;
use App\Models\FeeCategory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Yungifez\AprilUI\Livewire\Columns\Column;
use Yungifez\AprilUI\Livewire\DataTableComponent;

class ListFeeCategoriesTable extends DataTableComponent
{
    use InteractsWithAprilTable;

    protected function builder(): Builder
    {
        return FeeCategory::query()->inSchool()->orderBy('name');
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
        return $rows->map(function (FeeCategory $category): array {
            $row = $category->toArray();
            $row['edit_url'] = route('fee-categories.edit', $category);
            $row['delete_url'] = route('fee-categories.destroy', $category);

            return $row;
        })->values()->all();
    }

    public function render(): View
    {
        return view('livewire.list-fee-categories-table', array_merge(
            $this->aprilTablePayload(),
            [
                'canEditCategories' => auth()->user()->can('update fee category'),
                'canDeleteCategories' => auth()->user()->can('delete fee category'),
            ],
        ));
    }
}
