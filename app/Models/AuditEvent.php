<?php

namespace App\Models;

use App\Enums\AuditAction;
use App\Traits\InSchool;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use RuntimeException;

/**
 * One recorded sensitive action.
 *
 * The record is written once. It is never changed and never deleted, so the
 * log stays a trustworthy answer to "who did this, and when".
 *
 * @property AuditAction $action
 * @property array<string, mixed> $context
 */
class AuditEvent extends Model
{
    use HasFactory;
    use InSchool;

    /**
     * The log only stores when the action happened.
     */
    public const UPDATED_AT = null;

    protected $fillable = [
        'school_id',
        'actor_id',
        'action',
        'subject_type',
        'subject_id',
        'context',
        'ip_address',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'action' => AuditAction::class,
        'context' => 'array',
        'created_at' => 'datetime',
    ];

    /**
     * Keep the log append-only.
     */
    protected static function booted(): void
    {
        static::updating(function (): void {
            throw new RuntimeException('Audit records cannot be changed.');
        });

        static::deleting(function (): void {
            throw new RuntimeException('Audit records cannot be deleted.');
        });
    }

    /**
     * Get the person who made the change.
     *
     * @return BelongsTo<User, $this>
     */
    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }

    /**
     * Get the record the action was made on.
     *
     * @return MorphTo<Model, $this>
     */
    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the school the action belongs to.
     *
     * @return BelongsTo<School, $this>
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Limit the query to one action.
     *
     * @param  Builder<$this>  $query
     * @return Builder<$this>
     */
    public function scopeOfAction(Builder $query, AuditAction $action): Builder
    {
        return $query->where('action', $action);
    }

    /**
     * Limit the query to the records of one subject.
     *
     * @param  Builder<$this>  $query
     * @return Builder<$this>
     */
    public function scopeForSubject(Builder $query, Model $subject): Builder
    {
        return $query->where('subject_type', $subject->getMorphClass())
            ->where('subject_id', $subject->getKey());
    }
}
