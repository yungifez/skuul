<?php

namespace App\Models;

use App\Enums\TeachingRole;
use App\Traits\InSchool;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * One teacher teaching one subject for a period.
 *
 * The assignment names the period it belongs to, so a teacher who taught a
 * subject last year still shows in last year's records. It ends by taking an
 * end date, never by being deleted.
 *
 * @property TeachingRole $role
 * @property Carbon $starts_on
 * @property Carbon|null $ends_on
 * @property int|null $academic_cycle_section_id
 */
class TeachingAssignment extends Model
{
    use HasFactory;
    use InSchool;

    protected $fillable = [
        'school_id',
        'subject_id',
        'user_id',
        'academic_year_id',
        'academic_period_id',
        'course_offering_id',
        'academic_cycle_section_id',
        'role',
        'starts_on',
        'ends_on',
    ];

    /**
     * The default values for a new assignment.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'role' => TeachingRole::Lead->value,
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'role' => TeachingRole::class,
        'starts_on' => 'date:Y-m-d',
        'ends_on' => 'date:Y-m-d',
    ];

    /**
     * Limit the query to assignments that run on the given day.
     *
     * @param  Builder<$this>  $query
     * @return Builder<$this>
     */
    public function scopeRunningOn(Builder $query, DateTimeInterface|string|null $date = null): Builder
    {
        $day = Carbon::parse($date ?? now())->toDateString();

        return $query->where('starts_on', '<=', $day)
            ->where(function (Builder $query) use ($day): void {
                $query->whereNull('ends_on')->orWhere('ends_on', '>=', $day);
            });
    }

    /**
     * Limit the query to the assignments of one teacher.
     *
     * @param  Builder<$this>  $query
     * @return Builder<$this>
     */
    public function scopeForTeacher(Builder $query, User|int $teacher): Builder
    {
        return $query->where('user_id', $teacher instanceof User ? $teacher->id : $teacher);
    }

    /**
     * Check if the assignment still runs on the given day.
     */
    public function isRunningOn(DateTimeInterface|string|null $date = null): bool
    {
        $day = Carbon::parse($date ?? now())->startOfDay();

        return $this->starts_on->startOfDay()->lessThanOrEqualTo($day)
            && ($this->ends_on === null || $this->ends_on->startOfDay()->greaterThanOrEqualTo($day));
    }

    /**
     * Get the subject that is taught.
     *
     * @return BelongsTo<Subject, $this>
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Get the teacher.
     *
     * @return BelongsTo<User, $this>
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the academic year the assignment belongs to.
     *
     * @return BelongsTo<AcademicYear, $this>
     */
    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    /**
     * Get the period the assignment belongs to, when it names one.
     *
     * @return BelongsTo<AcademicPeriod, $this>
     */
    public function academicPeriod(): BelongsTo
    {
        return $this->belongsTo(AcademicPeriod::class);
    }

    /**
     * Get the course offering this assignment belongs to, when it was made
     * through the period-specific offering workflow.
     *
     * @return BelongsTo<CourseOffering, $this>
     */
    public function courseOffering(): BelongsTo
    {
        return $this->belongsTo(CourseOffering::class);
    }

    /**
     * Get the exact home group the assignment covers, when it names one.
     *
     * @return BelongsTo<AcademicCycleSection, $this>
     */
    public function academicCycleSection(): BelongsTo
    {
        return $this->belongsTo(AcademicCycleSection::class);
    }

    /**
     * Get the school the assignment belongs to.
     *
     * @return BelongsTo<School, $this>
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }
}
