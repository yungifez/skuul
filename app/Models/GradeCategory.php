<?php

namespace App\Models;

use App\Enums\GradeAggregation;
use App\Traits\InSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * A group of grade items inside one subject.
 *
 * A category says how its items are put together, so a school can weigh
 * classwork against a final exam without changing any code.
 *
 * @property GradeAggregation $aggregation
 * @property float            $weight
 */
class GradeCategory extends Model
{
    use HasFactory;
    use InSchool;

    protected $fillable = [
        'school_id',
        'subject_id',
        'academic_year_id',
        'semester_id',
        'parent_id',
        'name',
        'aggregation',
        'weight',
        'position',
    ];

    /**
     * The default values for a new category.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'aggregation' => GradeAggregation::WeightedMean->value,
        'weight'      => 1,
        'position'    => 1,
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'aggregation' => GradeAggregation::class,
        'weight'      => 'float',
        'position'    => 'integer',
    ];

    /**
     * Get the subject the category belongs to.
     *
     * @return BelongsTo<Subject, $this>
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Get the category this one sits in.
     *
     * @return BelongsTo<GradeCategory, $this>
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(GradeCategory::class, 'parent_id');
    }

    /**
     * Get the categories inside this one.
     *
     * @return HasMany<GradeCategory, $this>
     */
    public function children(): HasMany
    {
        return $this->hasMany(GradeCategory::class, 'parent_id')->orderBy('position')->orderBy('id');
    }

    /**
     * Get the items in this category.
     *
     * @return HasMany<GradeItem, $this>
     */
    public function items(): HasMany
    {
        return $this->hasMany(GradeItem::class)->orderBy('position')->orderBy('id');
    }
}
