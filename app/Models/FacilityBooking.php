<?php

namespace App\Models;

use App\Traits\InSchool;
use Database\Factories\FacilityBookingFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * One stretch of time somebody has claimed a shared thing for.
 */
class FacilityBooking extends Model
{
    /** @use HasFactory<FacilityBookingFactory> */
    use HasFactory;

    use InSchool;

    protected $fillable = [
        'school_id',
        'facility_id',
        'starts_at',
        'ends_at',
        'purpose',
        'booked_by',
        'cancelled_at',
        'cancelled_by',
        'cancelled_reason',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    /**
     * Get the thing that was booked.
     *
     * @return BelongsTo<Facility, $this>
     */
    public function facility(): BelongsTo
    {
        return $this->belongsTo(Facility::class);
    }

    /**
     * Get the person who booked it.
     *
     * @return BelongsTo<User, $this>
     */
    public function bookedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'booked_by');
    }

    /**
     * Check whether the booking still stands.
     */
    public function isRunning(): bool
    {
        return $this->cancelled_at === null;
    }

    /**
     * Limit the query to the bookings that still stand.
     *
     * @param  Builder<$this>  $query
     * @return Builder<$this>
     */
    public function scopeRunning(Builder $query): Builder
    {
        return $query->whereNull('cancelled_at');
    }

    /**
     * Limit the query to the bookings that overlap a stretch of time.
     *
     * A booking that ends exactly as another starts does not overlap, so a
     * hall can be handed straight from one class to the next.
     *
     * @param  Builder<$this>  $query
     * @return Builder<$this>
     */
    public function scopeOverlapping(Builder $query, string $from, string $to): Builder
    {
        return $query->where('starts_at', '<', $to)->where('ends_at', '>', $from);
    }
}
