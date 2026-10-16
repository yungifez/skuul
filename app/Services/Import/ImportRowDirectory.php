<?php

namespace App\Services\Import;

use App\Enums\ImportRowState;
use App\Models\ImportBatch;
use App\Models\ImportRow;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ImportRowDirectory
{
    public function rows(ImportBatch $batch, ?ImportRowState $state): LengthAwarePaginator
    {
        return $batch->rows()
            ->when($state !== null, function ($query) use ($state): void {
                $query->where('state', $state);
            })
            ->paginate(50);
    }

    /** @return array<int, string> */
    public function columns(ImportBatch $batch): array
    {
        $firstRow = $batch->rows()->first();

        return $firstRow instanceof ImportRow ? array_keys($firstRow->payload) : [];
    }
}
