<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subject extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name', 'short_name', 'school_id', 'my_class_id',
    ];

    /**
     * Get the class that owns the Subject.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function myClass()
    {
        return $this->belongsTo(MyClass::class);
    }

    /**
     * The teachers that belong to the Subject.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function teachers()
    {
        return $this->belongsToMany(User::class, 'subject_user');
    }

    /**
     * Get the subjects timetable records.
     */
    public function timetableRecord(): MorphOne
    {
        return $this->morphOne(TimetableRecord::class, 'timetable_time_slot_weekdayable');
    }
}
