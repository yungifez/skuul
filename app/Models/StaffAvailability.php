<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * One block of time a member of staff can work in an ordinary week.
 *
 * The day of week follows ISO order: Monday is 1 and Sunday is 7.
 */
class StaffAvailability extends Model
{
    use HasFactory;

    protected $fillable = [
        'staff_profile_id',
        'day_of_week',
        'starts_at',
        'ends_at',
    ];

    /**
     * Check if the block covers the whole of the given time.
     */
    public function covers(string $startsAt, string $endsAt): bool
    {
        return $this->starts_at <= $startsAt && $this->ends_at >= $endsAt;
    }

    /**
     * Get the employment record this belongs to.
     *
     * @return BelongsTo<StaffProfile, $this>
     */
    public function staffProfile(): BelongsTo
    {
        return $this->belongsTo(StaffProfile::class);
    }
}
