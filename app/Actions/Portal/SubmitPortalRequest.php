<?php

namespace App\Actions\Portal;

use App\Enums\PortalArea;
use App\Enums\PortalRequestStatus;
use App\Enums\PortalRequestType;
use App\Exceptions\InvalidValueException;
use App\Models\PortalRequest;
use App\Models\StudentRecord;
use App\Models\User;
use App\Services\Portal\PortalAccess;

/**
 * Let a family ask the school for something.
 *
 * A request changes nothing by itself. Somebody at the school reads it and
 * acts on it, which keeps the portal read-only over school records.
 */
class SubmitPortalRequest
{
    public function __construct(private PortalAccess $access) {}

    /**
     * Send a request about one student.
     *
     * @throws InvalidValueException when the area is closed, the person may not read the student, or the same request is still open
     */
    public function submit(
        StudentRecord $enrollment,
        string $subject,
        PortalRequestType $type = PortalRequestType::Document,
        ?string $message = null,
        ?User $person = null,
    ): PortalRequest {
        $person ??= auth()->user();

        if ($person === null) {
            throw new InvalidValueException('A request needs a signed-in person.');
        }

        if (!$this->access->areaIsOpen(PortalArea::Requests, $enrollment->school_id)) {
            throw new InvalidValueException('This school does not take requests through the portal.');
        }

        if (!$this->access->canRead($person, $enrollment)) {
            throw new InvalidValueException('This person cannot ask about this student.');
        }

        $alreadyAsked = PortalRequest::query()
            ->where('student_record_id', $enrollment->id)
            ->where('requested_by', $person->id)
            ->where('subject', $subject)
            ->whereIn('status', [PortalRequestStatus::Submitted, PortalRequestStatus::InReview])
            ->exists();

        if ($alreadyAsked) {
            throw new InvalidValueException('You already asked for this. The school has not answered yet.');
        }

        return PortalRequest::create([
            'school_id' => $enrollment->school_id,
            'student_record_id' => $enrollment->id,
            'requested_by' => $person->id,
            'type' => $type,
            'subject' => $subject,
            'message' => $message,
        ]);
    }

    /**
     * Take back a request the school has not finished with.
     *
     * Only the person who asked may take it back.
     *
     * @throws InvalidValueException when somebody else asked, or the school already closed it
     */
    public function withdraw(PortalRequest $request, User $person): PortalRequest
    {
        if ($request->requested_by !== $person->id) {
            throw new InvalidValueException('Only the person who asked can take this request back.');
        }

        if (!$request->status->isOpen()) {
            throw new InvalidValueException('The school has already closed this request.');
        }

        $request->status = PortalRequestStatus::Cancelled;
        $request->save();

        return $request;
    }

    /**
     * Answer a request.
     *
     * @throws InvalidValueException when the request is already finished
     */
    public function answer(PortalRequest $request, string $response, ?User $actor = null): PortalRequest
    {
        return $this->changeStatus($request, PortalRequestStatus::Answered, $actor, $response);
    }

    /**
     * Move the request to another state.
     *
     * @throws InvalidValueException when the state cannot follow the current one
     */
    public function changeStatus(
        PortalRequest $request,
        PortalRequestStatus $status,
        ?User $actor = null,
        ?string $response = null,
    ): PortalRequest {
        $current = $request->status;

        if ($current === $status) {
            return $request;
        }

        if (!$current->canMoveTo($status)) {
            throw new InvalidValueException("A request cannot move from {$current->value} to {$status->value}.");
        }

        $request->status = $status;

        if (!$status->isOpen()) {
            $request->response = $response ?? $request->response;
            $request->answered_by = $actor === null ? auth()->id() : $actor->id;
            $request->answered_at = now();
        }

        $request->save();

        return $request;
    }
}
