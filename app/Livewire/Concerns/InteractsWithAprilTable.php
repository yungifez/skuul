<?php

namespace App\Livewire\Concerns;

use Illuminate\Support\Collection;
use Yungifez\AprilUI\Livewire\Columns\Column;

trait InteractsWithAprilTable
{
    /**
     * @return array<string, mixed>
     */
    protected function aprilTablePayload(): array
    {
        $rows = $this->rows();
        $columns = $this->columns();

        return [
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
        ];
    }

    /**
     * @param  Collection<int, mixed>  $rows
     * @return array<int, array<string, mixed>>
     */
    abstract protected function serializeRows(Collection $rows): array;
}
