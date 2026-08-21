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
        'my_class_id',
        'section_id',
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
     * Get the class the event is for, when it names one.
     *
     * @return BelongsTo<MyClass, $this>
     */
    public function myClass(): BelongsTo
    {
        return $this->belongsTo(MyClass::class);
    }

    /**
     * Get the section the event is for, when it names one.
     *
     * @return BelongsTo<Section, $this>
     */
    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
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
