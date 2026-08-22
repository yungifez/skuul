<?php

namespace App\Models;

use Database\Factories\TimetableSubstitutionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TimetableSubstitution extends Model
{
    /** @use HasFactory<TimetableSubstitutionFactory> */
    use HasFactory;

    protected $fillable = ['timetable_id', 'timetable_time_slot_id', 'weekday_id', 'replacement_teacher_id', 'substituted_on', 'reason', 'approved_by'];

    protected $casts = ['substituted_on' => 'date:Y-m-d'];

    /**
     * Get the published timetable this date-specific replacement belongs to.
     *
     * @return BelongsTo<Timetable, $this>
     */
    public function timetable(): BelongsTo
    {
        return $this->belongsTo(Timetable::class);
    }

    /**
     * Get the scheduled time slot being covered.
     *
     * @return BelongsTo<TimetableTimeSlot, $this>
     */
    public function timeSlot(): BelongsTo
    {
        return $this->belongsTo(TimetableTimeSlot::class, 'timetable_time_slot_id');
    }

    /**
     * Get the regular weekday of the lesson.
     *
     * @return BelongsTo<Weekday, $this>
     */
    public function weekday(): BelongsTo
    {
        return $this->belongsTo(Weekday::class);
    }

    /**
     * Get the teacher covering the entry.
     *
     * @return BelongsTo<User, $this>
     */
    public function replacementTeacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'replacement_teacher_id');
    }

    /**
     * Get the staff member who approved the substitution.
     *
     * @return BelongsTo<User, $this>
     */
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
