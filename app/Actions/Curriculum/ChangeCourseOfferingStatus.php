<?php

namespace App\Actions\Curriculum;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AuditAction;
use App\Enums\CourseOfferingStatus;
use App\Enums\RosterMode;
use App\Exceptions\InvalidValueException;
use App\Models\CourseOffering;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Move a course offering through its operational lifecycle.
 */
class ChangeCourseOfferingStatus
{
    public function __construct(private RecordAuditEvent $auditor) {}

    /**
     * @throws InvalidValueException when the transition is not valid
     */
    public function change(CourseOffering $courseOffering, CourseOfferingStatus $status, ?User $actor = null): CourseOffering
    {
        return DB::transaction(function () use ($courseOffering, $status, $actor): CourseOffering {
            $courseOffering = CourseOffering::query()
                ->with(['academicPeriod', 'academicYear', 'cycleSections', 'studentRecords'])
                ->lockForUpdate()
                ->findOrFail($courseOffering->id);

            if (!$courseOffering->status->canMoveTo($status)) {
                throw new InvalidValueException("A {$courseOffering->status->label()} offering cannot move to {$status->label()}.");
            }

            $activeKey = null;

            if ($status === CourseOfferingStatus::Active) {
                if (!$courseOffering->academicPeriod->isOperational() || $courseOffering->academicYear->isClosed()) {
                    throw new InvalidValueException('Open the academic period before activating its course offering.');
                }

                $this->ensureRosterIsReady($courseOffering);

                $activeKey = $courseOffering->activeKeyForRoster(
                    $courseOffering->cycleSections->modelKeys(),
                    $courseOffering->studentRecords->modelKeys(),
                );

                if (CourseOffering::query()->where('active_key', $activeKey)->whereKeyNot($courseOffering->id)->exists()) {
                    throw new InvalidValueException('An active offering already exists for this subject, period, level, and section group.');
                }
            }

            $previousStatus = $courseOffering->status;
            $courseOffering->status = $status;
            $courseOffering->active_key = $activeKey;
            $courseOffering->save();

            $this->auditor->record(
                AuditAction::CourseOfferingStatusChanged,
                $courseOffering,
                [
                    'from' => $previousStatus->value,
                    'to' => $status->value,
                    'academic_period_id' => $courseOffering->academic_period_id,
                ],
                $actor,
            );

            return $courseOffering;
        });
    }

    private function ensureRosterIsReady(CourseOffering $courseOffering): void
    {
        $sectionCount = $courseOffering->cycleSections->count();
        $learnerCount = $courseOffering->studentRecords->count();

        match ($courseOffering->roster_mode) {
            RosterMode::HomeSection => $sectionCount === 1 ?: throw new InvalidValueException('Select exactly one section before activating this course offering.'),
            RosterMode::CombinedHomeSections => $sectionCount >= 2 ?: throw new InvalidValueException('Select at least two sections before activating this course offering.'),
            RosterMode::AcademicLevel => ($sectionCount === 0 && $learnerCount === 0) ?: throw new InvalidValueException('A whole-level offering cannot have sections or named learners.'),
            RosterMode::IndividualRoster => $learnerCount > 0 ?: throw new InvalidValueException('Add at least one named learner before activating this course offering.'),
        };
    }
}
