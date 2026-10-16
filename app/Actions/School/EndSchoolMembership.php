<?php

namespace App\Actions\School;

use App\Actions\Boarding\AssignBoardingSupervisor;
use App\Actions\Curriculum\AssignTeacher;
use App\Actions\Library\CloseReservation;
use App\Enums\AcademicStructureStatus;
use App\Enums\SchoolMembershipStatus;
use App\Exceptions\InvalidValueException;
use App\Models\AcademicCycleSection;
use App\Models\AcademicYear;
use App\Models\BoardingSupervision;
use App\Models\Incident;
use App\Models\IncidentAction;
use App\Models\School;
use App\Models\SchoolMembership;
use App\Models\SupportPlan;
use App\Models\SupportPlanAction;
use App\Models\TeachingAssignment;
use App\Models\TimetableSubstitution;
use App\Models\User;
use App\Services\Authorization\RoleAuthority;
use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * End campus access and active assignments without deleting the person or history.
 * Release library reservations and return open cases and support plans to the campus.
 */
class EndSchoolMembership
{
    public function __construct(
        private AssignTeacher $teaching,
        private AssignBoardingSupervisor $boardingDuty,
        private RoleAuthority $roleAuthority,
        private CloseReservation $reservations,
    ) {}

    /**
     * End the membership and return it, or null when there was none.
     *
     * @throws InvalidValueException when nobody left at the campus could manage roles
     */
    public function end(User $user, School $school): ?SchoolMembership
    {
        return $this->roleAuthority->mustKeepARoleManager($school, fn (): ?SchoolMembership => $this->endMembership($user, $school));
    }

    private function endMembership(User $user, School $school): ?SchoolMembership
    {
        return DB::transaction(function () use ($user, $school): ?SchoolMembership {
            $membership = $user->schoolMemberships()
                ->where('school_id', $school->id)
                ->first();

            if ($membership === null || $membership->status === SchoolMembershipStatus::Ended) {
                return $membership;
            }

            $membership->status = SchoolMembershipStatus::Ended;
            $membership->ended_at = now();
            $membership->is_primary = false;
            $membership->save();

            $user->schoolMemberships()->where('school_id', $school->id)->update(['is_primary' => false]);

            $this->endDutiesFrom($user, $school->id, school_today($school));

            // A copy held for somebody who no longer comes in goes to the next person.
            $this->reservations->cancelEveryReservation($user, $school->id);

            $this->promoteAnotherPrimary($user);

            return $membership;
        });
    }

    /**
     * End teaching, boarding duty, and future cover from the leaving date.
     * Preserve completed cover.
     */
    public function endDutiesFrom(User $user, int $schoolId, CarbonInterface $firstDayAway): void
    {
        $firstDayAway = Carbon::instance($firstDayAway)->startOfDay();

        DB::transaction(function () use ($user, $schoolId, $firstDayAway): void {
            TeachingAssignment::query()
                ->where('school_id', $schoolId)
                ->forTeacher($user)
                ->where(fn ($running) => $running->whereNull('ends_on')->orWhereDate('ends_on', '>', $firstDayAway))
                ->get()
                ->each(fn (TeachingAssignment $assignment) => $this->teaching->end($assignment, $firstDayAway));

            BoardingSupervision::query()
                ->where('school_id', $schoolId)
                ->where('user_id', $user->id)
                ->whereNull('ends_on')
                ->whereDate('starts_on', '<=', $firstDayAway)
                ->get()
                ->each(fn (BoardingSupervision $duty) => $this->boardingDuty->end($duty, $firstDayAway->copy()));

            TimetableSubstitution::query()
                ->where('replacement_teacher_id', $user->id)
                ->whereDate('substituted_on', '>=', $firstDayAway)
                ->whereHas('timetable.academicCycleSection', fn ($sections) => $sections->where('school_id', $schoolId))
                ->delete();

            // A leaver still at work keeps their cases until they go.
            if ($firstDayAway->lessThanOrEqualTo(school_today($schoolId))) {
                $this->handBackOpenCases($user, $schoolId);
                $this->handBackTheirClasses($user, $schoolId);
            }
        });
    }

    /**
     * Unassign open cases and plans so the campus can reassign them.
     * Preserve assignments on completed work.
     */
    private function handBackOpenCases(User $user, int $schoolId): void
    {
        Incident::query()
            ->where('school_id', $schoolId)
            ->where('assigned_to', $user->id)
            ->open()
            ->update(['assigned_to' => null]);

        IncidentAction::query()
            ->where('assigned_to', $user->id)
            ->whereNull('completed_at')
            ->whereIn('incident_id', Incident::query()->where('school_id', $schoolId)->open()->select('id'))
            ->update(['assigned_to' => null]);

        SupportPlan::query()
            ->where('school_id', $schoolId)
            ->where('assigned_to', $user->id)
            ->open()
            ->update(['assigned_to' => null]);

        SupportPlanAction::query()
            ->where('assigned_to', $user->id)
            ->whereNull('completed_at')
            ->whereIn('support_plan_id', SupportPlan::query()->where('school_id', $schoolId)->open()->select('id'))
            ->update(['assigned_to' => null]);
    }

    /**
     * Take the person off the classes they lead in years still running.
     *
     * The class then shows it needs a class teacher, and can be edited again.
     * A class in a finished year or an archived one keeps its name as history.
     */
    private function handBackTheirClasses(User $user, int $schoolId): void
    {
        AcademicCycleSection::query()
            ->where('school_id', $schoolId)
            ->where('homeroom_teacher_id', $user->id)
            ->where('status', '!=', AcademicStructureStatus::Archived)
            ->whereIn('academic_year_id', AcademicYear::query()->where('school_id', $schoolId)->operational()->select('id'))
            ->update(['homeroom_teacher_id' => null]);
    }

    /**
     * Make sure the person still has a school for organization-level work.
     */
    private function promoteAnotherPrimary(User $user): void
    {
        $hasPrimary = $user->schoolMemberships()->active()->primary()->exists();

        if ($hasPrimary) {
            return;
        }

        $next = $user->schoolMemberships()->active()->orderBy('id')->first();

        $next?->update(['is_primary' => true]);
    }

    /**
     * End the membership, or fail when the person is not a member.
     */
    public function endOrFail(User $user, School $school): SchoolMembership
    {
        $membership = $this->end($user, $school);

        if ($membership === null) {
            throw new RuntimeException("{$user->name} is not a member of {$school->name}.");
        }

        return $membership;
    }
}
