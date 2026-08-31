<?php

namespace App\Models;

use App\Exceptions\InvalidValueException;
use App\Traits\InAcademicPeriod;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class TimetableTimeSlot extends Model
{
    use HasFactory;
    use InAcademicPeriod;

    protected $fillable = [
        'start_time',
        'stop_time',
        'timetable_id',
        'recurrence',
        'occurs_on',
    ];

    protected $attributes = [
        'recurrence' => 'weekly',
    ];

    protected $casts = [
        'occurs_on' => 'date:Y-m-d',
    ];

    protected $getDateFormat = 'H:i';

    /**
     * A slot of a published timetable stops changing with it.
     */
    protected static function booted(): void
    {
        $failIfPublished = function (self $slot): void {
            if ($slot->timetable?->isPublished()) {
                throw new InvalidValueException('This timetable is published. Create a revision to change it.');
            }
        };

        static::saving($failIfPublished);
        static::deleting($failIfPublished);
    }

    public function startTime(): Attribute
    {
        return new Attribute(
            get: fn ($value) => Carbon::parse($value)->format($this->getDateFormat),
            set: fn ($value) => $value,
        );
    }

    public function stopTime(): Attribute
    {
        return new Attribute(
            get: fn ($value) => Carbon::parse($value)->format($this->getDateFormat),
            set: fn ($value) => $value,
        );
    }

    public function name(): Attribute
    {
        return new Attribute(
            get: fn ($value) => sprintf('%s - %s', $this->start_time, $this->stop_time),
            set: fn ($value) => "$this->start_time - $this->stop_time",
        );
    }

    /**
     * Get the timetable that owns the slot.
     *
     * @return BelongsTo<Timetable, $this>
     */
    public function timetable(): BelongsTo
    {
        return $this->belongsTo(Timetable::class);
    }

    /**
     * Get the academic period that governs this timetable slot.
     */
    public function governingAcademicPeriod(): AcademicYear|AcademicPeriod|null
    {
        return $this->timetable?->academicPeriod;
    }

    /**
     * Check if a one-date slot no longer falls inside its academic period.
     */
    public function occursOutsideAcademicPeriod(): bool
    {
        if ($this->recurrence !== 'one_time') {
            return false;
        }

        return $this->occurs_on === null
            || !$this->governingAcademicPeriod()?->covers($this->occurs_on);
    }

    public function weekdays(): BelongsToMany
    {
        // get pivot table as timetableRecords
        return $this->belongsToMany(Weekday::class)
            ->as('timetableRecord')
            ->withPivot([
                'timetable_time_slot_weekdayable_id',
                'timetable_time_slot_weekdayable_type',
                'audience_role',
                'facility_id',
            ])
            ->withTimestamps()
            ->using(TimetableRecord::class);
    }
}
