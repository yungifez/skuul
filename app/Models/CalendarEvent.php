<?php

namespace App\Models;

use App\Enums\CalendarEventType;
use App\Traits\InSchool;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * One day or event on the school calendar.
 *
 * @property CalendarEventType $type
 * @property Carbon $starts_at
 * @property Carbon $ends_at
 */
class CalendarEvent extends Model
{
    use HasFactory;
    use InSchool;

    protected $fillable = [
        'school_id',
        'academic_year_id',
        'semester_id',
        'title',
        'description',
        'type',
        'location',
        'is_all_day',
        'is_published',
        'starts_at',
        'ends_at',
        'created_by',
    ];

    /**
     * The default values for a new event.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'type' => CalendarEventType::Other->value,
        'is_all_day' => true,
        'is_published' => false,
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'type' => CalendarEventType::class,
        'is_all_day' => 'boolean',
        'is_published' => 'boolean',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    /**
     * Limit the query to the events that cover a day.
     *
     * @param  Builder<$this>  $query
     * @return Builder<$this>
     */
    public function scopeCovering(Builder $query, DateTimeInterface|string $date): Builder
    {
        $day = Carbon::parse($date);

        return $query->where('starts_at', '<=', $day->copy()->endOfDay())
            ->where('ends_at', '>=', $day->copy()->startOfDay());
    }

    /**
     * Limit the query to the events between two days.
     *
     * @param  Builder<$this>  $query
     * @return Builder<$this>
     */
    public function scopeBetween(Builder $query, DateTimeInterface|string $from, DateTimeInterface|string $to): Builder
    {
        return $query->where('starts_at', '<=', Carbon::parse($to)->endOfDay())
            ->where('ends_at', '>=', Carbon::parse($from)->startOfDay());
    }

    /**
     * Limit the query to the events people can see.
     *
     * @param  Builder<$this>  $query
     * @return Builder<$this>
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    /**
     * Get who the event is for.
     *
     * @return HasMany<CalendarEventAudience, $this>
     */
    public function audiences(): HasMany
    {
        return $this->hasMany(CalendarEventAudience::class);
    }

    /**
     * Get the person who put the event on the calendar.
     *
     * @return BelongsTo<User, $this>
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Check if the school teaches during this event.
     */
    public function isTeachingDay(): bool
    {
        return $this->type->isTeachingDay();
    }

    /**
     * Check if the event is for the whole school.
     */
    public function isForEverybody(): bool
    {
        return $this->audiences()->count() === 0;
    }
}
