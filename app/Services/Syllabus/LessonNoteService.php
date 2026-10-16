<?php

namespace App\Services\Syllabus;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AuditAction;
use App\Enums\LessonNoteStatus;
use App\Enums\SyllabusStatus;
use App\Exceptions\InvalidValueException;
use App\Models\LessonNote;
use App\Models\Syllabus;
use App\Models\User;
use App\Notifications\SyllabusWorkNotification;
use Illuminate\Support\Facades\DB;

/**
 * Write weekly lesson notes against the published syllabus, and review them.
 */
class LessonNoteService
{
    public function __construct(
        private SyllabusCoverageService $coverage,
        private RecordAuditEvent $auditor,
    ) {}

    /**
     * Create a note, or change one the author can still edit.
     *
     * @param  array{week: int, syllabus_topic_id: int|null, objectives: string, activities: string, evaluation: string|null}  $data
     */
    public function save(Syllabus $syllabus, ?int $academicCycleSectionId, array $data, User $author, ?LessonNote $note = null): LessonNote
    {
        if ($syllabus->fresh()?->status !== SyllabusStatus::Published) {
            throw new InvalidValueException('Lesson notes are written against the published syllabus only.');
        }

        if (!in_array($academicCycleSectionId, array_column($this->coverage->tracks($syllabus), 'id'), true)) {
            throw new InvalidValueException('That class does not take this course offering.');
        }

        if ($data['syllabus_topic_id'] !== null && !$syllabus->topics()->whereKey($data['syllabus_topic_id'])->exists()) {
            throw new InvalidValueException('That topic is not in the published syllabus.');
        }

        return DB::transaction(function () use ($syllabus, $academicCycleSectionId, $data, $author, $note): LessonNote {
            if ($note !== null) {
                $note = LessonNote::query()->lockForUpdate()->findOrFail($note->id);

                if (!$note->status->isEditable() || $note->course_offering_id !== $syllabus->course_offering_id) {
                    throw new InvalidValueException('This lesson note can no longer be changed.');
                }
            }

            $duplicate = LessonNote::query()
                ->where('course_offering_id', $syllabus->course_offering_id)
                ->where('academic_cycle_section_id', $academicCycleSectionId)
                ->where('user_id', $author->id)
                ->where('week', $data['week'])
                ->when($note !== null, fn ($notes) => $notes->whereKeyNot($note->id))
                ->lockForUpdate()
                ->exists();

            if ($duplicate) {
                throw new InvalidValueException("You already have a lesson note for week {$data['week']} of this class. Edit that note instead.");
            }

            $note ??= new LessonNote([
                'course_offering_id' => $syllabus->course_offering_id,
                'academic_cycle_section_id' => $academicCycleSectionId,
                'user_id' => $author->id,
            ]);
            $note->fill($data)->save();

            return $note;
        });
    }

    /**
     * Send a note to the head of department.
     */
    public function submit(LessonNote $note, ?User $actor = null): LessonNote
    {
        return DB::transaction(function () use ($note, $actor): LessonNote {
            $note = LessonNote::query()->lockForUpdate()->findOrFail($note->id);

            if (!$note->status->isEditable()) {
                throw new InvalidValueException('This lesson note was already sent for review.');
            }

            $note->update(['status' => LessonNoteStatus::Submitted, 'submitted_at' => now()]);
            $this->auditor->record(AuditAction::LessonNoteSubmitted, $note, ['week' => $note->week], $actor, $note->courseOffering->school_id);

            return $note;
        });
    }

    /**
     * Approve a note that waits for review.
     */
    public function approve(LessonNote $note, ?User $actor = null): LessonNote
    {
        return $this->review($note, LessonNoteStatus::Approved, null, AuditAction::LessonNoteApproved, $actor);
    }

    /**
     * Send a note back to its author with the changes it needs.
     */
    public function sendBack(LessonNote $note, string $reviewNote, ?User $actor = null): LessonNote
    {
        return $this->review($note, LessonNoteStatus::Returned, $reviewNote, AuditAction::LessonNoteReturned, $actor);
    }

    /**
     * Delete a note that was never approved.
     */
    public function delete(LessonNote $note): void
    {
        DB::transaction(function () use ($note): void {
            $note = LessonNote::query()->lockForUpdate()->findOrFail($note->id);

            if (!$note->status->isEditable()) {
                throw new InvalidValueException('Only a draft or a returned lesson note can be deleted.');
            }

            $note->delete();
        });
    }

    private function review(LessonNote $note, LessonNoteStatus $outcome, ?string $reviewNote, AuditAction $action, ?User $actor): LessonNote
    {
        $reviewed = DB::transaction(function () use ($note, $outcome, $reviewNote, $action, $actor): LessonNote {
            $note = LessonNote::query()->lockForUpdate()->findOrFail($note->id);

            if ($note->status !== LessonNoteStatus::Submitted) {
                throw new InvalidValueException('This lesson note is not waiting for review.');
            }

            $note->update([
                'status' => $outcome,
                'reviewed_by' => ($actor ?? auth()->user())?->id,
                'reviewed_at' => now(),
                'review_note' => $reviewNote,
            ]);
            $this->auditor->record($action, $note, ['week' => $note->week], $actor, $note->courseOffering->school_id);

            return $note;
        });

        $this->tellAuthor($reviewed, $actor);

        return $reviewed;
    }

    /**
     * Tell the author how the review of their note ended.
     */
    private function tellAuthor(LessonNote $note, ?User $reviewer): void
    {
        $author = $note->author;
        $syllabus = Syllabus::query()
            ->where('course_offering_id', $note->course_offering_id)
            ->where('status', SyllabusStatus::Published)
            ->first();

        if ($author === null || $syllabus === null || $author->id === ($reviewer ?? auth()->user())?->id) {
            return;
        }

        $subject = $note->courseOffering->subject->name;
        $notification = $note->status === LessonNoteStatus::Approved
            ? new SyllabusWorkNotification(
                "Week {$note->week} lesson note approved",
                ["Your {$subject} lesson note for week {$note->week} was approved."],
                'Open lesson notes',
                route('syllabi.lesson-notes', $syllabus),
            )
            : new SyllabusWorkNotification(
                "Week {$note->week} lesson note needs changes",
                ["Your {$subject} lesson note for week {$note->week} was sent back:", (string) $note->review_note, 'Change the note, then send it again.'],
                'Open lesson notes',
                route('syllabi.lesson-notes', $syllabus),
            );

        $author->notify($notification);
    }
}
