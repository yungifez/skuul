<?php

namespace App\Models;

use App\Traits\InSchool;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * What a student must finish before they can leave with a qualification.
 *
 * A school that does not count credits leaves `uses_credits` off and finishes
 * the plan by its requirements alone.
 *
 * @property bool $uses_credits
 * @property string $completion_operator
 * @property int|null $required_count
 * @property int|null $required_credits
 * @property bool $is_negated
 */
class GraduationPlan extends Model
{
    use HasFactory;
    use InSchool;

    protected $fillable = [
        'school_id',
        'parent_id',
        'name',
        'description',
        'completion_operator',
        'required_count',
        'position',
        'is_negated',
        'uses_credits',
        'required_credits',
        'cohort_id',
        'is_active',
    ];

    /**
     * The default values for a new plan.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'completion_operator' => 'all',
        'required_count' => null,
        'position' => 0,
        'is_negated' => false,
        'uses_credits' => false,
        'is_active' => true,
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'position' => 'integer',
        'required_count' => 'integer',
        'is_negated' => 'boolean',
        'uses_credits' => 'boolean',
        'is_active' => 'boolean',
        'required_credits' => 'integer',
    ];

    /**
     * Limit the query to the plans still in use.
     *
     * @param  Builder<$this>  $query
     * @return Builder<$this>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Get what the plan asks for.
     *
     * @return HasMany<GraduationRequirement, $this>
     */
    public function requirements(): HasMany
    {
        return $this->hasMany(GraduationRequirement::class)->orderBy('id');
    }

    /**
     * Get the stage this plan sits under, when it is nested.
     *
     * @return BelongsTo<GraduationPlan, $this>
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(GraduationPlan::class, 'parent_id');
    }

    /**
     * Get the stages that follow this plan.
     *
     * @return HasMany<GraduationPlan, $this>
     */
    public function children(): HasMany
    {
        return $this->hasMany(GraduationPlan::class, 'parent_id')
            ->orderBy('position')
            ->orderBy('id');
    }

    /**
     * Get the group the plan is for, when it names one.
     *
     * @return BelongsTo<Cohort, $this>
     */
    public function cohort(): BelongsTo
    {
        return $this->belongsTo(Cohort::class);
    }
}
