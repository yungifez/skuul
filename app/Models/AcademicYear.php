<?php

namespace App\Models;

use App\Enums\AcademicPeriodStatus;
use App\Enums\AcademicPeriodType;
use App\Traits\HasPeriodLifecycle;
use App\Traits\InSchool;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Carbon;

/**
 * @property AcademicPeriodStatus $status
 * @property string $name
 * @property int $start_year
 * @property int $stop_year
 * @property Carbon|null $starts_on
 * @property Carbon|null $ends_on
 * @property int $school_id
 */
class AcademicYear extends Model
{
    use HasFactory;
    use HasPeriodLifecycle;
    use InSchool;

    protected $appends = ['name'];

    protected $fillable = [
        'start_year',
        'stop_year',
        'starts_on',
        'ends_on',
        'school_id',
        'status',
    ];

    /**
     * The default values for a new period.
     *
     * @var array<string, string>
     */
    protected $attributes = [
        'status' => AcademicPeriodStatus::Open->value,
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'status' => AcademicPeriodStatus::class,
        'starts_on' => 'date:Y-m-d',
        'ends_on' => 'date:Y-m-d',
    ];

    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn () => "$this->start_year - $this->stop_year",
        );
    }

    /**
     * Get the school that owns the academic year.
     *
     * @return BelongsTo<School, $this>
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Get every period in the cycle, sub-periods included, in teaching order.
     *
     * @return HasMany<AcademicPeriod, $this>
     */
    public function academicPeriods(): HasMany
    {
        return $this->hasMany(AcademicPeriod::class)->orderBy('position')->orderBy('id');
    }

    /**
     * Get the periods that divide the cycle, without their sub-periods.
     *
     * This is the list a person reads as "the terms this year": an exam window
     * inside Term 2 is part of Term 2, not a fourth term.
     *
     * @return HasMany<AcademicPeriod, $this>
     */
    public function topLevelPeriods(): HasMany
    {
        return $this->academicPeriods()->whereNull('parent_id');
    }

    /**
     * Get the period that covers the given day, when one does.
     *
     * Sub-periods are skipped. A day inside an exam window is still a day of
     * the term that holds it, and the term is the reporting boundary.
     */
    public function periodForDate(DateTimeInterface|string|null $date = null): ?AcademicPeriod
    {
        return $this->topLevelPeriods()->get()->first(fn (AcademicPeriod $academicPeriod): bool => $academicPeriod->covers($date));
    }

    /**
     * Get the sub-period of the given kind that covers the day, when one does.
     *
     * Ask this to find out whether today falls in an exam window or a break.
     *
     * @param  array<int, AcademicPeriodType>  $types
     */
    public function subPeriodForDate(array $types, DateTimeInterface|string|null $date = null): ?AcademicPeriod
    {
        return $this->academicPeriods()
            ->whereNotNull('parent_id')
            ->ofType($types)
            ->covering($date)
            ->first();
    }

    /**
     * Get all of the exams for the AcademicYear.
     *
     * @return HasManyThrough<Exam, AcademicPeriod, $this>
     */
    public function exams(): HasManyThrough
    {
        return $this->hasManyThrough(Exam::class, AcademicPeriod::class, 'academic_year_id', 'academic_period_id', 'id', 'id');
    }

    /**
     * The studentRecords that belong to the AcademicYear.
     *
     * @return BelongsToMany<StudentRecord, $this, AcademicYearStudentRecord, 'studentAcademicYearBasedRecords'>
     */
    public function studentRecords(): BelongsToMany
    {
        return $this->belongsToMany(StudentRecord::class)
            ->as('studentAcademicYearBasedRecords')
            ->using(AcademicYearStudentRecord::class)
            ->withPivot('academic_cycle_section_id');
    }

    /**
     * Get the home sections that belong to this exact cycle.
     *
     * @return HasMany<AcademicCycleSection, $this>
     */
    public function cycleSections(): HasMany
    {
        return $this->hasMany(AcademicCycleSection::class)->orderBy('position')->orderBy('name');
    }
}
