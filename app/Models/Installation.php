<?php

namespace App\Models;

use App\Traits\InSchool;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Schema;

class Installation extends Model
{
    use InSchool;

    protected $fillable = [
        'lock_key',
        'installed_by',
        'organization_id',
        'school_id',
        'locale',
        'demo_data_loaded',
        'email_configured',
        'installed_at',
    ];

    protected $casts = [
        'demo_data_loaded' => 'boolean',
        'email_configured' => 'boolean',
        'installed_at' => 'datetime',
    ];

    /**
     * Check whether the one-time installer has completed.
     */
    public static function isInstalled(): bool
    {
        try {
            return Schema::hasTable('installations') && static::withoutGlobalScopes()->exists();
        } catch (\Throwable) {
            return false;
        }
    }

    /**
     * Get the account that completed installation.
     *
     * @return BelongsTo<User, $this>
     */
    public function installedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'installed_by');
    }

    /**
     * Get the organization created during installation.
     *
     * @return BelongsTo<Organization, $this>
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Get the first school created during installation.
     *
     * @return BelongsTo<School, $this>
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }
}
