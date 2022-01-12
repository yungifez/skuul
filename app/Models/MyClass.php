<?php

namespace App\Models;

use App\Models\User;
use App\Models\Section;
use App\Models\ClassGroup;
use App\Models\StudentRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MyClass extends Model
{
    use HasFactory;

    protected $fillable =['name', 'class_group_id'];

    /**
     * Get the classGroup that owns the MyClass
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function classGroup()
    {
        return $this->belongsTo(ClassGroup::class);
    }

    /**
     * Get all of the sections for the MyClass
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sections()
    {
        return $this->hasMany(Section::class);
    }

    //check if section is in a class
    public function isSectionInClass($sectionId)
    {
        return MyClass::whereHas('sections', function($query) use ($sectionId){
            $query->where('id', $sectionId);
        })->exists();
    }

    /**
     * Get all of the students for the MyClass
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function studentRecords()
    {
        return $this->hasMany(StudentRecord::class);
    }
}
