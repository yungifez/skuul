<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'my_class_id'];

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
     * Get the StudentRecords that owns the Section.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function studentRecords()
    {
        return $this->hasMany(StudentRecord::class);
    }

    /**
     * Get the students in section.
     *
     * @return Collection
     */
    public function students()
    {
        $students = User::students()->inSchool()->whereRelation('studentRecord.section', 'id', $this->id)->get();

        return $students;
    }
}
