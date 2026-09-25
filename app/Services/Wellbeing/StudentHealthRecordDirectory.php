<?php

namespace App\Services\Wellbeing;

use App\Models\StudentRecord;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

class StudentHealthRecordDirectory
{
    public function learners(string $search, bool $missingOnly): LengthAwarePaginator
    {
        return StudentRecord::query()
            ->inSchool()
            ->with(['user:id,name', 'healthRecord'])
            ->when($search !== '', function (Builder $query) use ($search): void {
                $query->where(function (Builder $query) use ($search): void {
                    $query->where('admission_number', 'like', "%$search%")
                        ->orWhereHas('user', function (Builder $query) use ($search): void {
                            $query->where('name', 'like', "%$search%");
                        });
                });
            })
            ->when($missingOnly, function (Builder $query): void {
                $query->whereDoesntHave('healthRecord');
            })
            ->orderBy('admission_number')
            ->paginate(20);
    }

    public function recordedCount(): int
    {
        return StudentRecord::query()->inSchool()->whereHas('healthRecord')->count();
    }

    public function learnerCount(): int
    {
        return StudentRecord::query()->inSchool()->count();
    }
}
