<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

class MyClass extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['name', 'class_group_id'];

    /**
     * Get the classGroup that owns the MyClass.
     *
     * @return BelongsTo<ClassGroup, $this>
     */
    public function classGroup(): BelongsTo
    {
        return $this->belongsTo(ClassGroup::class);
    }

    /**
     * Get all of the sections for the MyClass.
     *
     * @return HasMany<Section, $this>
     */
    public function sections(): HasMany
    {
        return $this->hasMany(Section::class);
    }

    /**
     * Get all of the students for the MyClass.
     *
     * @return HasMany<StudentRecord, $this>
     */
    public function studentRecords(): HasMany
    {
        return $this->hasMany(StudentRecord::class);
    }

    /**
     * The subjects that belong to the MyClass.
     */
    public function subjects(): HasMany
    {
        return $this->hasMany(Subject::class);
    }

    /**
     * Get the students in class.
     */
    public function students(): Collection
    {
        $students = User::students()->ofSchool()->activeStudents()->whereRelation('studentRecord.myClass', 'id', $this->id)->get();

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
