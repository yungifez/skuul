<?php

namespace App\Services\Portal;

use App\Enums\PortalRequestStatus;
use App\Enums\PortalRequestType;
use App\Models\PortalRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Read family requests for the working school's inbox.
 */
class PortalRequestInbox
{
    /**
     * Get the requests matching the selected filters.
     */
    public function requests(string $status, string $type): LengthAwarePaginator
    {
        $selectedStatus = PortalRequestStatus::tryFrom($status);
        $selectedType = PortalRequestType::tryFrom($type);

        return PortalRequest::query()
            ->inSchool()
            ->with(['studentRecord.user:id,name', 'requestedBy:id,name', 'answeredBy:id,name'])
            ->when($selectedStatus !== null, function (Builder $query) use ($selectedStatus): void {
                $query->where('status', $selectedStatus);
            })
            ->when($selectedType !== null, function (Builder $query) use ($selectedType): void {
                $query->where('type', $selectedType);
            })
            ->latest('id')
            ->paginate(20)
            ->withQueryString();
    }

    /**
     * Count requests waiting for the school.
     */
    public function waitingCount(): int
    {
        return PortalRequest::query()->inSchool()->open()->count();
    }

    /**
     * Find a request in the working school.
     */
    public function request(int $requestId): PortalRequest
    {
        return PortalRequest::query()->inSchool()->findOrFail($requestId);
    }
}
