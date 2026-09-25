<?php

namespace App\Services\Staff;

use App\Enums\StaffStatus;
use App\Models\StaffProfile;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Search and count employment records in the working school.
 */
class StaffProfileDirectory
{
    public function profiles(string $search, string $status, bool $awayOnly): LengthAwarePaginator
    {
        $selectedStatus = StaffStatus::tryFrom($status);
        $term = trim($search);

        return StaffProfile::query()
            ->inSchool()
            ->with('user:id,name,email')
            ->withCount('credentials')
            ->when($term !== '', function (Builder $query) use ($term): void {
                $query->where(function (Builder $query) use ($term): void {
                    $query->where('staff_number', 'like', "%$term%")
                        ->orWhere('job_title', 'like', "%$term%")
                        ->orWhere('department', 'like', "%$term%")
                        ->orWhereHas('user', function (Builder $query) use ($term): void {
                            $query->where('name', 'like', "%$term%");
                        });
                });
            })
            ->when($selectedStatus !== null, function (Builder $query) use ($selectedStatus): void {
                $query->where('status', $selectedStatus);
            })
            ->when($awayOnly, function (Builder $query): void {
                $query->awayOn(now());
            })
            ->orderBy('id')
            ->paginate(20)
            ->withQueryString();
    }

    public function employedCount(): int
    {
        return StaffProfile::query()->inSchool()->employed()->count();
    }

    public function awayCount(): int
    {
        return StaffProfile::query()->inSchool()->awayOn(now())->count();
    }
}
