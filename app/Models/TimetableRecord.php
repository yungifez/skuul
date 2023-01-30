<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class TimetableRecord extends Pivot
{
    use HasFactory;

    protected $table = 'timetable_time_slot_weekday';

    public function timetableRecordableType(): Attribute
    {
        return new Attribute(
            get: fn ($value) => $this->timetable_time_slot_weekdayable_type,
            set: fn ($value) => $this->timetable_time_slot_weekdayable_type,
        );
    }

    public function timetableRecordableId(): Attribute
    {
        return new Attribute(
            get: fn ($value) => $this->timetable_time_slot_weekdayable_id,
            set: fn ($value) => $this->timetable_time_slot_weekdayable_id,
        );
    }

    /**
     * Get the parent timetableRecordable model (subject or custom).
     */
    public function timetableRecordable()
    {
        return $this->morphTo('timetable_time_slot_weekdayable');
    }
}
