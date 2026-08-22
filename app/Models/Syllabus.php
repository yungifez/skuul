<?php

namespace App\Models;

use App\Traits\InAcademicPeriod;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Syllabus extends Model
{
    use HasFactory;
    use InAcademicPeriod;

    protected $fillable = [
        'name', 'description', 'file', 'course_offering_id',
    ];

    /**
     * Get the exact offering this syllabus supports.
     *
     * @return BelongsTo<CourseOffering, $this>
     */
    public function courseOffering(): BelongsTo
    {
        return $this->belongsTo(CourseOffering::class);
    }

    /**
     * Limit syllabi to one school through their course offering.
     *
     * @param  Builder<$this>  $query
     * @return Builder<$this>
     */
    public function scopeInSchool(Builder $query, School|int|null $school = null): Builder
    {
        $schoolId = $school instanceof School ? $school->id : ($school ?? current_school_id());

        return $query->whereHas('courseOffering', function (Builder $courseOfferings) use ($schoolId): void {
            $courseOfferings->where('school_id', $schoolId);
        });
    }

    /**
     * Get the academic period that freezes this syllabus.
     */
    public function governingAcademicPeriod(): AcademicYear|AcademicPeriod|null
    {
        return $this->courseOffering?->academicPeriod;
    }
}
