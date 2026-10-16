<?php

namespace App\Services\Import;

use App\Enums\ImportStatus;
use App\Models\ImportBatch;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

class ImportBatchDirectory
{
    public function batches(?string $type, ?ImportStatus $status): LengthAwarePaginator
    {
        return ImportBatch::query()
            ->inSchool()
            ->with('createdBy:id,name')
            ->when($type !== null, function (Builder $query) use ($type): void {
                $query->where('type', $type);
            })
            ->when($status !== null, function (Builder $query) use ($status): void {
                $query->where('status', $status);
            })
            ->latest('id')
            ->paginate(20);
    }
}
