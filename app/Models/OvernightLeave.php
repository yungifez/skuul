<?php

namespace App\Models;

use App\Enums\OvernightLeaveStatus;
use App\Traits\InSchool;
use Database\Factories\OvernightLeaveFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A night a learner spends away from the house, with somebody's permission.
 *
 * Staff ask one question every evening: who is not in the building tonight,
 * and does somebody know where they are? This record answers it.
 *
 * @property OvernightLeaveStatus $status
 */
class OvernightLeave extends Model
{
    /** @use HasFactory<OvernightLeaveFactory> */
    use HasFactory;

    use InSchool;

    protected $fillable = [
        'school_id',
        'student_record_id',
        'leaves_on',
        'returns_on',
        'destination',
        'contact',
        'reason',
        'status',
        'requested_by',
        'decided_by',
        'decided_at',
        'returned_at',
        'decision_note',
    ];

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'status' => OvernightLeaveStatus::Requested->value,
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'status' => OvernightLeaveStatus::class,
        'leaves_on' => 'date:Y-m-d',
        'returns_on' => 'date:Y-m-d',
        'decided_at' => 'datetime',
        'returned_at' => 'datetime',
    ];

    /**
     * Get the enrollment the leave is about.
     *
     * @return BelongsTo<StudentRecord, $this>
     */
    public function studentRecord(): BelongsTo
    {
        return $this->belongsTo(StudentRecord::class);
    }

    /**
     * Get the person who asked for the leave.
     *
     * @return BelongsTo<User, $this>
     */
    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
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

    /**
     * Limit the query to the requests nobody has answered.
     *
     * @param  Builder<$this>  $query
     * @return Builder<$this>
     */
    public function scopeWaiting(Builder $query): Builder
    {
        return $query->where('status', OvernightLeaveStatus::Requested);
    }

    /**
     * Limit the query to the learners who should be out on one night.
     *
     * @param  Builder<$this>  $query
     * @return Builder<$this>
     */
    public function scopeAwayOn(Builder $query, ?string $night = null): Builder
    {
        $night ??= now()->toDateString();

        return $query
            ->where('status', OvernightLeaveStatus::Approved)
            ->where('leaves_on', '<=', $night)
            ->where('returns_on', '>=', $night);
    }

    /**
     * Check whether the learner is out of the house tonight.
     */
    public function coversTonight(): bool
    {
        return $this->status->allowsTheLearnerOut()
            && $this->leaves_on->lessThanOrEqualTo(now())
            && $this->returns_on->greaterThanOrEqualTo(now()->startOfDay());
    }
}
