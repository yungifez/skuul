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
    public function __construct(private PortalAccess $access)
    {
    }

    /**
     * Send a request about one student.
     *
     * @throws InvalidValueException when the area is closed or the person may not read the student
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

        return PortalRequest::create([
            'school_id'         => $enrollment->school_id,
            'student_record_id' => $enrollment->id,
            'requested_by'      => $person->id,
            'type'              => $type,
            'subject'           => $subject,
            'message'           => $message,
        ]);
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
