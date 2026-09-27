<?php

namespace App\Policies;

use App\Enums\CourseOfferingStatus;
use App\Enums\Role;
use App\Enums\SyllabusStatus;
use App\Models\Syllabus;
use App\Models\TeachingAssignment;
use App\Models\User;
use App\Services\Gradebook\CourseOfferingRoster;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Database\Eloquent\Builder;

class SyllabusPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('read syllabus') && !$user->isParentPortalOnly();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Syllabus $syllabus): bool
    {
        if (!$user->can('read syllabus')
            || $user->isParentPortalOnly()
            || current_school_id() !== $syllabus->courseOffering->school_id
        ) {
            return false;
        }

        if (!$user->hasRole(Role::Student)) {
            return true;
        }

        $enrollment = $user->studentRecord()->attending()->first();

        return $syllabus->status === SyllabusStatus::Published
            && $syllabus->courseOffering->status === CourseOfferingStatus::Active
            && $enrollment !== null
            && app(CourseOfferingRoster::class)->includes($syllabus->courseOffering, $enrollment);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create syllabus') && !$user->isPortalOnly();
    }

    /**
     * Determine whether the user can change a syllabus they own.
     *
     * Reviewers own every syllabus in the school. Other staff own the
     * syllabi of the offerings they are assigned to teach.
     */
    public function update(User $user, Syllabus $syllabus): bool
    {
        return $user->can('update syllabus')
            && !$user->isPortalOnly()
            && current_school_id() === $syllabus->courseOffering->school_id
            && ($user->can('approve syllabus') || $this->teaches($user, $syllabus->course_offering_id));
    }

    /**
     * Determine whether the user can delete a draft they own.
     */
    public function delete(User $user, Syllabus $syllabus): bool
    {
        return $user->can('delete syllabus')
            && $syllabus->status === SyllabusStatus::Draft
            && $this->update($user, $syllabus);
    }

    /**
     * Determine whether the user can send a draft for review.
     */
    public function submit(User $user, Syllabus $syllabus): bool
    {
        return $syllabus->status === SyllabusStatus::Draft && $this->update($user, $syllabus);
    }

    /**
     * Determine whether the user can take a syllabus back from review.
     */
    public function withdraw(User $user, Syllabus $syllabus): bool
    {
        return $syllabus->status === SyllabusStatus::Submitted && $this->update($user, $syllabus);
    }

    /**
     * Determine whether the user can approve a syllabus and publish it to students.
     */
    public function publish(User $user, Syllabus $syllabus): bool
    {
        return in_array($syllabus->status, [SyllabusStatus::Draft, SyllabusStatus::Submitted], true)
            && $this->review($user, $syllabus);
    }

    /**
     * Determine whether the user can send a submitted syllabus back for changes.
     */
    public function sendBack(User $user, Syllabus $syllabus): bool
    {
        return $syllabus->status === SyllabusStatus::Submitted && $this->review($user, $syllabus);
    }

    /**
     * Determine whether the user can start a revision of a published syllabus.
     */
    public function revise(User $user, Syllabus $syllabus): bool
    {
        return $syllabus->status === SyllabusStatus::Published && $this->update($user, $syllabus);
    }

    /**
     * Determine whether the user can record what a class was taught.
     *
     * A teacher records coverage for the sections they are assigned to. An
     * assignment with no section covers the whole offering.
     */
    public function recordCoverage(User $user, Syllabus $syllabus, ?int $academicCycleSectionId = null): bool
    {
        if ($syllabus->status !== SyllabusStatus::Published
            || !$user->can('update syllabus')
            || $user->isPortalOnly()
            || current_school_id() !== $syllabus->courseOffering->school_id
        ) {
            return false;
        }

        if ($user->can('approve syllabus')) {
            return true;
        }

        return $syllabus->courseOffering->teachingAssignments()
            ->where('user_id', $user->id)
            ->where(function (Builder $assignments) use ($academicCycleSectionId): void {
                $assignments->whereNull('academic_cycle_section_id')->orWhere('academic_cycle_section_id', $academicCycleSectionId);
            })
            ->exists();
    }

    /**
     * Determine whether the user can see coverage across every syllabus.
     */
    public function viewCoverage(User $user): bool
    {
        return $user->can('approve syllabus') && !$user->isPortalOnly();
    }

    /**
     * Check if the user reviews syllabi in the syllabus's school.
     */
    private function review(User $user, Syllabus $syllabus): bool
    {
        return $user->can('approve syllabus')
            && !$user->isPortalOnly()
            && current_school_id() === $syllabus->courseOffering->school_id;
    }

    /**
     * Check if the user is assigned to teach the offering.
     */
    private function teaches(User $user, int $courseOfferingId): bool
    {
        return TeachingAssignment::query()
            ->where('course_offering_id', $courseOfferingId)
            ->where('user_id', $user->id)
            ->exists();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Syllabus $syllabus)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Syllabus $syllabus)
    {
        //
    }
}
