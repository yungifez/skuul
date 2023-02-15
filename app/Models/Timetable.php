<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class Timetable extends Pivot
{
    use HasFactory;

    protected $table = 'timetables';

    protected $fillable = [
        'name',
        'description',
        'semester_id',
        'my_class_id',
    ];

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    public function MyClass()
    {
        return $this->belongsTo(MyClass::class);
    }

    /**
     * Get all of the timeSlots for the Timetable.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function timeSlots()
    {
        return $this->hasMany(TimetableTimeSlot::class, 'timetable_id');
    }
}
