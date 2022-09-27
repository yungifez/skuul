<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\AcademicYear;
use Illuminate\Database\Eloquent\Model;
use App\Models\AcademicYearStudentRecord;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class StudentRecord extends Model
{
    use HasFactory;

    protected $fillable = ['admission_number', 'admission_date', 'my_class_id', 'section_id'];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'admission_date' => 'datetime:Y-m-d',
    ];

    //accessor for admission_date

    public function getAdmissionDateAttribute($value)
    {
        return Carbon::parse($value)->format('Y/m/d');
    }

    /**
     * Get the MyClass that owns the Section.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function myClass()
    {
        return $this->belongsTo(MyClass::class);
    }

    /**
     * Get the user that owns the StudentRecord.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * The academicYears that belong to the StudentRecord
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function academicYears(): BelongsToMany
    {
        return $this->belongsToMany(AcademicYear::class)->as('studentAcademicYearBasedRecords')->using(AcademicYearStudentRecord::class)->withPivot('my_class_id', 'section_id');
    }

    /**
     * Get current academic year
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function currentAcademicYear()
    {
        return $this->academicYears()->wherePivot('academic_year_id', $this->user->school->academicYear->id);
    }

}
