<?php

namespace App\Models;

use App\Enums\AccountInvitationStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * A one-time link that lets a provisioned person set a password and sign in.
 *
 * The plain token never reaches the database. Only its hash is stored.
 *
 * @property int         $id
 * @property int         $user_id
 * @property int|null    $invited_by
 * @property string      $token_hash
 * @property Carbon      $expires_at
 * @property Carbon|null $accepted_at
 * @property Carbon|null $revoked_at
 * @property-read User|null $invitedBy
 */
class AccountInvitation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'invited_by',
        'token_hash',
        'expires_at',
        'accepted_at',
        'revoked_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'expires_at'  => 'datetime',
            'accepted_at' => 'datetime',
            'revoked_at'  => 'datetime',
        ];
    }

    /**
     * Get the account this invitation belongs to.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the administrator who sent the invitation.
     *
     * @return BelongsTo<User, $this>
     */
    public function invitedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_by');
    }

    /**
     * Limit the query to invitations that a person can still accept.
     *
     * @param Builder<AccountInvitation> $query
     *
     * @return Builder<AccountInvitation>
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->whereNull('accepted_at')
            ->whereNull('revoked_at')
            ->where('expires_at', '>', now());
    }

    /**
     * Limit the query to invitations the person already used.
     *
     * @param Builder<AccountInvitation> $query
     *
     * @return Builder<AccountInvitation>
     */
    public function scopeAccepted(Builder $query): Builder
    {
        return $query->whereNotNull('accepted_at');
    }

    /**
     * Limit the query to invitations an administrator stopped.
     *
     * @param Builder<AccountInvitation> $query
     *
     * @return Builder<AccountInvitation>
     */
    public function scopeRevoked(Builder $query): Builder
    {
        return $query->whereNull('accepted_at')->whereNotNull('revoked_at');
    }

    /**
     * Limit the query to invitations that ran out of time unused.
     *
     * @param Builder<AccountInvitation> $query
     *
     * @return Builder<AccountInvitation>
     */
    public function scopeExpired(Builder $query): Builder
    {
        return $query->whereNull('accepted_at')
            ->whereNull('revoked_at')
            ->where('expires_at', '<=', now());
    }

    /**
     * Limit the query to one state.
     *
     * @param Builder<AccountInvitation> $query
     *
     * @return Builder<AccountInvitation>
     */
    public function scopeWithStatus(Builder $query, AccountInvitationStatus $status): Builder
    {
        return match ($status) {
            AccountInvitationStatus::Pending  => $query->pending(),
            AccountInvitationStatus::Accepted => $query->accepted(),
            AccountInvitationStatus::Expired  => $query->expired(),
            AccountInvitationStatus::Revoked  => $query->revoked(),
        };
    }

    /**
     * Hash a plain token for storage and lookup.
     */
    public static function hashToken(string $token): string
    {
        return hash('sha256', $token);
    }

    /**
     * Check if the person can still accept this invitation.
     */
    public function isPending(): bool
    {
        return $this->accepted_at === null
            && $this->revoked_at === null
            && $this->expires_at->isFuture();
    }

    /**
     * Check if the invitation passed its expiry time.
     */
    public function isExpired(): bool
    {
        return $this->accepted_at === null
            && $this->revoked_at === null
            && $this->expires_at->isPast();
    }

    /**
     * Get the one state this invitation is in.
     */
    public function status(): AccountInvitationStatus
    {
        if ($this->accepted_at !== null) {
            return AccountInvitationStatus::Accepted;
        }

        if ($this->revoked_at !== null) {
            return AccountInvitationStatus::Revoked;
        }

        return $this->expires_at->isPast()
            ? AccountInvitationStatus::Expired
            : AccountInvitationStatus::Pending;
    }

    /**
     * Get the time the invitation stops working.
     */
    public function expiresAt(): Carbon
    {
        return $this->expires_at;
    }
}
