<?php

namespace App\Livewire;

use App\Livewire\Concerns\InteractsWithAprilTable;
use App\Models\Fee;
use App\Services\Fee\FeeService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Yungifez\AprilUI\Livewire\Columns\Column;
use Yungifez\AprilUI\Livewire\DataTableComponent;

class ListFeesTable extends DataTableComponent
{
    use InteractsWithAprilTable;

    protected function builder(): Builder
    {
        return Fee::query()->whereRelation('feeCategory', 'school_id', current_school_id())->with('feeCategory')->withCount('feeInvoiceRecords')->orderBy('name');
    }

    /** @return array<int, Column> */
    protected function columns(): array
    {
        return [
            Column::make('Name', 'name')->searchable()->sortable(),
            Column::make('Fee category', 'fee_category_name'),
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
        return $rows->map(function (Fee $fee): array {
            $row = $fee->toArray();
            $row['fee_category_name'] = $fee->feeCategory->name;
            $row['edit_url'] = route('fees.edit', $fee);

            return $row;
        })->values()->all();
    }

    /**
     * Delete one fee. A fee that is on an invoice is refused.
     */
    public function deleteFee(int $feeId, FeeService $feeService): void
    {
        $fee = Fee::query()->whereRelation('feeCategory', 'school_id', current_school_id())->findOrFail($feeId);
        Gate::authorize('delete', $fee);

        $this->changeRow(fn () => $feeService->deleteFee($fee), "{$fee->name} was deleted.");
    }

    public function render(): View
    {
        return view('livewire.list-fees-table', array_merge(
            $this->aprilTablePayload(),
            [
                'canEditFees' => auth()->user()->can('update fee'),
                'canDeleteFees' => auth()->user()->can('delete fee'),
            ],
        ));
    }
}
