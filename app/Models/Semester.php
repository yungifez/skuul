<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class Semester extends Pivot
{
    protected $table = 'semesters';
    
    protected $fillable = ['name', 'school_id', 'academic_year_id'];

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }
}
