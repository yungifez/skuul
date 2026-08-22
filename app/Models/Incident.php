<?php

namespace App\Models;

use App\Enums\IncidentCategory;
use App\Enums\IncidentStatus;
use App\Traits\InSchool;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * One recorded case: a behaviour record or a safeguarding concern.
 *
 * @property IncidentCategory $category
 * @property IncidentStatus $status
 * @property bool $is_restricted
 */
class Incident extends Model
{
    use HasFactory;
    use InSchool;

    protected $fillable = [
        'school_id',
        'reference',
        'category',
        'status',
        'is_restricted',
        'summary',
        'description',
        'location',
        'occurred_at',
        'academic_year_id',
        'academic_period_id',
        'reported_by',
        'assigned_to',
    ];

    /**
     * The default values for a new case.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'category' => IncidentCategory::Behaviour->value,
        'status' => IncidentStatus::Reported->value,
        'is_restricted' => false,
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'category' => IncidentCategory::class,
        'status' => IncidentStatus::class,
        'is_restricted' => 'boolean',
        'occurred_at' => 'datetime',
    ];

    /**
     * A safeguarding case is restricted, whoever writes it.
     */
    protected static function booted(): void
    {
        static::saving(function (self $incident): void {
            if ($incident->category->isRestricted()) {
                $incident->is_restricted = true;
            }
        });
    }

    /**
     * Limit the query to the cases a person may read.
     *
     * @param  Builder<$this>  $query
     * @return Builder<$this>
     */
    public function scopeReadableBy(Builder $query, User $user): Builder
    {
        if ($user->can('read safeguarding case')) {
            return $query;
        }

        return $query->where(function (Builder $query) use ($user): void {
            $query->where('is_restricted', false)
                ->orWhere('assigned_to', $user->id)
                ->orWhere('reported_by', $user->id);
        });
    }

    /**
     * Limit the query to the cases that still need work.
     *
     * @param  Builder<$this>  $query
     * @return Builder<$this>
     */
    public function scopeOpen(Builder $query): Builder
    {
        return $query->whereIn('status', [
            IncidentStatus::Reported,
            IncidentStatus::UnderReview,
            IncidentStatus::Referred,
            IncidentStatus::ActionTaken,
        ]);
    }

    /**
     * Get the people named in the case.
     *
     * @return HasMany<IncidentParticipant, $this>
     */
    public function participants(): HasMany
    {
        return $this->hasMany(IncidentParticipant::class);
    }

    /**
     * Get what the school did about the case.
     *
     * @return HasMany<IncidentAction, $this>
     */
    public function actions(): HasMany
    {
        return $this->hasMany(IncidentAction::class)->orderBy('id');
    }

    /**
     * Get how the case moved between states.
     *
     * @return HasMany<IncidentStatusChange, $this>
     */
    public function statusChanges(): HasMany
    {
        return $this->hasMany(IncidentStatusChange::class)->orderBy('id');
    }

    /**
     * Get the person who wrote the case down.
     *
     * @return BelongsTo<User, $this>
     */
    public function reportedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    /**
     * Get the person handling the case.
     *
     * @return BelongsTo<User, $this>
     */
    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
