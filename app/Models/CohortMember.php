<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * One place in a cohort, for an enrollment or for a person.
 *
 * A student joins through their enrollment, because a cohort belongs to a
 * school. Staff and guardians join as themselves.
 */
class CohortMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'cohort_id',
        'student_record_id',
        'user_id',
        'joined_on',
        'left_on',
        'added_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'joined_on' => 'date',
        'left_on'   => 'date',
    ];

    /**
     * Check if the place is still held on the given day.
     *
     * `left_on` is the day the person stopped, so the place is not held on
     * that day.
     */
    public function isHeldOn(mixed $date = null): bool
    {
        $day = Carbon::parse($date ?? now())->startOfDay();

        if ($this->joined_on !== null && $this->joined_on->gt($day)) {
            return false;
        }

        return $this->left_on === null || $this->left_on->gt($day);
    }

    /**
     * Limit the query to the places held today.
     *
     * @param Builder<$this> $query
     *
     * @return Builder<$this>
     */
    public function scopeCurrent(Builder $query): Builder
    {
        return $query->whereNull('left_on');
    }

    /**
     * Get the group this place is in.
     *
     * @return BelongsTo<Cohort, $this>
     */
    public function cohort(): BelongsTo
    {
        return $this->belongsTo(Cohort::class);
    }

    /**
     * Get the enrollment in the group, when the member is a student.
     *
     * @return BelongsTo<StudentRecord, $this>
     */
    public function studentRecord(): BelongsTo
    {
        return $this->belongsTo(StudentRecord::class);
    }

    /**
     * Get the person in the group, when the member is not a student.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
