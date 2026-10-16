<?php

namespace App\Policies;

use App\Enums\LessonNoteStatus;
use App\Enums\SyllabusStatus;
use App\Models\LessonNote;
use App\Models\Syllabus;
use App\Models\TeachingAssignment;
use App\Models\User;

class LessonNotePolicy
{
    /**
     * Determine whether the user can read the lesson notes of a syllabus's offering.
     *
     * Reviewers read every note in the school. A teacher reads the notes of
     * the offerings they are assigned to teach.
     */
    public function viewAny(User $user, Syllabus $syllabus): bool
    {
        if ($syllabus->status !== SyllabusStatus::Published
            || !$user->can('read syllabus')
            || $user->isPortalOnly()
            || current_school_id() !== $syllabus->courseOffering->school_id
        ) {
            return false;
        }

        return $user->can('approve syllabus')
            || TeachingAssignment::query()
                ->where('course_offering_id', $syllabus->course_offering_id)
                ->where('user_id', $user->id)
                ->exists();
    }

    /**
     * Determine whether the user can write a note for one class.
     *
     * The rule is the one for recording coverage: a teacher writes for the
     * sections they are assigned to.
     */
    public function create(User $user, Syllabus $syllabus, ?int $academicCycleSectionId = null): bool
    {
        return $user->can('recordCoverage', [$syllabus, $academicCycleSectionId]);
    }

    /**
     * Determine whether the user can change their own note before it is approved.
     */
    public function update(User $user, LessonNote $lessonNote): bool
    {
        return $lessonNote->user_id === $user->id
            && $lessonNote->status->isEditable()
            && $user->can('update syllabus')
            && !$user->isPortalOnly()
            && current_school_id() === $lessonNote->courseOffering->school_id;
    }

    /**
     * Determine whether the user can send their note for review.
     */
    public function submit(User $user, LessonNote $lessonNote): bool
    {
        return $this->update($user, $lessonNote);
    }

    /**
     * Determine whether the user can delete their note before it is approved.
     */
    public function delete(User $user, LessonNote $lessonNote): bool
    {
        return $this->update($user, $lessonNote);
    }

    /**
     * Determine whether the user can approve a note or send it back.
     *
     * Nobody reviews their own note.
     */
    public function review(User $user, LessonNote $lessonNote): bool
    {
        return $lessonNote->status === LessonNoteStatus::Submitted
            && $lessonNote->user_id !== $user->id
            && $user->can('approve syllabus')
            && !$user->isPortalOnly()
            && current_school_id() === $lessonNote->courseOffering->school_id;
    }
}
