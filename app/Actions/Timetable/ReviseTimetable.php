<?php

namespace App\Actions\Timetable;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AuditAction;
use App\Enums\TimetableStatus;
use App\Models\Timetable;
use App\Models\TimetableRecord;
use App\Models\TimetableTimeSlot;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Start the next revision of a published timetable.
 *
 * The published revision keeps its entries. The new draft starts as a copy,
 * so a small change does not mean typing the week again.
 */
class ReviseTimetable
{
    public function __construct(private RecordAuditEvent $auditor) {}

    /**
     * Copy the timetable into a new draft revision.
     */
    public function revise(Timetable $timetable, ?User $actor = null): Timetable
    {
        return DB::transaction(function () use ($timetable, $actor): Timetable {
            $draft = Timetable::create([
                'name' => $timetable->name,
                'description' => $timetable->description,
                'status' => TimetableStatus::Draft,
                'revision' => $timetable->revision + 1,
                'academic_period_id' => $timetable->academic_period_id,
                'academic_cycle_section_id' => $timetable->academic_cycle_section_id,
                'effective_from' => $timetable->effective_from,
                'effective_to' => $timetable->effective_to,
                'revision_of_id' => $timetable->id,
            ]);

            foreach ($timetable->timeSlots()->get() as $slot) {
                $copy = TimetableTimeSlot::create([
                    'timetable_id' => $draft->id,
                    'start_time' => $slot->start_time,
                    'stop_time' => $slot->stop_time,
                ]);

                $records = TimetableRecord::query()
                    ->where('timetable_time_slot_id', $slot->id)
                    ->get();

                foreach ($records as $record) {
                    TimetableRecord::create([
                        'timetable_time_slot_id' => $copy->id,
                        'weekday_id' => $record->weekday_id,
                        'timetable_time_slot_weekdayable_id' => $record->timetable_time_slot_weekdayable_id,
                        'timetable_time_slot_weekdayable_type' => $record->timetable_time_slot_weekdayable_type,
                    ]);
                }
            }

            $this->auditor->record(
                AuditAction::TimetableRevised,
                $draft,
                ['revision' => $draft->revision, 'revision_of_id' => $timetable->id],
                $actor,
            );

            return $draft;
        });
    }
}
