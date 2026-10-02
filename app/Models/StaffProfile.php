<?php

namespace App\Models;

use App\Enums\EmploymentType;
use App\Enums\LeaveStatus;
use App\Enums\StaffStatus;
use App\Traits\InSchool;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * One employment record: who works here and in what job.
 *
 * A person can work in more than one school, so the profile belongs to a
 * school and not to the account.
 *
 * @property EmploymentType $employment_type
 * @property StaffStatus $status
 */
class StaffProfile extends Model
{
    use HasFactory;
    use InSchool;

    protected $fillable = [
        'school_id',
        'user_id',
        'staff_number',
        'job_title',
        'department',
        'employment_type',
        'status',
        'joined_on',
        'left_on',
    ];

    /**
     * The default values for a new profile.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'employment_type' => EmploymentType::FullTime->value,
        'status' => StaffStatus::Active->value,
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'employment_type' => EmploymentType::class,
        'status' => StaffStatus::class,
        'joined_on' => 'date',
        'left_on' => 'date',
    ];

    /**
     * Limit the query to the people who may be given work.
     *
     * @param  Builder<$this>  $query
     * @return Builder<$this>
     */
    public function scopeEmployed(Builder $query): Builder
    {
        return $query->where('status', StaffStatus::Active);
    }

    /**
     * Limit the query to the people away on the given day.
     *
     * @param  Builder<$this>  $query
     * @return Builder<$this>
     */
    public function scopeAwayOn(Builder $query, mixed $date): Builder
    {
        $day = Carbon::parse($date)->toDateString();

        return $query->whereHas('leaveRequests', function (Builder $query) use ($day): void {
            $query->whereIn('status', [LeaveStatus::Approved, LeaveStatus::Taken])
                ->whereDate('starts_on', '<=', $day)
                ->whereDate('ends_on', '>=', $day);
        });
    }

    /**
     * Get the account this profile belongs to.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get what the person is qualified for.
     *
     * @return HasMany<StaffCredential, $this>
     */
    public function credentials(): HasMany
    {
        return $this->hasMany(StaffCredential::class)->orderBy('id');
    }

    /**
     * Get the hours the person can work.
     *
     * @return HasMany<StaffAvailability, $this>
     */
    public function availabilities(): HasMany
    {
        return $this->hasMany(StaffAvailability::class)->orderBy('day_of_week')->orderBy('starts_at');
    }

    /**
     * Get the times the person asked to be away.
     *
     * @return HasMany<StaffLeaveRequest, $this>
     */
    public function leaveRequests(): HasMany
    {
        return $this->hasMany(StaffLeaveRequest::class)->orderBy('starts_on');
    }
}
