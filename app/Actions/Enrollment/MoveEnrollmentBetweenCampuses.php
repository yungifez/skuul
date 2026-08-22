<?php

namespace App\Actions\Enrollment;

use App\Actions\Audit\RecordAuditEvent;
use App\Actions\School\GrantSchoolMembership;
use App\Enums\AuditAction;
use App\Exceptions\InvalidValueException;
use App\Models\AcademicCycleSection;
use App\Models\School;
use App\Models\StudentRecord;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;

/**
 * Move one enrollment to another campus of the same organization.
 *
 * A campus is a school inside an organization. Moving between two campuses of
 * one organization is an internal move, not a transfer: the person keeps the
 * same enrollment, the same admission number, and the whole placement history.
 * Only a move to another organization closes an enrollment and opens a new
 * one, which is what `TransferEnrollment` does.
 */
class MoveEnrollmentBetweenCampuses
{
    public function __construct(
        private ChangeEnrollmentPlacement $changePlacement,
        private GrantSchoolMembership $grantSchoolMembership,
        private RecordAuditEvent $auditor,
    ) {
    }

    /**
     * Move the enrollment to the campus that owns the given cycle section.
     *
     * @throws InvalidValueException when the two campuses do not share an organization
     */
    public function move(
        StudentRecord $enrollment,
        AcademicCycleSection $academicCycleSection,
        ?User $actor = null,
        ?string $reason = null,
        ?CarbonInterface $effectiveOn = null,
    ): StudentRecord {
        $enrollmentId = $enrollment->getKey();
        $academicCycleSectionId = $academicCycleSection->getKey();

        return DB::transaction(function () use ($enrollmentId, $academicCycleSectionId, $actor, $reason, $effectiveOn): StudentRecord {
            $enrollment = StudentRecord::query()
                ->with(['school', 'user'])
                ->lockForUpdate()
                ->findOrFail($enrollmentId);
            $academicCycleSection = AcademicCycleSection::query()
                ->with('school')
                ->lockForUpdate()
                ->findOrFail($academicCycleSectionId);

            $source = $enrollment->school;
            $destination = $academicCycleSection->school;

            $this->failIfTheMoveDoesNotFit($enrollment, $source, $destination);

            // The person needs access to the campus they now attend. The
            // membership at the old campus stays, so its staff keep reading
            // the records the student made while they were there.
            $this->grantSchoolMembership->grant($enrollment->user, $destination);

            $enrollment->school_id = $destination->id;
            $enrollment->save();

            // The placement action owns the history, and it now sees one
            // enrollment and one cycle section inside the same campus.
            $enrollment = $this->changePlacement->place(
                enrollment: $enrollment,
                academicCycleSection: $academicCycleSection,
                actor: $actor,
                reason: $reason,
                effectiveOn: $effectiveOn,
            );

            $this->auditor->record(
                AuditAction::EnrollmentCampusChanged,
                $enrollment,
                [
                    'from_school_id'            => $source->id,
                    'to_school_id'              => $destination->id,
                    'organization_id'           => $destination->organization_id,
                    'academic_cycle_section_id' => $academicCycleSection->id,
                    'reason'                    => $reason,
                ],
                $actor,
                $destination,
            );

            return $enrollment;
        });
    }

    /**
     * Check that this is an internal move between two campuses.
     *
     * @throws InvalidValueException
     */
    private function failIfTheMoveDoesNotFit(
        StudentRecord $enrollment,
        School $source,
        School $destination,
    ): void {
        if ($enrollment->status->isClosed()) {
            throw new InvalidValueException('This enrollment is closed. It cannot move to another campus.');
        }

        if ($source->id === $destination->id) {
            throw new InvalidValueException('The student already attends that campus. Change the placement instead.');
        }

        if ($source->organization_id !== $destination->organization_id) {
            throw new InvalidValueException('The two campuses belong to different organizations. Transfer the enrollment instead.');
        }
    }
}
