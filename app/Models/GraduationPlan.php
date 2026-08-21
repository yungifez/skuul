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
 */
class GraduationPlan extends Model
{
    use HasFactory;
    use InSchool;

    protected $fillable = [
        'school_id',
        'name',
        'description',
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
        'uses_credits' => false,
        'is_active'    => true,
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'uses_credits'     => 'boolean',
        'is_active'        => 'boolean',
        'required_credits' => 'integer',
    ];

    /**
     * Limit the query to the plans still in use.
     *
     * @param Builder<$this> $query
     *
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
     * Get the group the plan is for, when it names one.
     *
     * @return BelongsTo<Cohort, $this>
     */
    public function cohort(): BelongsTo
    {
        return $this->belongsTo(Cohort::class);
    }
}
