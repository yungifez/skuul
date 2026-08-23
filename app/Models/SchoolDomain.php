<?php

namespace App\Models;

use Database\Factories\SchoolDomainFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * One web address an organization answers on.
 *
 * This model has no `school_id` scope of its own on purpose: a domain is read
 * before anybody has signed in, which is exactly when there is no working
 * school yet. It is a hint about which organization the visitor meant, never
 * proof of what they may see.
 *
 * @property Carbon|null $verified_at
 */
class SchoolDomain extends Model
{
    /** @use HasFactory<SchoolDomainFactory> */
    use HasFactory;

    /** @var list<string> */
    protected $fillable = [
        'organization_id',
        'school_id',
        'host',
        'is_primary',
        'verification_token',
        'verified_at',
        'created_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_primary' => 'boolean',
        'verified_at' => 'datetime',
    ];

    /**
     * Get the organization this address belongs to.
     *
     * @return BelongsTo<Organization, $this>
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Get the campus this address opens, if it names one.
     *
     * @return BelongsTo<School, $this>
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Get the person who added the address.
     *
     * @return BelongsTo<User, $this>
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Limit the query to addresses the organization has proved it owns.
     */
    public function scopeVerified(Builder $query): void
    {
        $query->whereNotNull('verified_at');
    }

    /**
     * Find the proved address of one host.
     */
    public static function forHost(string $host): ?self
    {
        return self::query()->verified()->where('host', self::tidy($host))->first();
    }

    /**
     * Check whether the organization has proved it owns this address.
     */
    public function isVerified(): bool
    {
        return $this->verified_at !== null;
    }

    /**
     * Get the name of the DNS record that proves ownership.
     */
    public function verificationRecord(): string
    {
        return '_skuul-verification.'.$this->host;
    }

    /**
     * Write a host the one way it is stored: lower case, no port, no trailing dot.
     */
    public static function tidy(string $host): string
    {
        $host = strtolower(trim($host));
        $host = (string) preg_replace('/:\d+$/', '', $host);

        return trim($host, '.');
    }
}
