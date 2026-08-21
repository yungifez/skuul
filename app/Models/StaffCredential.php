<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * One qualification, certificate, or licence a member of staff holds.
 */
class StaffCredential extends Model
{
    use HasFactory;

    protected $fillable = [
        'staff_profile_id',
        'type',
        'name',
        'issuer',
        'reference',
        'issued_on',
        'expires_on',
        'verified_at',
        'verified_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'issued_on'   => 'date',
        'expires_on'  => 'date',
        'verified_at' => 'datetime',
    ];

    /**
     * Check if the school has seen the paper.
     */
    public function isVerified(): bool
    {
        return $this->verified_at !== null;
    }

    /**
     * Check if it has run out.
     */
    public function hasExpired(mixed $on = null): bool
    {
        if ($this->expires_on === null) {
            return false;
        }

        return $this->expires_on->lt(Carbon::parse($on ?? now())->startOfDay());
    }

    /**
     * Limit the query to the papers that run out before the given day.
     *
     * @param Builder<$this> $query
     *
     * @return Builder<$this>
     */
    public function scopeExpiringBefore(Builder $query, mixed $date): Builder
    {
        return $query->whereNotNull('expires_on')->whereDate('expires_on', '<=', Carbon::parse($date)->toDateString());
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
