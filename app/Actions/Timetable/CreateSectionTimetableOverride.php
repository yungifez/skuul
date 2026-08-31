<?php

namespace App\Actions\Timetable;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AuditAction;
use App\Enums\TimetableStatus;
use App\Exceptions\InvalidValueException;
use App\Models\AcademicCycleSection;
use App\Models\Timetable;
use App\Models\TimetableRecord;
use App\Models\TimetableTimeSlot;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CreateSectionTimetableOverride
{
    public function __construct(private RecordAuditEvent $auditor) {}

    public function create(Timetable $template, AcademicCycleSection $section, ?User $actor = null): Timetable
    {
        if ($template->status !== TimetableStatus::Published) {
            throw new InvalidValueException('Only a published timetable can be used as a section template.');
        }

        if ($template->academic_period_id !== current_academic_period_id()
            || $section->academic_year_id !== $template->academicPeriod->academic_year_id
            || $section->school_id !== $template->academicCycleSection->school_id) {
            throw new InvalidValueException('The template and target section must belong to the same campus and academic cycle.');
        }

        return DB::transaction(function () use ($template, $section, $actor): Timetable {
            $override = Timetable::create([
                'name' => $template->name.' · '.($section->label ?? $section->name),
                'description' => $template->description,
                'status' => TimetableStatus::Draft,
                'academic_period_id' => $template->academic_period_id,
                'academic_cycle_section_id' => $section->id,
                'template_timetable_id' => $template->id,
                'effective_from' => $template->effective_from,
                'effective_to' => $template->effective_to,
            ]);

            foreach ($template->timeSlots()->get() as $slot) {
                $copy = TimetableTimeSlot::create(['timetable_id' => $override->id, 'start_time' => $slot->start_time, 'stop_time' => $slot->stop_time]);
                foreach (TimetableRecord::query()->where('timetable_time_slot_id', $slot->id)->get() as $record) {
                    TimetableRecord::create([
                        'timetable_time_slot_id' => $copy->id,
                        'weekday_id' => $record->weekday_id,
                        'timetable_time_slot_weekdayable_id' => $record->timetable_time_slot_weekdayable_id,
                        'timetable_time_slot_weekdayable_type' => $record->timetable_time_slot_weekdayable_type,
                        'audience_role' => $record->audience_role,
                        'facility_id' => $record->facility_id,
                    ]);
                }
            }

            $this->auditor->record(AuditAction::TimetableRevised, $override, ['template_timetable_id' => $template->id, 'academic_cycle_section_id' => $section->id], $actor);

            return $override;
        });
    }
}
