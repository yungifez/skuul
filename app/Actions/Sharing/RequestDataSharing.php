<?php

namespace App\Actions\Sharing;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AuditAction;
use App\Enums\DataCategory;
use App\Enums\DataSharingStatus;
use App\Exceptions\InvalidValueException;
use App\Models\DataSharingRequest;
use App\Models\School;
use App\Models\StudentRecord;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;

/**
 * Ask another school for a student's records, and answer such a request.
 *
 * Asking, approving, and handing over are three separate decisions. This
 * action covers the first two; handing the records over is
 * {@see FulfilDataSharingRequest}.
 */
class RequestDataSharing
{
    public function __construct(private RecordAuditEvent $auditor) {}

    /**
     * Ask the school that holds the records.
     *
     * @param  array<int, DataCategory>  $categories
     *
     * @throws InvalidValueException when the schools are the same, no category is named, or the end date has passed
     */
    public function request(
        StudentRecord $enrollment,
        School $requestingSchool,
        string $purpose,
        array $categories,
        CarbonInterface|string|null $expiresOn = null,
        ?User $actor = null,
    ): DataSharingRequest {
        if ($enrollment->school_id === $requestingSchool->id) {
            throw new InvalidValueException('A school does not ask itself for its own records.');
        }

        if ($categories === []) {
            throw new InvalidValueException('A request must name what it asks for.');
        }

        $expiry = $expiresOn === null ? null : Carbon::parse($expiresOn)->startOfDay();

        if ($expiry !== null && $expiry->lt(now()->startOfDay())) {
            throw new InvalidValueException('A request cannot end before it starts.');
        }

        $request = DataSharingRequest::create([
            'requesting_school_id' => $requestingSchool->id,
            'holding_school_id' => $enrollment->school_id,
            'student_record_id' => $enrollment->id,
            'categories' => array_values(array_unique(array_map(
                fn (DataCategory $category): string => $category->value,
                $categories,
            ))),
            'purpose' => $purpose,
            'expires_on' => $expiry,
            'requested_by' => $actor === null ? auth()->id() : $actor->id,
        ]);

        $this->auditor->record(
            AuditAction::DataSharingRequested,
            $request,
            ['categories' => $request->categories, 'requesting_school_id' => $requestingSchool->id],
            $actor,
            $enrollment->school_id,
        );

        return $request;
    }

    /**
     * Agree to share the records.
     */
    public function approve(DataSharingRequest $request, ?User $actor = null, ?string $note = null): DataSharingRequest
    {
        return $this->changeStatus($request, DataSharingStatus::Approved, $actor, $note);
    }

    /**
     * Say no.
     */
    public function decline(DataSharingRequest $request, ?User $actor = null, ?string $note = null): DataSharingRequest
    {
        return $this->changeStatus($request, DataSharingStatus::Declined, $actor, $note);
    }

    /**
     * Take the permission back.
     */
    public function revoke(DataSharingRequest $request, ?User $actor = null, ?string $note = null): DataSharingRequest
    {
        return $this->changeStatus($request, DataSharingStatus::Revoked, $actor, $note);
    }

    /**
     * Move the request to another state.
     *
     * @throws InvalidValueException when the state cannot follow the current one
     */
    public function changeStatus(
        DataSharingRequest $request,
        DataSharingStatus $status,
        ?User $actor = null,
        ?string $note = null,
    ): DataSharingRequest {
        $current = $request->status;

        if ($current === $status) {
            return $request;
        }

        if (!$current->canMoveTo($status)) {
            throw new InvalidValueException("A request cannot move from {$current->value} to {$status->value}.");
        }

        $request->status = $status;
        $request->decided_by = $actor === null ? auth()->id() : $actor->id;
        $request->decided_at = now();
        $request->decision_note = $note ?? $request->decision_note;
        $request->save();

        $this->auditor->record(
            AuditAction::DataSharingStatusChanged,
            $request,
            ['from' => $current->value, 'to' => $status->value, 'note' => $note],
            $actor,
            $request->holding_school_id,
        );

        return $request;
    }
}
