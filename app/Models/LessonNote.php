<?php

namespace App\Models;

use App\Enums\LessonNoteStatus;
use App\Traits\InAcademicPeriod;
use Database\Factories\LessonNoteFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A teacher's plan for one week of lessons, checked by the head of department.
 *
 * A null section means the offering is taught to one group.
 */
class LessonNote extends Model
{
    /** @use HasFactory<LessonNoteFactory> */
    use HasFactory;

    use InAcademicPeriod;

    protected $fillable = [
        'course_offering_id', 'syllabus_topic_id', 'academic_cycle_section_id', 'user_id', 'week',
        'objectives', 'activities', 'evaluation',
        'status', 'submitted_at', 'reviewed_by', 'reviewed_at', 'review_note',
    ];

    protected $attributes = [
        'status' => LessonNoteStatus::Draft->value,
    ];

    protected $casts = [
        'status' => LessonNoteStatus::class,
        'week' => 'integer',
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    /**
     * @return BelongsTo<CourseOffering, $this>
     */
    public function courseOffering(): BelongsTo
    {
        return $this->belongsTo(CourseOffering::class);
    }

    /**
     * @return BelongsTo<SyllabusTopic, $this>
     */
    public function topic(): BelongsTo
    {
        return $this->belongsTo(SyllabusTopic::class, 'syllabus_topic_id');
    }

    /**
     * @return BelongsTo<AcademicCycleSection, $this>
     */
    public function academicCycleSection(): BelongsTo
    {
        return $this->belongsTo(AcademicCycleSection::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id')->withTrashed();
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by')->withTrashed();
    }

    /**
     * Limit lesson notes to one school through their course offering.
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
     * Get the academic period that freezes this note.
     */
    public function governingAcademicPeriod(): AcademicYear|AcademicPeriod|null
    {
        return $this->courseOffering?->academicPeriod;
    }
}
