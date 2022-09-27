<?php

namespace App\Models;

use App\Models\StudentRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class AcademicYear extends Model
{
    use HasFactory;

    protected $fillable = [
        'start_year',
        'stop_year',
        'school_id',
    ];

    public function name()
    {
        return "$this->start_year - $this->stop_year";
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    //semesters
    public function semesters()
    {
        return $this->hasMany(Semester::class);
    }
    
    /**
     * The studentRecords that belong to the AcademicYear
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function studentRecords(): BelongsToMany
    {
        return $this->belongsToMany(StudentRecord::class)->as('studentAcademicYearBasedRecords')->using(AcademicYearStudentRecord::class)->withPivot('my_class_id', 'section_id');
    }
}
