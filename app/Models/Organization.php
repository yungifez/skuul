<?php

namespace App\Models;

use App\Enums\OrganizationMembershipStatus;
use App\Enums\OrganizationPermission;
use Database\Factories\OrganizationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Organization extends Model
{
    /** @use HasFactory<OrganizationFactory> */
    use HasFactory;

    /** @var list<string> */
    protected $fillable = [
        'name',
        'code',
        'address',
        'email',
        'phone',
    ];

    /**
     * Get the campuses owned by this organization.
     *
     * @return HasMany<School, $this>
     */
    public function schools(): HasMany
    {
        return $this->hasMany(School::class);
    }

    /**
     * Get organization administration records.
     *
     * @return HasMany<OrganizationMembership, $this>
     */
    public function memberships(): HasMany
    {
        return $this->hasMany(OrganizationMembership::class);
    }

    /**
     * Check whether somebody other than this person can still manage members.
     *
     * The last person who can manage members must not be removed, and must not
     * delegate that permission away, or nobody inside the organization could
     * grant it back.
     */
    public function hasAnotherMemberManager(User $except): bool
    {
        return $this->memberships()
            ->active()
            ->where('user_id', '!=', $except->id)
            ->get()
            ->contains(fn (OrganizationMembership $membership): bool => $membership->grants(OrganizationPermission::ManageMembers));
    }

    /**
     * Get people with active scope in this organization.
     *
     * @return BelongsToMany<User, $this>
     */
    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'organization_memberships')
            ->withPivot(['status', 'joined_at', 'ended_at'])
            ->wherePivot('status', OrganizationMembershipStatus::Active->value)
            ->withTimestamps();
    }
}
