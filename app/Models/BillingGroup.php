<?php

namespace App\Models;

use Database\Factories\BillingGroupFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * A set of campuses that keep one purse.
 *
 * A family owing money at one campus of the group owes it at every campus of
 * the group, so a learner who moves does not leave a debt behind. Campuses
 * outside the group keep their own books.
 */
class BillingGroup extends Model
{
    /** @use HasFactory<BillingGroupFactory> */
    use HasFactory;

    /** @var list<string> */
    protected $fillable = [
        'organization_id',
        'name',
    ];

    /**
     * Get the organization that owns this group.
     *
     * @return BelongsTo<Organization, $this>
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Get the campuses in this group.
     *
     * @return HasMany<School, $this>
     */
    public function schools(): HasMany
    {
        return $this->hasMany(School::class);
    }
}
