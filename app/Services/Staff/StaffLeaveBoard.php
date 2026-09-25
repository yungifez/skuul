<?php

namespace App\Services\Staff;

use App\Enums\LeaveStatus;
use App\Enums\LeaveType;
use App\Models\StaffLeaveRequest;
use App\Models\StaffProfile;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Read the staff leave board within the working school.
 */
class StaffLeaveBoard
{
    /**
     * Get the leave requests matching the selected filters.
     */
    public function requests(?string $status, ?string $type): LengthAwarePaginator
    {
        $selectedStatus = LeaveStatus::tryFrom((string) $status);
        $selectedType = LeaveType::tryFrom((string) $type);

        return StaffLeaveRequest::query()
            ->inSchool()
            ->with(['staffProfile.user:id,name', 'decidedBy:id,name'])
            ->when($selectedStatus !== null, function ($query) use ($selectedStatus): void {
                $query->where('status', $selectedStatus);
            })
            ->when($selectedType !== null, function ($query) use ($selectedType): void {
                $query->where('type', $selectedType);
            })
            ->orderByDesc('starts_on')
            ->paginate(20)
            ->withQueryString();
    }

    /**
     * Get the employed people who can ask for leave.
     *
     * @return Collection<int, StaffProfile>
     */
    public function employedProfiles(): Collection
    {
        return StaffProfile::query()
            ->inSchool()
            ->employed()
            ->with('user:id,name')
            ->orderBy('id')
            ->get();
    }

    /**
     * Find an employment record in the working school.
     */
    public function profile(int $profileId): StaffProfile
    {
        return StaffProfile::query()->inSchool()->findOrFail($profileId);
    }

    /**
     * Count requests that still need an answer.
     */
    public function waitingCount(): int
    {
        return StaffLeaveRequest::query()
            ->inSchool()
            ->where('status', LeaveStatus::Requested)
            ->count();
    }

    /**
     * Get employed people who are away today.
     *
     * @return Collection<int, StaffProfile>
     */
    public function awayToday(): Collection
    {
        return StaffProfile::query()
            ->inSchool()
            ->awayOn(now())
            ->with('user:id,name')
            ->get();
    }

    /**
     * Find a leave request in the working school.
     */
    public function leaveRequest(int $requestId): StaffLeaveRequest
    {
        return StaffLeaveRequest::query()->inSchool()->findOrFail($requestId);
    }
}
