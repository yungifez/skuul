<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
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

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

    public function myClass(): BelongsTo
    {
        return $this->belongsTo(MyClass::class);
    }

    public function timeSlots(): HasMany
    {
        return $this->hasMany(TimetableTimeSlot::class, 'timetable_id');
    }
}
