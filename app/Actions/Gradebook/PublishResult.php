<?php

namespace App\Actions\Gradebook;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AuditAction;
use App\Enums\ResultApprovalStatus;
use App\Models\CourseOffering;
use App\Models\ResultSnapshot;
use App\Models\StudentRecord;
use App\Models\User;
use App\Services\Gradebook\CourseOfferingRoster;
use App\Services\Gradebook\GradebookCalculator;
use Illuminate\Support\Facades\DB;

/**
 * Turn what the gradebook says today into a result awaiting approval.
 *
 * Teachers keep working in the gradebook. Publication takes a copy and gives
 * it a revision number, so a correction is a new revision instead of a quiet
 * change to a result a family already read.
 */
class PublishResult
{
    public function __construct(
        private GradebookCalculator $calculator,
        private CourseOfferingRoster $roster,
        private RecordAuditEvent $auditor,
    ) {
    }

    /**
     * Publish the result of one enrollment in one course offering.
     */
    public function publish(
        CourseOffering $courseOffering,
        StudentRecord $enrollment,
        ?User $actor = null,
        ?string $reason = null,
    ): ResultSnapshot {
        $this->roster->ensureIncludes($courseOffering, $enrollment);
        $result = $this->calculator->calculate($courseOffering, $enrollment);

        return DB::transaction(function () use ($courseOffering, $enrollment, $actor, $reason, $result): ResultSnapshot {
            $previous = ResultSnapshot::query()
                ->where('student_record_id', $enrollment->id)
                ->whereBelongsTo($courseOffering)
                ->latestRevision()
                ->first();

            $snapshot = ResultSnapshot::create([
                'school_id'          => $courseOffering->school_id,
                'student_record_id'  => $enrollment->id,
                'course_offering_id' => $courseOffering->id,
                'revision'           => $previous === null ? 1 : $previous->revision + 1,
                'percentage'         => $result['percentage'],
                'payload'            => $result,
                'reason'             => $reason,
                'approval_status' => ResultApprovalStatus::Pending,
                'published_at'       => now(),
                'published_by'       => $actor === null ? auth()->id() : $actor->id,
            ]);

            $this->auditor->record(
                AuditAction::ResultSubmittedForApproval,
                $snapshot,
                [
                    'course_offering_id' => $courseOffering->id,
                    'student_record_id'  => $enrollment->id,
                    'revision'           => $snapshot->revision,
                    'percentage'         => $snapshot->percentage,
                    'reason'             => $reason,
                ],
                $actor,
            );

            return $snapshot;
        });
    }

    /**
     * Get the result families and teachers should read now.
     */
    public function current(CourseOffering $courseOffering, StudentRecord $enrollment): ?ResultSnapshot
    {
        return ResultSnapshot::query()
            ->where('student_record_id', $enrollment->id)
            ->whereBelongsTo($courseOffering)
            ->approved()
            ->latestRevision()
            ->first();
    }
}
