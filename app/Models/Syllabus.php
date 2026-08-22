<?php

namespace App\Models;

use App\Enums\SyllabusStatus;
use App\Traits\InAcademicPeriod;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Syllabus extends Model
{
    use HasFactory;
    use InAcademicPeriod;

    protected $fillable = [
        'name', 'description', 'file', 'course_offering_id',
        'status', 'revision', 'revision_of_id', 'published_at', 'published_by',
    ];

    protected $attributes = [
        'status'   => SyllabusStatus::Draft->value,
        'revision' => 1,
    ];

    protected $casts = [
        'status'       => SyllabusStatus::class,
        'revision'     => 'integer',
        'published_at' => 'datetime',
    ];

    public function revisionOf(): BelongsTo
    {
        return $this->belongsTo(self::class, 'revision_of_id');
    }

    public function revisions(): HasMany
    {
        return $this->hasMany(self::class, 'revision_of_id');
    }

    public function publishedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'published_by');
    }

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
     * @param Builder<$this> $query
     *
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
