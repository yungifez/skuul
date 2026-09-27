<?php

namespace App\Models;

use App\Traits\InAcademicPeriod;
use Database\Factories\SyllabusTopicFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * One planned topic in a syllabus: what is taught, in which week, and why.
 */
class SyllabusTopic extends Model
{
    /** @use HasFactory<SyllabusTopicFactory> */
    use HasFactory;

    use InAcademicPeriod;

    protected $fillable = [
        'syllabus_id', 'copied_from_id', 'week', 'position', 'title', 'objectives', 'content', 'resources',
    ];

    protected $casts = [
        'week' => 'integer',
        'position' => 'integer',
    ];

    /**
     * Get the syllabus this topic belongs to.
     *
     * @return BelongsTo<Syllabus, $this>
     */
    public function syllabus(): BelongsTo
    {
        return $this->belongsTo(Syllabus::class);
    }

    /**
     * Get the gradebook items that assess this topic.
     *
     * @return BelongsToMany<GradeItem, $this>
     */
    public function gradeItems(): BelongsToMany
    {
        return $this->belongsToMany(GradeItem::class)->withTimestamps();
    }

    /**
     * Get what each class was taught of this topic.
     *
     * @return HasMany<SyllabusTopicCoverage, $this>
     */
    public function coverages(): HasMany
    {
        return $this->hasMany(SyllabusTopicCoverage::class);
    }

    /**
     * Get the academic period that freezes this topic.
     */
    public function governingAcademicPeriod(): AcademicYear|AcademicPeriod|null
    {
        return $this->syllabus?->governingAcademicPeriod();
    }
}
