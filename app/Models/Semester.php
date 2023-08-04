<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Semester extends Model
{
    use HasFactory;

    protected $table = 'semesters';

    protected $fillable = ['name', 'school_id', 'academic_year_id'];

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Get all of the exams for the Semester.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function exams()
    {
        return $this->hasMany(Exam::class, 'semester_id');
    }

    /**
     * Get all of the examSlots for the Semester.
     */
    public function examSlots(): HasManyThrough
    {
        return $this->hasManyThrough(ExamSlot::class, Exam::class, 'semester_id');
    }
}
