<?php

namespace App\Models;

use App\Enums\OrganizationMembershipStatus;
use App\Enums\OrganizationPermission;
use Database\Factories\OrganizationMembershipFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int                          $organization_id
 * @property int                          $user_id
 * @property OrganizationMembershipStatus $status
 * @property list<string>|null            $permissions
 */
class OrganizationMembership extends Model
{
    /** @use HasFactory<OrganizationMembershipFactory> */
    use HasFactory;

    /** @var list<string> */
    protected $fillable = [
        'organization_id',
        'user_id',
        'status',
        'permissions',
        'joined_at',
        'ended_at',
    ];

    protected function casts(): array
    {
        return [
            'status'      => OrganizationMembershipStatus::class,
            'permissions' => 'array',
            'joined_at'   => 'datetime',
            'ended_at'    => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<Organization, $this>
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @param Builder<OrganizationMembership> $query
     *
     * @return Builder<OrganizationMembership>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', OrganizationMembershipStatus::Active);
    }

    /**
     * Check whether this membership holds every organization permission.
     */
    public function hasFullAuthority(): bool
    {
        return $this->permissions === null;
    }

    /**
     * Get the permissions this membership carries.
     *
     * A membership with no stored list holds all of them.
     *
     * @return list<OrganizationPermission>
     */
    public function grantedPermissions(): array
    {
        if ($this->hasFullAuthority()) {
            return OrganizationPermission::all();
        }

        return array_values(array_filter(array_map(
            fn (string $permission): ?OrganizationPermission => OrganizationPermission::tryFrom($permission),
            $this->permissions ?? [],
        )));
    }

    /**
     * Check whether this membership carries one permission.
     *
     * A membership that no longer grants access carries nothing, so an ended
     * or suspended record cannot be used to act on the organization.
     */
    public function grants(OrganizationPermission $permission): bool
    {
        if (!$this->status->grantsAccess()) {
            return false;
        }

        return in_array($permission, $this->grantedPermissions(), true);
    }
}
