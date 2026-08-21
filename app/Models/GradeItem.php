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
 * @property float|null    $max_points
 * @property float         $weight
 */
class GradeItem extends Model
{
    use HasFactory;
    use InSchool;

    protected $fillable = [
        'school_id',
        'subject_id',
        'academic_year_id',
        'semester_id',
        'grade_category_id',
        'name',
        'type',
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
        'type'     => GradeItemType::Numeric->value,
        'weight'   => 1,
        'position' => 1,
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'type'       => GradeItemType::class,
        'max_points' => 'float',
        'weight'     => 'float',
        'due_on'     => 'date:Y-m-d',
        'position'   => 'integer',
    ];

    /**
     * Limit the query to the items of one subject.
     *
     * @param Builder<$this> $query
     *
     * @return Builder<$this>
     */
    public function scopeForSubject(Builder $query, Subject|int $subject): Builder
    {
        return $query->where('subject_id', $subject instanceof Subject ? $subject->id : $subject);
    }

    /**
     * Get the subject the item belongs to.
     *
     * @return BelongsTo<Subject, $this>
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
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
     * Get the academic year the item belongs to.
     *
     * @return BelongsTo<AcademicYear, $this>
     */
    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    /**
     * Get the period the item belongs to, when it names one.
     *
     * @return BelongsTo<Semester, $this>
     */
    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
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
