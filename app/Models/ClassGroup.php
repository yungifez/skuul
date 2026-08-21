<?php

namespace App\Models;

use App\Traits\InSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClassGroup extends Model
{
    use HasFactory;
    use InSchool;

    protected $fillable = [
        'name', 'school_id',
    ];

    /**
     * Get the school that owns the class group.
     *
     * @return BelongsTo<School, $this>
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function classes()
    {
        return $this->hasMany(MyClass::class);
    }

    public function gradeSystem()
    {
        return $this->hasMany(GradeSystem::class);
    }
}
