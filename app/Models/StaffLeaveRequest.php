<?php

namespace App\Models;

use App\Enums\LeaveStatus;
use App\Enums\LeaveType;
use App\Traits\InSchool;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * One time a member of staff asked to be away.
 *
 * @property LeaveType   $type
 * @property LeaveStatus $status
 */
class StaffLeaveRequest extends Model
{
    use HasFactory;
    use InSchool;

    protected $fillable = [
        'school_id',
        'staff_profile_id',
        'type',
        'status',
        'starts_on',
        'ends_on',
        'reason',
        'requested_by',
        'decided_by',
        'decided_at',
        'decision_note',
    ];

    /**
     * The default values for a new request.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'type'   => LeaveType::Annual->value,
        'status' => LeaveStatus::Requested->value,
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'type'       => LeaveType::class,
        'status'     => LeaveStatus::class,
        'starts_on'  => 'date',
        'ends_on'    => 'date',
        'decided_at' => 'datetime',
    ];

    /**
     * Count the days asked for, both ends included.
     */
    public function days(): int
    {
        return (int) $this->starts_on->diffInDays($this->ends_on) + 1;
    }

    /**
     * Check if the leave covers the given day.
     */
    public function covers(mixed $date): bool
    {
        $day = Carbon::parse($date)->startOfDay();

        return $this->starts_on->lte($day) && $this->ends_on->gte($day);
    }

    /**
     * Limit the query to the requests that still block the days.
     *
     * @param Builder<$this> $query
     *
     * @return Builder<$this>
     */
    public function scopeHolding(Builder $query): Builder
    {
        return $query->whereIn('status', [LeaveStatus::Requested, LeaveStatus::Approved, LeaveStatus::Taken]);
    }

    /**
     * Limit the query to the requests that touch the given days.
     *
     * @param Builder<$this> $query
     *
     * @return Builder<$this>
     */
    public function scopeOverlapping(Builder $query, mixed $startsOn, mixed $endsOn): Builder
    {
        return $query->whereDate('starts_on', '<=', Carbon::parse($endsOn)->toDateString())
            ->whereDate('ends_on', '>=', Carbon::parse($startsOn)->toDateString());
    }

    /**
     * Get the employment record this belongs to.
     *
     * @return BelongsTo<StaffProfile, $this>
     */
    public function staffProfile(): BelongsTo
    {
        return $this->belongsTo(StaffProfile::class);
    }

    /**
     * Get how the request moved between states.
     *
     * @return HasMany<StaffLeaveStatusChange, $this>
     */
    public function statusChanges(): HasMany
    {
        return $this->hasMany(StaffLeaveStatusChange::class)->orderBy('id');
    }

    /**
     * Get the person who answered the request.
     *
     * @return BelongsTo<User, $this>
     */
    public function decidedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'decided_by');
    }
}
