<?php

namespace App\Models;

use App\Enums\RosterMode;
use App\Traits\InSchool;
use Database\Factories\InstructionalModelExceptionFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Permission for one subject to be taught outside the campus model.
 *
 * A campus whose learners normally stay together all day still runs a
 * combined music class. The exception says so in writing, for one subject and
 * one cycle, and changes nothing about how the campus teaches.
 *
 * @property RosterMode $roster_mode
 */
class InstructionalModelException extends Model
{
    /** @use HasFactory<InstructionalModelExceptionFactory> */
    use HasFactory;

    use InSchool;

    protected $fillable = [
        'school_id',
        'academic_year_id',
        'subject_id',
        'academic_level_id',
        'roster_mode',
        'reason',
        'granted_by',
        'revoked_at',
        'revoked_by',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'roster_mode' => RosterMode::class,
        'revoked_at' => 'datetime',
    ];

    /**
     * Get the cycle the exception covers.
     *
     * @return BelongsTo<AcademicYear, $this>
     */
    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    /**
     * Get the subject the exception is for.
     *
     * @return BelongsTo<Subject, $this>
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Get the level the exception is limited to, when it names one.
     *
     * @return BelongsTo<AcademicLevel, $this>
     */
    public function academicLevel(): BelongsTo
    {
        return $this->belongsTo(AcademicLevel::class);
    }

    /**
     * Get the person who allowed it.
     *
     * @return BelongsTo<User, $this>
     */
    public function grantedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'granted_by');
    }

    /**
     * Check whether the exception still stands.
     */
    public function isRunning(): bool
    {
        return $this->revoked_at === null;
    }

    /**
     * Limit the query to the exceptions that still stand.
     *
     * @param  Builder<$this>  $query
     * @return Builder<$this>
     */
    public function scopeRunning(Builder $query): Builder
    {
        return $query->whereNull('revoked_at');
    }

    /**
     * Say in words what the exception covers.
     */
    public function coverage(): string
    {
        $level = $this->academicLevel;

        return $level === null ? 'Every level' : $level->name;
    }
}
