<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * One group or person an event is for.
 *
 * An event with no audience row is for the whole school.
 */
class CalendarEventAudience extends Model
{
    use HasFactory;

    protected $fillable = [
        'calendar_event_id',
        'academic_cycle_section_id',
        'user_id',
        'role',
    ];

    /**
     * Get the event this audience belongs to.
     *
     * @return BelongsTo<CalendarEvent, $this>
     */
    public function calendarEvent(): BelongsTo
    {
        return $this->belongsTo(CalendarEvent::class);
    }

    /**
     * Get the cycle section the event is for, when it names one.
     *
     * A cycle section is the exact home group for one academic cycle. This
     * preserves the audience's historical meaning when a school changes its
     * section structure next year.
     *
     * @return BelongsTo<AcademicCycleSection, $this>
     */
    public function academicCycleSection(): BelongsTo
    {
        return $this->belongsTo(AcademicCycleSection::class);
    }

    /**
     * Get the person the event is for, when it names one.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
