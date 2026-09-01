<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * One thing a graduation plan asks for.
 *
 * @property bool $is_required
 * @property bool $is_negated
 * @property float $pass_mark
 */
class GraduationRequirement extends Model
{
    use HasFactory;

    protected $fillable = [
        'graduation_plan_id',
        'subject_id',
        'description',
        'credits',
        'pass_mark',
        'is_required',
        'is_negated',
    ];

    /**
     * The default values for a new requirement.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'credits' => 1,
        'pass_mark' => 50,
        'is_required' => true,
        'is_negated' => false,
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'credits' => 'integer',
        'pass_mark' => 'float',
        'is_required' => 'boolean',
        'is_negated' => 'boolean',
    ];

    /**
     * Get the plan this belongs to.
     *
     * @return BelongsTo<GraduationPlan, $this>
     */
    public function graduationPlan(): BelongsTo
    {
        return $this->belongsTo(GraduationPlan::class);
    }

    /**
     * Get the subject it asks for, when it names one.
     *
     * @return BelongsTo<Subject, $this>
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Get the students excused from it.
     *
     * @return HasMany<GraduationExemption, $this>
     */
    public function exemptions(): HasMany
    {
        return $this->hasMany(GraduationExemption::class);
    }
}
