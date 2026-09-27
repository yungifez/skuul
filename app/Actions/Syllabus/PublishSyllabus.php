<?php

namespace App\Actions\Syllabus;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AuditAction;
use App\Enums\SyllabusStatus;
use App\Exceptions\InvalidValueException;
use App\Models\LessonNote;
use App\Models\Syllabus;
use App\Models\SyllabusTopic;
use App\Models\SyllabusTopicCoverage;
use App\Models\User;
use App\Notifications\SyllabusWorkNotification;
use Illuminate\Support\Facades\DB;

class PublishSyllabus
{
    public function __construct(private RecordAuditEvent $auditor) {}

    public function publish(Syllabus $syllabus, ?User $actor = null): Syllabus
    {
        $published = DB::transaction(function () use ($syllabus, $actor): Syllabus {
            $syllabus = Syllabus::query()->lockForUpdate()->findOrFail($syllabus->id);

            if (!in_array($syllabus->status, [SyllabusStatus::Draft, SyllabusStatus::Submitted], true)) {
                throw new InvalidValueException('Only a draft syllabus can be published.');
            }

            if (!$syllabus->topics()->exists()) {
                throw new InvalidValueException('Add at least one topic before publishing the syllabus.');
            }

            if ($syllabus->revision_of_id !== null) {
                $replaced = Syllabus::query()->lockForUpdate()->findOrFail($syllabus->revision_of_id);

                if ($replaced->status !== SyllabusStatus::Published) {
                    throw new InvalidValueException('The syllabus this draft revises is no longer the published one. Delete this draft and revise the current syllabus.');
                }

                $replaced->update(['status' => SyllabusStatus::Superseded]);
                $this->carryCoverageForward($syllabus);
            }

            $syllabus->update(['status' => SyllabusStatus::Published, 'published_at' => now(), 'published_by' => $actor?->id, 'review_note' => null]);
            $this->auditor->record(AuditAction::SyllabusPublished, $syllabus, ['revision' => $syllabus->revision], $actor);

            return $syllabus;
        });

        $author = $published->submittedBy;

        if ($author !== null && $author->id !== $actor?->id) {
            $author->notify(new SyllabusWorkNotification(
                "{$published->name} was approved",
                ["{$published->name} (revision {$published->revision}) was approved and published. Students now see it."],
                'Open the syllabus',
                route('syllabi.show', $published),
            ));
        }

        return $published;
    }

    /**
     * Move what classes were taught, the lesson notes, and the gradebook items
     * that name a topic onto the matching topics of the new revision.
     *
     * A topic the revision dropped keeps these records on the
     * superseded revision, so the history of what was taught is never lost.
     */
    private function carryCoverageForward(Syllabus $revision): void
    {
        $revision->topics()->whereNotNull('copied_from_id')->get(['id', 'copied_from_id'])
            ->each(function (SyllabusTopic $topic): void {
                SyllabusTopicCoverage::query()
                    ->where('syllabus_topic_id', $topic->copied_from_id)
                    ->update(['syllabus_topic_id' => $topic->id]);
                LessonNote::query()
                    ->where('syllabus_topic_id', $topic->copied_from_id)
                    ->update(['syllabus_topic_id' => $topic->id]);
                DB::table('grade_item_syllabus_topic')
                    ->where('syllabus_topic_id', $topic->copied_from_id)
                    ->update(['syllabus_topic_id' => $topic->id]);
            });
    }
}
