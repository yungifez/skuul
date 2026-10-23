<?php

namespace App\Models;

use App\Enums\CourseOfferingStatus;
use App\Enums\RosterMode;
use App\Traits\InSchool;
use Database\Factories\CourseOfferingFactory;
use Illuminate\Database\Eloquent\Builder;
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
 * @property string|null $name
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
        'name',
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
     * Get the name people know the offering by.
     *
     * A school names an offering when it runs two of one subject for one
     * level, such as Algebra A and Algebra B. Otherwise the subject names it.
     */
    public function displayName(): string
    {
        return filled($this->name) ? $this->name : $this->subject->name;
    }

    /**
     * Get a name that stands alone, where no subject or level column sits beside it.
     *
     * For example "Algebra B · Mathematics · Grade 10", or "Mathematics · Grade 10".
     */
    public function qualifiedName(): string
    {
        return collect([$this->nameWithSubject(), $this->academicLevel?->name])->filter()->join(' · ');
    }

    /**
     * Get the offering's own name followed by its subject, where a level column sits beside it.
     *
     * For example "Algebra B · Mathematics", or "Mathematics".
     */
    public function nameWithSubject(): string
    {
        return collect([$this->displayName(), filled($this->name) ? $this->subject?->name : null])->filter()->join(' · ');
    }

    /**
     * Get the sections whose timetables can place this offering's lessons.
     *
     * These are the sections the roster names, the sections of the level and
     * the levels under it, or the home sections of the named learners.
     *
     * @return array<int, int>
     */
    public function reachedSectionIds(): array
    {
        $ids = match ($this->roster_mode) {
            RosterMode::HomeSection, RosterMode::CombinedHomeSections => $this->cycleSections()->pluck('academic_cycle_sections.id')->all(),
            RosterMode::IndividualRoster => $this->studentRecords()->whereNotNull('academic_cycle_section_id')->pluck('academic_cycle_section_id')->all(),
            RosterMode::AcademicLevel => AcademicCycleSection::query()
                ->where('academic_year_id', $this->academic_year_id)
                ->whereIn('academic_level_id', $this->academicLevel->teachingScopeIds())
                ->pluck('id')
                ->all(),
        };

        return array_values(array_unique(array_map('intval', $ids)));
    }

    /**
     * Limit the query to the offerings a timetable teaches.
     *
     * An offering's own timetable teaches that offering. A section's
     * timetable teaches every offering whose roster reaches the section. A
     * schoolwide timetable teaches no one offering.
     *
     * @param  Builder<$this>  $query
     * @return Builder<$this>
     */
    public function scopeTaughtOn(Builder $query, Timetable $timetable): Builder
    {
        if ($timetable->course_offering_id !== null) {
            return $query->whereKey($timetable->course_offering_id);
        }

        $section = $timetable->academicCycleSection;

        return $section === null ? $query->whereKey([]) : $query->reachingSection($section);
    }

    /**
     * Limit the query to the offerings whose roster reaches a section.
     *
     * @param  Builder<$this>  $query
     * @return Builder<$this>
     */
    public function scopeReachingSection(Builder $query, AcademicCycleSection $section): Builder
    {
        $section->loadMissing('academicLevel');
        $levelIds = $section->academicLevel?->hierarchyIds() ?? [$section->academic_level_id];

        return $query->where(fn (Builder $offerings): Builder => $offerings
            ->where(fn (Builder $bySection): Builder => $bySection
                ->whereIn('roster_mode', [RosterMode::HomeSection, RosterMode::CombinedHomeSections])
                ->whereHas('cycleSections', fn (Builder $sections): Builder => $sections->whereKey($section->id)))
            ->orWhere(fn (Builder $byLevel): Builder => $byLevel
                ->where('roster_mode', RosterMode::AcademicLevel)
                ->whereIn('academic_level_id', $levelIds))
            ->orWhere(fn (Builder $named): Builder => $named
                ->where('roster_mode', RosterMode::IndividualRoster)
                ->whereHas('studentRecords', fn (Builder $learners): Builder => $learners->where('academic_cycle_section_id', $section->id))));
    }

    /**
     * Limit the query to the offerings a learner takes.
     *
     * This reads the same roster rules as the gradebook: the learner's home
     * section, their level and the groups above it, or their name on the
     * roster.
     *
     * @param  Builder<$this>  $query
     * @return Builder<$this>
     */
    public function scopeTakenBy(Builder $query, StudentRecord $learner): Builder
    {
        $section = $learner->academicCycleSection;
        $levelIds = $section?->academicLevel?->hierarchyIds() ?? [];

        return $query
            ->where('school_id', $learner->school_id)
            ->where(fn (Builder $offerings): Builder => $offerings
                ->where(fn (Builder $named): Builder => $named
                    ->where('roster_mode', RosterMode::IndividualRoster)
                    ->whereHas('studentRecords', fn (Builder $learners): Builder => $learners->whereKey($learner->id)))
                ->when($section !== null, fn (Builder $bySection): Builder => $bySection->orWhere(fn (Builder $home): Builder => $home
                    ->whereIn('roster_mode', [RosterMode::HomeSection, RosterMode::CombinedHomeSections])
                    ->whereHas('cycleSections', fn (Builder $sections): Builder => $sections->whereKey($section?->id))))
                ->when($levelIds !== [], fn (Builder $byLevel): Builder => $byLevel->orWhere(fn (Builder $level): Builder => $level
                    ->where('roster_mode', RosterMode::AcademicLevel)
                    ->whereIn('academic_level_id', $levelIds))));
    }

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
     * The timetables that hold this offering's own meeting times.
     *
     * @return HasMany<Timetable, $this>
     */
    public function timetables(): HasMany
    {
        return $this->hasMany(Timetable::class);
    }

    /**
     * @return HasMany<TeachingAssignment, $this>
     */
    public function teachingAssignments(): HasMany
    {
        return $this->hasMany(TeachingAssignment::class);
    }

    /**
     * @return HasMany<Syllabus, $this>
     */
    public function syllabi(): HasMany
    {
        return $this->hasMany(Syllabus::class);
    }

    /**
     * @return HasMany<GradeCategory, $this>
     */
    public function gradeCategories(): HasMany
    {
        return $this->hasMany(GradeCategory::class);
    }

    /**
     * @return HasMany<GradeItem, $this>
     */
    public function gradeItems(): HasMany
    {
        return $this->hasMany(GradeItem::class);
    }

    /**
     * @return HasMany<ResultSnapshot, $this>
     */
    public function resultSnapshots(): HasMany
    {
        return $this->hasMany(ResultSnapshot::class);
    }
}
