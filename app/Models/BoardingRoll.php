<?php

namespace App\Models;

use App\Enums\BoardingRollType;
use App\Traits\InSchool;
use Database\Factories\BoardingRollFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * One regular accountability check for one boarding house on one day.
 *
 * @property BoardingRollType $type
 */
class BoardingRoll extends Model
{
    /** @use HasFactory<BoardingRollFactory> */
    use HasFactory;

    use InSchool;

    protected $fillable = [
        'school_id',
        'dormitory_id',
        'type',
        'taken_on',
        'completed_at',
        'started_by',
        'completed_by',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'type' => BoardingRollType::class,
        'taken_on' => 'date:Y-m-d',
        'completed_at' => 'datetime',
    ];

    /**
     * Get the house checked by this roll.
     *
     * @return BelongsTo<Dormitory, $this>
     */
    public function dormitory(): BelongsTo
    {
        return $this->belongsTo(Dormitory::class);
    }

    /**
     * Get every boarder checked by this roll.
     *
     * @return HasMany<BoardingRollEntry, $this>
     */
    public function entries(): HasMany
    {
        return $this->hasMany(BoardingRollEntry::class);
    }

    /**
     * Limit the query to one day.
     *
     * @param  Builder<$this>  $query
     * @return Builder<$this>
     */
    public function scopeOnDate(Builder $query, string $date): Builder
    {
        return $query->whereDate('taken_on', $date);
    }

    /**
     * Check whether staff have finished this roll.
     */
    public function isComplete(): bool
    {
        return $this->completed_at !== null;
    }
}
