<?php

namespace App\Actions\Timetable;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AuditAction;
use App\Enums\TimetableStatus;
use App\Exceptions\InvalidValueException;
use App\Exceptions\TimetableConflictException;
use App\Models\Timetable;
use App\Models\User;
use App\Services\Timetable\TimetableConflictChecker;
use Illuminate\Support\Facades\DB;

/**
 * Put a timetable revision into use.
 *
 * Publishing is the moment the schedule becomes a promise to students and
 * teachers, so it is also the moment the clashes are checked. The revision
 * that was in use is archived, not deleted, so the old schedule stays
 * readable.
 */
class PublishTimetable
{
    public function __construct(
        private TimetableConflictChecker $conflictChecker,
        private RecordAuditEvent $auditor,
    ) {}

    /**
     * Publish the revision.
     *
     * Publishing a revision that is already in use changes nothing.
     *
     * @throws InvalidValueException when the state cannot follow the current one
     * @throws TimetableConflictException when entries clash
     */
    public function publish(Timetable $timetable, ?User $actor = null): Timetable
    {
        if ($timetable->status === TimetableStatus::Published) {
            return $timetable;
        }

        if (!$timetable->status->canMoveTo(TimetableStatus::Published)) {
            throw new InvalidValueException('An archived timetable cannot be published again. Start a revision.');
        }

        $conflicts = $this->conflictChecker->conflicts($timetable);

        if ($conflicts !== []) {
            throw new TimetableConflictException($conflicts);
        }

        return DB::transaction(function () use ($timetable, $actor): Timetable {
            // Only one revision of a schedule is in use at a time.
            $inUse = Timetable::query()
                ->published()
                ->where('academic_period_id', $timetable->academic_period_id)
                ->where('academic_cycle_section_id', $timetable->academic_cycle_section_id)
                ->whereKeyNot($timetable->getKey())
                ->get();

            foreach ($inUse as $old) {
                $this->archive($old, $actor);
            }

            $timetable->status = TimetableStatus::Published;
            $timetable->published_at = now();
            $timetable->published_by = $actor === null ? auth()->id() : $actor->id;
            $timetable->save();

            $this->auditor->record(
                AuditAction::TimetablePublished,
                $timetable,
                [
                    'revision' => $timetable->revision,
                    'academic_cycle_section_id' => $timetable->academic_cycle_section_id,
                    'academic_period_id' => $timetable->academic_period_id,
                ],
                $actor,
            );

            return $timetable;
        });
    }

    /**
     * Take the revision out of use and keep it readable.
     */
    public function archive(Timetable $timetable, ?User $actor = null): Timetable
    {
        if ($timetable->status === TimetableStatus::Archived) {
            return $timetable;
        }

        $timetable->status = TimetableStatus::Archived;
        $timetable->save();

        $this->auditor->record(
            AuditAction::TimetableArchived,
            $timetable,
            ['revision' => $timetable->revision],
            $actor,
        );

        return $timetable;
    }
}
