<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MyClass extends Model
{
    use HasFactory;

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
        $students = User::students()->inSchool()->whereRelation('studentRecord.myClass','id', $this->id)->get();


        return $students;
    }
}
