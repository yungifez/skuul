<?php

namespace App\Services\Cohorts;

use App\Enums\ParticipationStatus;
use App\Enums\ProgramType;
use App\Models\Program;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * List the programmes available in the current school.
 */
class ProgramDirectory
{
    public function programs(?ProgramType $type, bool $activeOnly): LengthAwarePaginator
    {
        return Program::query()
            ->inSchool()
            // A running place is requested or active; the relation closure gets a plain builder.
            ->withCount(['participations as running_count' => function (Builder $query): void {
                $query->whereIn('status', [ParticipationStatus::Requested->value, ParticipationStatus::Active->value]);
            }])
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
