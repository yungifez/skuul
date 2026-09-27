<?php

namespace App\Livewire\Concerns;

use App\Exceptions\ApplicationException;
use Illuminate\Support\Collection;
use Yungifez\AprilUI\Livewire\Columns\Column;

trait InteractsWithAprilTable
{
    use DispatchesStatusNotifications;

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
     * Run a change to one row, then redraw the table.
     *
     * A refused change shows its reason. When the change empties the last
     * page, the table steps back one page so it never shows an empty page.
     */
    protected function changeRow(callable $change, string $success): void
    {
        try {
            $change();
        } catch (ApplicationException $exception) {
            $this->notify($exception->getMessage(), 'danger');

            return;
        }

        if ($this->getPage() > 1 && $this->rows()->isEmpty()) {
            $this->previousPage();
        }

        $this->tableRevision++;
        $this->notify($success);
    }

    /**
     * @param  Collection<int, mixed>  $rows
     * @return array<int, array<string, mixed>>
     */
    abstract protected function serializeRows(Collection $rows): array;
}
