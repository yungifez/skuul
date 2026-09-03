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

    /** @return HasMany<BoardingResidence, $this> */
    public function boardingResidences(): HasMany
    {
        return $this->hasMany(BoardingResidence::class);
    }

    /**
     * Get the web addresses this organization answers on.
     *
     * @return HasMany<SchoolDomain, $this>
     */
    public function domains(): HasMany
    {
        return $this->hasMany(SchoolDomain::class);
    }

    /**
     * Get the purses this organization has set up.
     *
     * @return HasMany<BillingGroup, $this>
     */
    public function billingGroups(): HasMany
    {
        return $this->hasMany(BillingGroup::class);
    }

    /**
     * Get the calendar templates this organization defines.
     *
     * @return HasMany<CalendarTemplate, $this>
     */
    public function calendarTemplates(): HasMany
    {
        return $this->hasMany(CalendarTemplate::class);
    }

    /**
     * Get the template a campus follows when it has not chosen one.
     */
    public function defaultCalendarTemplate(): ?CalendarTemplate
    {
        return $this->calendarTemplates()->where('is_default', true)->first();
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
