<?php

namespace App\Services\Discipline;

use App\Enums\IncidentCategory;
use App\Enums\IncidentStatus;
use App\Models\Incident;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

class IncidentDirectory
{
    public function cases(
        User $reader,
        ?IncidentStatus $status,
        ?IncidentCategory $category,
        bool $openOnly,
    ): LengthAwarePaginator {
        return Incident::query()
            ->inSchool()
            ->readableBy($reader)
            ->with(['assignedTo:id,name', 'reportedBy:id,name'])
            ->withCount('participants')
            ->when($status !== null, function (Builder $query) use ($status): void {
                $query->where('status', $status);
            })
            ->when($category !== null, function (Builder $query) use ($category): void {
                $query->where('category', $category);
            })
            ->when($openOnly, function (Builder $query): void {
                $query->open();
            })
            ->latest('occurred_at')
            ->paginate(20);
    }

    public function openCount(User $reader): int
    {
        return Incident::query()
            ->inSchool()
            ->readableBy($reader)
            ->open()
            ->count();
    }
}
