<?php

namespace App\Models;

use App\Enums\GradeItemType;
use App\Traits\InSchool;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * One thing a student is marked on.
 *
 * A quiz, a project, an exam paper, a piece of classwork: the gradebook does
 * not care which, so a teacher is free to grade the way the subject needs.
 *
 * @property GradeItemType $type
 * @property float|null $max_points
 * @property float $weight
 */
class GradeItem extends Model
{
    use HasFactory;
    use InSchool;

    protected $fillable = [
        'school_id',
        'course_offering_id',
        'grade_category_id',
        'name',
        'type',
        'grading_scale_id',
        'exam_slot_id',
        'max_points',
        'weight',
        'due_on',
        'position',
        'created_by',
    ];

    /**
     * The default values for a new item.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'type' => GradeItemType::Numeric->value,
        'weight' => 1,
        'position' => 1,
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'type' => GradeItemType::class,
        'max_points' => 'float',
        'weight' => 'float',
        'due_on' => 'date:Y-m-d',
        'position' => 'integer',
    ];

    /**
     * Limit the query to the items of one course offering.
     *
     * @param  Builder<$this>  $query
     * @return Builder<$this>
     */
    public function scopeForCourseOffering(Builder $query, CourseOffering|int $courseOffering): Builder
    {
        return $query->where('course_offering_id', $courseOffering instanceof CourseOffering ? $courseOffering->id : $courseOffering);
    }

    /**
     * Get the offering the item belongs to.
     *
     * @return BelongsTo<CourseOffering, $this>
     */
    public function courseOffering(): BelongsTo
    {
        return $this->belongsTo(CourseOffering::class);
    }

    /**
     * Get the category the item sits in, when it has one.
     *
     * @return BelongsTo<GradeCategory, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(GradeCategory::class, 'grade_category_id');
    }

    /**
     * Get the scale this item uses, when marks are selected from a scale.
     *
     * @return BelongsTo<GradingScale, $this>
     */
    public function gradingScale(): BelongsTo
    {
        return $this->belongsTo(GradingScale::class);
    }

    /**
     * Get the exam paper this assessment records, when it is linked.
     *
     * @return BelongsTo<ExamSlot, $this>
     */
    public function examSlot(): BelongsTo
    {
        return $this->belongsTo(ExamSlot::class);
    }

    /**
     * Get the marks students hold for this item.
     *
     * @return HasMany<GradeEntry, $this>
     */
    public function entries(): HasMany
    {
        return $this->hasMany(GradeEntry::class);
    }
}
