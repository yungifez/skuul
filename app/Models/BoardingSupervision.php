<?php

namespace App\Models;

use App\Enums\SupervisionRole;
use App\Traits\InSchool;
use Database\Factories\BoardingSupervisionFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A member of staff on duty in a boarding house, between two dates.
 *
 * Last term's rota is kept, so a school can still answer who was on duty on
 * the night something happened.
 *
 * @property SupervisionRole $role
 */
class BoardingSupervision extends Model
{
    /** @use HasFactory<BoardingSupervisionFactory> */
    use HasFactory;

    use InSchool;

    protected $fillable = [
        'school_id',
        'dormitory_id',
        'user_id',
        'role',
        'starts_on',
        'ends_on',
        'assigned_by',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'role' => SupervisionRole::class,
        'starts_on' => 'date:Y-m-d',
        'ends_on' => 'date:Y-m-d',
    ];

    /**
     * Get the house the duty is in.
     *
     * @return BelongsTo<Dormitory, $this>
     */
    public function dormitory(): BelongsTo
    {
        return $this->belongsTo(Dormitory::class);
    }

    /**
     * Get the member of staff on duty.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the person who set the rota.
     *
     * @return BelongsTo<User, $this>
     */
    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    /**
     * Limit the query to the duties running on one day.
     *
     * @param  Builder<$this>  $query
     * @return Builder<$this>
     */
    public function scopeOnDuty(Builder $query, ?string $onDate = null): Builder
    {
        $onDate ??= now()->toDateString();

        return $query
            ->where('starts_on', '<=', $onDate)
            ->where(function (Builder $open) use ($onDate): void {
                $open->whereNull('ends_on')->orWhere('ends_on', '>=', $onDate);
            });
    }
}
