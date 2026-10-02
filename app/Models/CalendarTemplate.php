<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * A description of how an organization divides its academic year.
 *
 * The template holds shape, not dates: how many periods, in what order, of
 * what kind, and how long each runs. A campus generates a cycle from it by
 * naming one start date.
 *
 * @property string $name
 * @property string|null $description
 * @property bool $is_default
 * @property int $cycle_length_days
 * @property int $organization_id
 */
class CalendarTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id',
        'name',
        'description',
        'is_default',
        'cycle_length_days',
        'auto_open',
        'generate_ahead_weeks',
        'remind_days_before',
        'created_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_default' => 'boolean',
        'cycle_length_days' => 'integer',
        'auto_open' => 'boolean',
        'generate_ahead_weeks' => 'integer',
        'remind_days_before' => 'integer',
    ];

    /**
     * Check if the next cycle is built without anyone asking.
     */
    public function generatesAhead(): bool
    {
        return $this->generate_ahead_weeks > 0;
    }

    /**
     * Get the organization that owns the template.
     *
     * @return BelongsTo<Organization, $this>
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Get every period in the template, sub-periods included, in order.
     *
     * @return HasMany<CalendarTemplatePeriod, $this>
     */
    public function periods(): HasMany
    {
        return $this->hasMany(CalendarTemplatePeriod::class)->orderBy('position')->orderBy('id');
    }

    /**
     * Get the periods that divide the cycle, without their sub-periods.
     *
     * @return HasMany<CalendarTemplatePeriod, $this>
     */
    public function topLevelPeriods(): HasMany
    {
        return $this->periods()->whereNull('parent_id');
    }

    /**
     * Get the person who created the template.
     *
     * @return BelongsTo<User, $this>
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the campuses that follow this template by name.
     *
     * A campus that follows the organization default is not listed here: it
     * has no template of its own.
     *
     * @return HasMany<School, $this>
     */
    public function schools(): HasMany
    {
        return $this->hasMany(School::class);
    }
}
