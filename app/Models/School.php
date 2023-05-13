<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Storage;

class School extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'address', 'code', 'initials', 'phone', 'email', 'logo_path',
    ];

    public function getLogoUrlAttribute()
    {
        return $this->logo_path ? Storage::url($this->logo_path) : asset(config('app.logo'));
    }

    /**
     * Get all the class groups in the school.
     */
    public function classGroups(): HasMany
    {
        return $this->hasMany(ClassGroup::class);
    }

    /**
     * Get all of the users for the School.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get all of the MyClasses for the School.
     */
    public function myClasses(): HasManyThrough
    {
        return $this->hasManyThrough(MyClass::class, ClassGroup::class);
    }

    /**
     * Get the AcademicYears for the School.
     */
    public function academicYears(): HasMany
    {
        return $this->hasMany(AcademicYear::class);
    }

    /**
     * Get the academicYear associated with the School.
     */
    public function academicYear(): HasOne
    {
        return $this->hasOne(AcademicYear::class, 'id', 'academic_year_id');
    }

    /**
     * Get the semester associated with the School.
     */
    public function semester(): HasOne
    {
        return $this->hasOne(Semester::class, 'id', 'semester_id');
    }
}
