<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class TimetableRecord extends Pivot
{
    use HasFactory;

    protected $table = 'timetable_time_slot_weekday';

    protected $fillable = [
        'timetable_time_slot_id',
        'weekday_id',
        'timetable_time_slot_weekdayable_id',
        'timetable_time_slot_weekdayable_type',
        'audience_role',
        'facility_id',
    ];

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
     * Get the slot this entry sits in.
     *
     * @return BelongsTo<TimetableTimeSlot, $this>
     */
    public function timeSlot(): BelongsTo
    {
        return $this->belongsTo(TimetableTimeSlot::class, 'timetable_time_slot_id');
    }

    /**
     * Get the day of the week this entry sits on.
     *
     * @return BelongsTo<Weekday, $this>
     */
    public function weekday(): BelongsTo
    {
        return $this->belongsTo(Weekday::class);
    }

    /**
     * Get the shared thing this lesson was moved into, when it was moved.
     *
     * @return BelongsTo<Facility, $this>
     */
    public function facility(): BelongsTo
    {
        return $this->belongsTo(Facility::class);
    }

    /**
     * Get the parent timetableRecordable model (subject or custom).
     */
    public function timetableRecordable()
    {
        return $this->morphTo('timetable_time_slot_weekdayable');
    }
}
