<?php

namespace App\Models;

use App\Enums\SchoolMembershipStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Storage;

/**
 * @property int|null $academic_year_id
 * @property int|null $semester_id
 * @property AcademicYear|null $academicYear
 * @property Semester|null $semester
 * @property string $name
 */
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
     *
     * @return HasMany<ClassGroup, $this>
     */
    public function classGroups(): HasMany
    {
        return $this->hasMany(ClassGroup::class);
    }

    /**
     * Get every access record for this school.
     *
     * @return HasMany<SchoolMembership, $this>
     */
    public function memberships(): HasMany
    {
        return $this->hasMany(SchoolMembership::class);
    }

    /**
     * Get the people who can work in this school.
     *
     * @return BelongsToMany<User, $this>
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'school_memberships')
            ->withPivot(['status', 'is_primary', 'joined_at', 'ended_at'])
            ->wherePivot('status', SchoolMembershipStatus::Active->value)
            ->withTimestamps();
    }

    /**
     * Get all of the MyClasses for the School.
     *
     * @return HasManyThrough<MyClass, ClassGroup, $this>
     */
    public function myClasses(): HasManyThrough
    {
        return $this->hasManyThrough(MyClass::class, ClassGroup::class);
    }

    /**
     * Get the AcademicYears for the School.
     *
     * @return HasMany<AcademicYear, $this>
     */
    public function academicYears(): HasMany
    {
        return $this->hasMany(AcademicYear::class);
    }

    /**
     * Get the academicYear associated with the School.
     *
     * @return HasOne<AcademicYear, $this>
     */
    public function academicYear(): HasOne
    {
        return $this->hasOne(AcademicYear::class, 'id', 'academic_year_id');
    }

    /**
     * Get the semester associated with the School.
     *
     * @return HasOne<Semester, $this>
     */
    public function semester(): HasOne
    {
        return $this->hasOne(Semester::class, 'id', 'semester_id');
    }
}
