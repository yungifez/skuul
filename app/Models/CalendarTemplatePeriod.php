<?php

namespace App\Models;

use App\Enums\AcademicPeriodType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * One period inside a calendar template.
 *
 * Dates are offsets from the first day of the cycle, so the same row makes
 * "Term 1" for every year the organization ever runs.
 *
 * @property string $name
 * @property string|null $label
 * @property AcademicPeriodType $type
 * @property int $position
 * @property int $start_offset_days
 * @property int $length_days
 * @property int|null $parent_id
 */
class CalendarTemplatePeriod extends Model
{
    use HasFactory;

    protected $fillable = [
        'calendar_template_id',
        'parent_id',
        'name',
        'label',
        'type',
        'position',
        'start_offset_days',
        'length_days',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'type' => AcademicPeriodType::class,
        'position' => 'integer',
        'start_offset_days' => 'integer',
        'length_days' => 'integer',
    ];

    /**
     * The default values for a new template period.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'type' => AcademicPeriodType::Term->value,
    ];

    /**
     * Read the periods in the order the organization teaches them.
     *
     * @param  Builder<$this>  $query
     * @return Builder<$this>
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('position')->orderBy('id');
    }

    /**
     * Get the template that owns this period.
     *
     * @return BelongsTo<CalendarTemplate, $this>
     */
    public function calendarTemplate(): BelongsTo
    {
        return $this->belongsTo(CalendarTemplate::class);
    }

    /**
     * Get the template period this one sits inside.
     *
     * @return BelongsTo<self, $this>
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /**
     * Get the template periods inside this one, in order.
     *
     * @return HasMany<self, $this>
     */
    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->ordered();
    }

    /**
     * Work out the first day of this period for a cycle starting on the day.
     */
    public function startsOn(Carbon $cycleStart): Carbon
    {
        return $cycleStart->copy()->addDays($this->start_offset_days);
    }

    /**
     * Work out the last day of this period for a cycle starting on the day.
     *
     * A period one day long starts and ends on the same day, so the last day
     * is the first day plus one less than the length.
     */
    public function endsOn(Carbon $cycleStart): Carbon
    {
        return $this->startsOn($cycleStart)->addDays(max($this->length_days, 1) - 1);
    }
}
