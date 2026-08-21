<?php

namespace App\Actions\Gradebook;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AuditAction;
use App\Models\ResultSnapshot;
use App\Models\StudentRecord;
use App\Models\Subject;
use App\Models\User;
use App\Services\Gradebook\GradebookCalculator;
use Illuminate\Support\Facades\DB;

/**
 * Turn what the gradebook says today into a published result.
 *
 * Teachers keep working in the gradebook. Publication takes a copy and gives
 * it a revision number, so a correction is a new revision instead of a quiet
 * change to a result a family already read.
 */
class PublishResult
{
    public function __construct(
        private GradebookCalculator $calculator,
        private RecordAuditEvent $auditor,
    ) {
    }

    /**
     * Publish the result of one enrollment in one subject.
     */
    public function publish(
        Subject $subject,
        StudentRecord $enrollment,
        ?int $academicYearId = null,
        ?int $semesterId = null,
        ?User $actor = null,
        ?string $reason = null,
    ): ResultSnapshot {
        $academicYearId ??= current_academic_year_id();
        $semesterId ??= current_semester_id();

        $result = $this->calculator->calculate($subject, $enrollment, $academicYearId, $semesterId);

        return DB::transaction(function () use ($subject, $enrollment, $academicYearId, $semesterId, $actor, $reason, $result): ResultSnapshot {
            $previous = ResultSnapshot::query()
                ->where('student_record_id', $enrollment->id)
                ->where('subject_id', $subject->id)
                ->where('academic_year_id', $academicYearId)
                ->where('semester_id', $semesterId)
                ->latestRevision()
                ->first();

            $snapshot = ResultSnapshot::create([
                'school_id'         => $subject->school_id,
                'student_record_id' => $enrollment->id,
                'subject_id'        => $subject->id,
                'academic_year_id'  => $academicYearId,
                'semester_id'       => $semesterId,
                'revision'          => $previous === null ? 1 : $previous->revision + 1,
                'percentage'        => $result['percentage'],
                'payload'           => $result,
                'reason'            => $reason,
                'published_at'      => now(),
                'published_by'      => $actor === null ? auth()->id() : $actor->id,
            ]);

            $this->auditor->record(
                $previous === null ? AuditAction::ResultPublished : AuditAction::ResultRevised,
                $snapshot,
                [
                    'subject_id'        => $subject->id,
                    'student_record_id' => $enrollment->id,
                    'revision'          => $snapshot->revision,
                    'percentage'        => $snapshot->percentage,
                    'reason'            => $reason,
                ],
                $actor,
            );

            return $snapshot;
        });
    }

    /**
     * Get the result families and teachers should read now.
     */
    public function current(Subject $subject, StudentRecord $enrollment, ?int $academicYearId = null, ?int $semesterId = null): ?ResultSnapshot
    {
        return ResultSnapshot::query()
            ->where('student_record_id', $enrollment->id)
            ->where('subject_id', $subject->id)
            ->where('academic_year_id', $academicYearId ?? current_academic_year_id())
            ->where('semester_id', $semesterId ?? current_semester_id())
            ->latestRevision()
            ->first();
    }
}
