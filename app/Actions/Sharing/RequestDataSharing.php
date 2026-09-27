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
use Illuminate\Support\Facades\DB;

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
     * @throws InvalidValueException when the schools are the same, no category is named, the end date has passed,
     *                               or this school still has an open request for the learner
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

        return DB::transaction(function () use ($enrollment, $requestingSchool, $purpose, $categories, $expiry, $actor): DataSharingRequest {
            // Holding the asking school keeps a double click from sending two asks.
            School::query()->whereKey($requestingSchool->id)->lockForUpdate()->first();

            $stillOpen = DataSharingRequest::query()
                ->where('requesting_school_id', $requestingSchool->id)
                ->where('student_record_id', $enrollment->id)
                ->whereIn('status', [DataSharingStatus::Requested, DataSharingStatus::Approved])
                ->get()
                ->contains(fn (DataSharingRequest $open): bool => !$open->hasExpired());

            if ($stillOpen) {
                throw new InvalidValueException('This school already has an open request for that learner. Wait for its answer before asking again.');
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
        });
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
     * @throws InvalidValueException when the state cannot follow the current one, or an
     *                               approval comes after the request ran out
     */
    public function changeStatus(
        DataSharingRequest $request,
        DataSharingStatus $status,
        ?User $actor = null,
        ?string $note = null,
    ): DataSharingRequest {
        return DB::transaction(function () use ($request, $status, $actor, $note): DataSharingRequest {
            // Two people answering at once must see each other's answer.
            $locked = DataSharingRequest::query()->lockForUpdate()->findOrFail($request->id);
            $current = $locked->status;

            if ($current === $status) {
                return $locked;
            }

            if (!$current->canMoveTo($status)) {
                throw new InvalidValueException("This request is already {$current->label()}, so it cannot be {$status->label()}.");
            }

            if ($status === DataSharingStatus::Approved && $locked->hasExpired()) {
                throw new InvalidValueException('This request ran out before it was answered. The other school must ask again.');
            }

            $locked->status = $status;
            $locked->decided_by = $actor === null ? auth()->id() : $actor->id;
            $locked->decided_at = now();
            $locked->decision_note = $note ?? $locked->decision_note;
            $locked->save();

            $this->auditor->record(
                AuditAction::DataSharingStatusChanged,
                $locked,
                ['from' => $current->value, 'to' => $status->value, 'note' => $note],
                $actor,
                $locked->holding_school_id,
            );

            return $locked;
        });
    }
}
