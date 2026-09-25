<?php

namespace App\Services\Cohorts;

use App\Enums\CohortType;
use App\Models\Cohort;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * List the groups that the current staff member may read.
 */
class CohortDirectory
{
    public function groups(?CohortType $type, bool $activeOnly, bool $mayReadRestricted): LengthAwarePaginator
    {
        return Cohort::query()
            ->inSchool()
            // The relation closure gets a plain builder, so use the current-member condition directly.
            ->withCount(['members as current_members_count' => function (Builder $query): void {
                $query->whereNull('left_on');
            }])
            ->when(!$mayReadRestricted, function (Builder $query): void {
                $query->where('is_restricted', false);
            })
            ->when($type !== null, function (Builder $query) use ($type): void {
                $query->where('type', $type);
            })
            ->when($activeOnly, function (Builder $query): void {
                $query->active();
            })
            ->orderBy('name')
            ->paginate(20);
    }
}
