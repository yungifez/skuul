<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

class MyClass extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['name', 'class_group_id'];

    public function school()
    {
        $this->hasOneThrough(School::class, ClassGroup::class);
    }

    /**
     * Get the classGroup that owns the MyClass.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function classGroup()
    {
        return $this->belongsTo(ClassGroup::class);
    }

    /**
     * Get all of the sections for the MyClass.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sections()
    {
        return $this->hasMany(Section::class);
    }

    /**
     * Get all of the students for the MyClass.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function studentRecords()
    {
        return $this->hasMany(StudentRecord::class);
    }

    /**
     * The subjects that belong to the MyClass.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function subjects()
    {
        return $this->hasMany(Subject::class);
    }

    /**
     * Get the students in class.
     *
     * @return Collection
     */
    public function students()
    {
        $students = User::students()->inSchool()->whereRelation('studentRecord.myClass', 'id', $this->id)->get();

        return $students;
    }

    /**
     * Get all of the syllabi for the MyClass.
     */
    public function syllabi(): HasManyThrough
    {
        return $this->hasManyThrough(Syllabus::class, Subject::class);
    }

    /**
     * Get all of the timetables for the MyClass.
     */
    public function timetables(): HasMany
    {
        return $this->hasMany(Timetable::class);
    }
}
