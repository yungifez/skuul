<?php

namespace App\Actions\Enrollment;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AuditAction;
use App\Enums\CampusMoveStatus;
use App\Exceptions\InvalidValueException;
use App\Models\AcademicCycleSection;
use App\Models\CampusMoveRequest;
use App\Models\StudentRecord;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Ask the other campus to take a student, and answer such a request.
 *
 * A person with organization authority moves a student straight away with
 * {@see MoveEnrollmentBetweenCampuses}. A campus administrator cannot: the
 * campus that receives the student has to agree first. Approving performs the
 * move in the same transaction, so no request can sit approved but unapplied.
 */
class RequestCampusMove
{
    public function __construct(
        private MoveEnrollmentBetweenCampuses $moveEnrollment,
        private RecordAuditEvent $auditor,
    ) {
    }

    /**
     * Ask the campus that owns the given cycle section to take the student.
     *
     * @throws InvalidValueException when the move itself could never be made
     */
    public function request(
        StudentRecord $enrollment,
        AcademicCycleSection $academicCycleSection,
        ?User $actor = null,
        ?string $reason = null,
        CarbonInterface|string|null $effectiveOn = null,
    ): CampusMoveRequest {
        $academicCycleSection->loadMissing('school');
        $enrollment->loadMissing('school');

        $this->failIfTheMoveCouldNeverBeMade($enrollment, $academicCycleSection);

        if ($this->openRequestFor($enrollment) !== null) {
            throw new InvalidValueException('This student already has a campus move waiting for a decision.');
        }

        $request = CampusMoveRequest::create([
            'student_record_id'         => $enrollment->id,
            'from_school_id'            => $enrollment->school_id,
            'to_school_id'              => $academicCycleSection->school_id,
            'academic_cycle_section_id' => $academicCycleSection->id,
            'reason'                    => $reason,
            'effective_on'              => $effectiveOn === null ? now()->toDateString() : Carbon::parse($effectiveOn)->toDateString(),
            'requested_by'              => $actor === null ? auth()->id() : $actor->id,
        ]);

        $this->auditor->record(
            AuditAction::CampusMoveRequested,
            $request,
            [
                'student_record_id' => $enrollment->id,
                'from_school_id'    => $request->from_school_id,
                'to_school_id'      => $request->to_school_id,
                'reason'            => $reason,
            ],
            $actor,
            $request->to_school_id,
        );

        return $request;
    }

    /**
     * Agree, and move the student in the same breath.
     */
    public function approve(CampusMoveRequest $request, ?User $actor = null, ?string $note = null): CampusMoveRequest
    {
        return DB::transaction(function () use ($request, $actor, $note): CampusMoveRequest {
            $request = CampusMoveRequest::query()->lockForUpdate()->findOrFail($request->getKey());

            $this->failIfTheRequestCannotMoveTo($request, CampusMoveStatus::Approved);

            $this->moveEnrollment->move(
                enrollment: $request->studentRecord,
                academicCycleSection: $request->academicCycleSection,
                actor: $actor,
                reason: $request->reason,
                effectiveOn: $request->effective_on,
            );

            return $this->writeDecision($request, CampusMoveStatus::Approved, $actor, $note);
        });
    }

    /**
     * Say no, and leave the student where they are.
     */
    public function reject(CampusMoveRequest $request, ?User $actor = null, ?string $note = null): CampusMoveRequest
    {
        return $this->writeDecision($request, CampusMoveStatus::Rejected, $actor, $note);
    }

    /**
     * Take the request back, which only the campus that asked does.
     */
    public function cancel(CampusMoveRequest $request, ?User $actor = null, ?string $note = null): CampusMoveRequest
    {
        return $this->writeDecision($request, CampusMoveStatus::Cancelled, $actor, $note);
    }

    /**
     * Get the request still waiting on this enrollment, if there is one.
     */
    public function openRequestFor(StudentRecord $enrollment): ?CampusMoveRequest
    {
        return CampusMoveRequest::query()
            ->where('student_record_id', $enrollment->id)
            ->open()
            ->latest('id')
            ->first();
    }

    /**
     * Write the decision on the request.
     *
     * @throws InvalidValueException when the state cannot follow the current one
     */
    private function writeDecision(
        CampusMoveRequest $request,
        CampusMoveStatus $status,
        ?User $actor,
        ?string $note,
    ): CampusMoveRequest {
        $current = $request->status;

        $this->failIfTheRequestCannotMoveTo($request, $status);

        $request->status = $status;
        $request->decided_by = $actor === null ? auth()->id() : $actor->id;
        $request->decided_at = now();
        $request->decision_note = $note ?? $request->decision_note;
        $request->save();

        $this->auditor->record(
            AuditAction::CampusMoveStatusChanged,
            $request,
            ['from' => $current->value, 'to' => $status->value, 'note' => $note],
            $actor,
            $request->to_school_id,
        );

        return $request;
    }

    /**
     * @throws InvalidValueException
     */
    private function failIfTheRequestCannotMoveTo(CampusMoveRequest $request, CampusMoveStatus $status): void
    {
        if (!$request->status->canMoveTo($status)) {
            throw new InvalidValueException("A campus move request cannot move from {$request->status->value} to {$status->value}.");
        }
    }

    /**
     * Refuse a request that the move action would refuse anyway.
     *
     * @throws InvalidValueException
     */
    private function failIfTheMoveCouldNeverBeMade(
        StudentRecord $enrollment,
        AcademicCycleSection $academicCycleSection,
    ): void {
        if ($enrollment->status->isClosed()) {
            throw new InvalidValueException('This enrollment is closed. It cannot move to another campus.');
        }

        if ($enrollment->school_id === $academicCycleSection->school_id) {
            throw new InvalidValueException('The student already attends that campus. Change the placement instead.');
        }

        if ($enrollment->school->organization_id !== $academicCycleSection->school->organization_id) {
            throw new InvalidValueException('The two campuses belong to different organizations. Transfer the enrollment instead.');
        }
    }
}
