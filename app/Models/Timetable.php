<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class Timetable extends Pivot
{
    protected $fillable = [
        'name',
        'semester_id',
        'subject_id',
    ];

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
}
