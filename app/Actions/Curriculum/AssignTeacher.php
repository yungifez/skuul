<?php

namespace App\Actions\Curriculum;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AuditAction;
use App\Enums\Role;
use App\Enums\TeachingRole;
use App\Exceptions\InvalidValueException;
use App\Models\AcademicCycleSection;
use App\Models\CourseOffering;
use App\Models\TeachingAssignment;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Give a teacher a course offering to teach, and record when it started.
 *
 * Several teachers can share an offering, each in their own part, so an
 * assignment is a record instead of a row in a pivot table. An assignment
 * ends by taking an end date, which keeps last year's timetable readable.
 */
class AssignTeacher
{
    public function __construct(private RecordAuditEvent $auditor)
    {
    }

    /**
     * Give the teacher the subject.
     *
     * Asking twice returns the assignment that already runs.
     *
     * @throws InvalidValueException when the teacher, offering, or home group does not fit
     */
    public function assign(
        CourseOffering $courseOffering,
        User $teacher,
        TeachingRole $role = TeachingRole::Lead,
        ?AcademicCycleSection $academicCycleSection = null,
        ?User $actor = null,
        ?CarbonInterface $startsOn = null,
    ): TeachingAssignment {
        $courseOffering->loadMissing(['academicYear', 'academicPeriod', 'subject']);
        $this->failIfRecordsDoNotFit($courseOffering, $teacher, $academicCycleSection);

        $running = TeachingAssignment::query()
            ->where('course_offering_id', $courseOffering->id)
            ->forTeacher($teacher)
            ->where('academic_cycle_section_id', $academicCycleSection?->id)
            ->runningOn($startsOn)
            ->first();

        if ($running !== null) {
            return $running;
        }

        return DB::transaction(function () use ($courseOffering, $teacher, $role, $academicCycleSection, $actor, $startsOn): TeachingAssignment {
            $assignment = TeachingAssignment::create([
                'school_id'                 => $courseOffering->school_id,
                'subject_id'                => $courseOffering->subject_id,
                'user_id'                   => $teacher->id,
                'academic_year_id'          => $courseOffering->academic_year_id,
                'academic_period_id'        => $courseOffering->academic_period_id,
                'course_offering_id'        => $courseOffering->id,
                'academic_cycle_section_id' => $academicCycleSection?->id,
                'role'                      => $role,
                'starts_on'                 => $startsOn ?? now(),
            ]);

            $this->auditor->record(
                AuditAction::TeachingAssignmentCreated,
                $assignment,
                [
                    'subject_id'                => $courseOffering->subject_id,
                    'teacher_id'                => $teacher->id,
                    'role'                      => $role->value,
                    'academic_cycle_section_id' => $academicCycleSection?->id,
                    'academic_year_id'          => $courseOffering->academic_year_id,
                    'course_offering_id'        => $courseOffering->id,
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

            $this->auditor->record(
                AuditAction::TeachingAssignmentEnded,
                $assignment,
                [
                    'subject_id' => $assignment->subject_id,
                    'teacher_id' => $assignment->user_id,
                    'ends_on'    => $assignment->ends_on->toDateString(),
                ],
                $actor,
            );

            return $assignment;
        });
    }

    /**
     * Check that the teacher, offering, and home group belong together.
     *
     * @throws InvalidValueException
     */
    private function failIfRecordsDoNotFit(CourseOffering $courseOffering, User $teacher, ?AcademicCycleSection $academicCycleSection): void
    {
        if (!$teacher->belongsToSchool($courseOffering->school_id)) {
            throw new InvalidValueException('The teacher does not work in this school.');
        }

        if (!$teacher->hasRole(Role::Teacher->value)) {
            throw new InvalidValueException('Only a teacher can be assigned to a subject.');
        }

        if ($academicCycleSection !== null
            && ($academicCycleSection->school_id !== $courseOffering->school_id
                || $academicCycleSection->academic_year_id !== $courseOffering->academic_year_id
                || $academicCycleSection->academic_level_id !== $courseOffering->academic_level_id)) {
            throw new InvalidValueException('The home group does not belong to this offering.');
        }

        if ($courseOffering->academicYear->isClosed()) {
            throw new InvalidValueException('The academic year is closed. Reopen it before you change teaching.');
        }
    }
}
