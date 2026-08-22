<?php

namespace App\Actions\Curriculum;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AuditAction;
use App\Enums\Role;
use App\Enums\TeachingRole;
use App\Exceptions\InvalidValueException;
use App\Models\AcademicCycleSection;
use App\Models\AcademicPeriod;
use App\Models\AcademicYear;
use App\Models\CourseOffering;
use App\Models\Subject;
use App\Models\TeachingAssignment;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Give a teacher a subject to teach, and record when it started.
 *
 * Several teachers can share a subject, each in their own part, so an
 * assignment is a record instead of a row in a pivot table. An assignment
 * ends by taking an end date, which keeps last year's timetable readable.
 */
class AssignTeacher
{
    public function __construct(private RecordAuditEvent $auditor) {}

    /**
     * Give the teacher the subject.
     *
     * Asking twice returns the assignment that already runs.
     *
     * @throws InvalidValueException when the teacher, subject, or home group does not fit
     */
    public function assign(
        Subject $subject,
        User $teacher,
        TeachingRole $role = TeachingRole::Lead,
        ?AcademicCycleSection $academicCycleSection = null,
        ?AcademicYear $academicYear = null,
        ?AcademicPeriod $academicPeriod = null,
        ?User $actor = null,
        ?CarbonInterface $startsOn = null,
        ?CourseOffering $courseOffering = null,
    ): TeachingAssignment {
        if ($courseOffering !== null) {
            $this->failIfCourseOfferingDoesNotFit($subject, $academicYear, $academicPeriod, $courseOffering);
            $academicYear = $courseOffering->academicYear;
            $academicPeriod = $courseOffering->academicPeriod;
        }

        $academicYear ??= current_academic_year();

        if ($academicYear === null) {
            throw new InvalidValueException('Set the academic year before you assign a teacher.');
        }

        $this->failIfRecordsDoNotFit($subject, $teacher, $academicCycleSection, $academicYear);

        $running = TeachingAssignment::query()
            ->where('subject_id', $subject->id)
            ->forTeacher($teacher)
            ->where('academic_year_id', $academicYear->id)
            ->where('academic_cycle_section_id', $academicCycleSection?->id)
            ->when($courseOffering !== null, fn ($query) => $query->where('course_offering_id', $courseOffering->id))
            ->runningOn($startsOn)
            ->first();

        if ($running !== null) {
            return $running;
        }

        return DB::transaction(function () use ($subject, $teacher, $role, $academicCycleSection, $academicYear, $academicPeriod, $actor, $startsOn, $courseOffering): TeachingAssignment {
            $assignment = TeachingAssignment::create([
                'school_id' => $subject->school_id,
                'subject_id' => $subject->id,
                'user_id' => $teacher->id,
                'academic_year_id' => $academicYear->id,
                'academic_period_id' => $academicPeriod === null ? current_academic_period_id() : $academicPeriod->id,
                'course_offering_id' => $courseOffering?->id,
                'academic_cycle_section_id' => $academicCycleSection?->id,
                'role' => $role,
                'starts_on' => $startsOn ?? now(),
            ]);

            // The old pivot still feeds the screens, so keep it in step.
            $subject->teachers()->syncWithoutDetaching([$teacher->id]);

            $this->auditor->record(
                AuditAction::TeachingAssignmentCreated,
                $assignment,
                [
                    'subject_id' => $subject->id,
                    'teacher_id' => $teacher->id,
                    'role' => $role->value,
                    'academic_cycle_section_id' => $academicCycleSection?->id,
                    'academic_year_id' => $academicYear->id,
                    'course_offering_id' => $courseOffering?->id,
                ],
                $actor,
            );

            return $assignment;
        });
    }

    /**
     * End the assignment on the given day.
     *
     * Ending it twice changes nothing.
     */
    public function end(TeachingAssignment $assignment, ?CarbonInterface $endsOn = null, ?User $actor = null): TeachingAssignment
    {
        if ($assignment->ends_on !== null) {
            return $assignment;
        }

        return DB::transaction(function () use ($assignment, $endsOn, $actor): TeachingAssignment {
            $assignment->ends_on = Carbon::parse($endsOn ?? now());
            $assignment->save();

            // This assignment still covers today, so leave it out when asking
            // whether the teacher keeps the subject.
            $stillTeaches = TeachingAssignment::query()
                ->whereKeyNot($assignment->getKey())
                ->where('subject_id', $assignment->subject_id)
                ->forTeacher($assignment->user_id)
                ->runningOn()
                ->exists();

            if (!$stillTeaches) {
                $assignment->subject?->teachers()->detach($assignment->user_id);
            }

            $this->auditor->record(
                AuditAction::TeachingAssignmentEnded,
                $assignment,
                [
                    'subject_id' => $assignment->subject_id,
                    'teacher_id' => $assignment->user_id,
                    'ends_on' => $assignment->ends_on->toDateString(),
                ],
                $actor,
            );

            return $assignment;
        });
    }

    /**
     * Check that the teacher, the subject, and the home group belong together.
     *
     * @throws InvalidValueException
     */
    private function failIfRecordsDoNotFit(Subject $subject, User $teacher, ?AcademicCycleSection $academicCycleSection, AcademicYear $academicYear): void
    {
        if ($subject->school_id !== $academicYear->school_id) {
            throw new InvalidValueException('The academic year belongs to another school.');
        }

        if (!$teacher->belongsToSchool($subject->school_id)) {
            throw new InvalidValueException('The teacher does not work in this school.');
        }

        if (!$teacher->hasRole(Role::Teacher->value)) {
            throw new InvalidValueException('Only a teacher can be assigned to a subject.');
        }

        if ($academicCycleSection !== null && ($academicCycleSection->school_id !== $subject->school_id || $academicCycleSection->academic_year_id !== $academicYear->id)) {
            throw new InvalidValueException('The home group does not belong to this school and academic cycle.');
        }

        if ($academicYear->isClosed()) {
            throw new InvalidValueException('The academic year is closed. Reopen it before you change teaching.');
        }
    }

    /**
     * @throws InvalidValueException
     */
    private function failIfCourseOfferingDoesNotFit(
        Subject $subject,
        ?AcademicYear $academicYear,
        ?AcademicPeriod $academicPeriod,
        CourseOffering $courseOffering,
    ): void {
        if ($courseOffering->subject_id !== $subject->id) {
            throw new InvalidValueException('The course offering teaches another subject.');
        }

        if ($academicYear !== null && $courseOffering->academic_year_id !== $academicYear->id) {
            throw new InvalidValueException('The course offering belongs to another academic year.');
        }

        if ($academicPeriod !== null && $courseOffering->academic_period_id !== $academicPeriod->id) {
            throw new InvalidValueException('The course offering belongs to another academic period.');
        }

    }
}
