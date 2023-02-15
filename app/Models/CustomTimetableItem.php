<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class CustomTimetableItem extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'school_id'];

    /**
     * Get the school that owns the CustomTimetableItem.
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Get the subjects timetable records.
     */
    public function timetableRecord(): MorphOne
    {
        return $this->morphOne(TimetableRecord::class, 'timetable_time_slot_weekdayable');
    }
}
