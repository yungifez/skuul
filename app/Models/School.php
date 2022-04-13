<?php

namespace App\Models;

use App\Models\User;
use App\Models\MyClass;
use App\Models\Semester;
use App\Models\ClassGroup;
use App\Models\AcademicYear;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class School extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'address', 'code', 'initials',
    ];

    public function classGroups()
    {
        return $this->hasMany(ClassGroup::class);
    }

    /**
     * Get all of the users for the School
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get all of the MyClasses for the School
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function myClasses()
    {
        return $this->hasManyThrough(MyClass::class, ClassGroup::class);
    }

    /**
     * Get the AcademicYears for the School
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */

    public function academicYears()
    {
        return $this->hasMany(AcademicYear::class);
    }

    /**
     * Get the academicYear associated with the School
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function academicYear()
    {
        return $this->hasOne(AcademicYear::class,'id', 'academic_year_id');
    }

    /**
     * Get the semester associated with the School
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function semester()
    {
        return $this->hasOne(Semester::class, 'id', 'semester_id');
    }
}
