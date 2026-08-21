<?php

namespace App\Models;

use App\Enums\SchoolMembershipStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * A person's access to one school.
 *
 * Membership carries school access. It does not carry the account state and it
 * does not carry student enrollment. Ending a membership keeps the record.
 *
 * @property int $id
 * @property int $user_id
 * @property int $school_id
 * @property SchoolMembershipStatus $status
 * @property bool $is_primary
 * @property Carbon|null $joined_at
 * @property Carbon|null $ended_at
 */
class SchoolMembership extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'school_id',
        'status',
        'is_primary',
        'joined_at',
        'ended_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'status' => SchoolMembershipStatus::class,
        'is_primary' => 'boolean',
        'joined_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    /**
     * Get the person this membership belongs to.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the school this membership grants access to.
     *
     * @return BelongsTo<School, $this>
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Limit the query to memberships that grant access.
     *
     * @param  Builder<SchoolMembership>  $query
     * @return Builder<SchoolMembership>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', SchoolMembershipStatus::Active);
    }

    /**
     * Limit the query to the membership used for organization-level work.
     *
     * @param  Builder<SchoolMembership>  $query
     * @return Builder<SchoolMembership>
     */
    public function scopePrimary(Builder $query): Builder
    {
        return $query->where('is_primary', true);
    }

    /**
     * Check if this membership grants access to its school.
     */
    public function grantsAccess(): bool
    {
        return $this->status->grantsAccess();
    }
}
