<?php

namespace App\Services\Wellbeing;

use App\Enums\SupportCategory;
use App\Enums\SupportPlanStatus;
use App\Models\SupportPlan;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

class SupportPlanDirectory
{
    public function plans(
        User $reader,
        ?SupportPlanStatus $status,
        ?SupportCategory $category,
        bool $dueOnly,
    ): LengthAwarePaginator {
        return SupportPlan::query()
            ->inSchool()
            ->readableBy($reader)
            ->with(['studentRecord.user:id,name', 'assignedTo:id,name'])
            ->withCount(['actions', 'notes'])
            ->when($status !== null, function (Builder $query) use ($status): void {
                $query->where('status', $status);
            })
            ->when($category !== null, function (Builder $query) use ($category): void {
                $query->where('category', $category);
            })
            ->when($dueOnly, function (Builder $query): void {
                $query->dueForReview();
            })
            ->latest('id')
            ->paginate(20);
    }

    public function openCount(User $reader): int
    {
        return SupportPlan::query()
            ->inSchool()
            ->readableBy($reader)
            ->open()
            ->count();
    }

    public function dueCount(User $reader): int
    {
        return SupportPlan::query()
            ->inSchool()
            ->readableBy($reader)
            ->dueForReview()
            ->count();
    }
}
