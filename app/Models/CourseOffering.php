<?php

namespace App\Models;

use App\Enums\CourseOfferingStatus;
use App\Enums\RosterMode;
use App\Traits\InSchool;
use Database\Factories\CourseOfferingFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * A subject taught to one academic level during one exact academic period.
 *
 * An offering does not decide where individual learners are placed. Its
 * cycle sections describe the default home groups the offering teaches. Enrollment
 * and individual course rosters remain separate work.
 *
 * @property CourseOfferingStatus $status
 * @property RosterMode $roster_mode
 */
class CourseOffering extends Model
{
    /** @use HasFactory<CourseOfferingFactory> */
    use HasFactory;

    use InSchool;

    protected $fillable = [
        'school_id',
        'academic_year_id',
        'academic_period_id',
        'subject_id',
        'academic_level_id',
        'roster_mode',
        'planned_periods_per_week',
        'capacity',
    ];

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'status' => CourseOfferingStatus::Draft->value,
        'roster_mode' => RosterMode::HomeSection->value,
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'status' => CourseOfferingStatus::class,
        'roster_mode' => RosterMode::class,
        'planned_periods_per_week' => 'integer',
        'capacity' => 'integer',
    ];

    /**
     * @param  array<int, int>  $academicCycleSectionIds
     * @param  array<int, int>  $studentRecordIds
     */
    public function activeKeyForRoster(array $academicCycleSectionIds, array $studentRecordIds): string
    {
        sort($academicCycleSectionIds, SORT_NUMERIC);
        sort($studentRecordIds, SORT_NUMERIC);

        return hash('sha256', implode(':', [
            $this->school_id,
            $this->academic_period_id,
            $this->subject_id,
            $this->academic_level_id,
            $this->roster_mode->value,
            implode(',', $academicCycleSectionIds),
            implode(',', $studentRecordIds),
        ]));
    }

    /**
     * @return BelongsTo<School, $this>
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /**
     * @return BelongsTo<AcademicYear, $this>
     */
    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    /**
     * @return BelongsTo<AcademicPeriod, $this>
     */
    public function academicPeriod(): BelongsTo
    {
        return $this->belongsTo(AcademicPeriod::class);
    }

    /**
     * @return BelongsTo<Subject, $this>
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * @return BelongsTo<AcademicLevel, $this>
     */
    public function academicLevel(): BelongsTo
    {
        return $this->belongsTo(AcademicLevel::class);
    }

    /**
     * @return BelongsToMany<AcademicCycleSection, $this>
     */
    public function cycleSections(): BelongsToMany
    {
        return $this->belongsToMany(AcademicCycleSection::class, 'course_offering_cycle_sections');
    }

    /**
     * The learners who attend an individual-roster offering.
     *
     * @return BelongsToMany<StudentRecord, $this>
     */
    public function studentRecords(): BelongsToMany
    {
        return $this->belongsToMany(StudentRecord::class, 'course_offering_student_records');
    }

    /**
     * @return HasMany<TeachingAssignment, $this>
     */
    public function teachingAssignments(): HasMany
    {
        return $this->hasMany(TeachingAssignment::class);
    }
}
