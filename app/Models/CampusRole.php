<?php

namespace App\Models;

use App\Enums\Role as BuiltInRole;
use App\Traits\InSchool;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Spatie\Permission\Models\Role as SpatieRole;

/**
 * A role as this application treats it: a named set of permissions a campus
 * owns.
 *
 * Business rules never read a role name. A role is a convenient way to hand
 * out permissions, so a campus can invent Registrar or Finance Officer without
 * anything in the application knowing what those words mean.
 *
 * @property Carbon|null $archived_at
 * @property string|null $description
 * @property int|null $school_id
 */
class CampusRole extends SpatieRole
{
    use InSchool;

    /**
     * Limit the query to roles a campus still offers.
     *
     * @param  Builder<$this>  $query
     * @return Builder<$this>
     */
    public function scopeInUse(Builder $query): Builder
    {
        return $query->whereNull('archived_at');
    }

    /**
     * Get the campus that owns this role.
     *
     * @return BelongsTo<School, $this>
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Check whether the application itself relies on this role.
     *
     * The built-in roles decide who can sign in as what, and the platform
     * roles carry authority above any one campus. A campus may hand them out,
     * but it may not rewrite or retire them.
     */
    public function isBuiltIn(): bool
    {
        return BuiltInRole::tryFrom($this->name) !== null;
    }

    /**
     * Check whether the campus has stopped offering this role.
     */
    public function isArchived(): bool
    {
        return $this->archived_at !== null;
    }
}
