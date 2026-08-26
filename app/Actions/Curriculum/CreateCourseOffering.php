<?php

namespace App\Actions\Curriculum;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AcademicPeriodType;
use App\Enums\AuditAction;
use App\Enums\RosterMode;
use App\Exceptions\InvalidValueException;
use App\Models\AcademicCycleSection;
use App\Models\AcademicLevel;
use App\Models\AcademicPeriod;
use App\Models\AcademicYear;
use App\Models\CourseOffering;
use App\Models\StudentRecord;
use App\Models\Subject;
use App\Models\User;
use App\Services\Curriculum\OfferingExceptions;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Create the period-specific instance of a subject.
 *
 * This action creates the dated operational record without changing enrollment
 * or a learner's current placement.
 */
class CreateCourseOffering
{
    public function __construct(
        private RecordAuditEvent $auditor,
        private OfferingExceptions $exceptions,
    ) {}

    /**
     * @param  array<int, int>  $academicCycleSectionIds
     * @param  array<int, int>  $studentRecordIds
     *
     * @throws InvalidValueException when the records do not share one school, cycle, and academic level
     */
    public function create(
        Subject $subject,
        AcademicYear $academicYear,
        AcademicPeriod $academicPeriod,
        AcademicLevel $academicLevel,
        array $academicCycleSectionIds = [],
        RosterMode $rosterMode = RosterMode::HomeSection,
        array $studentRecordIds = [],
        ?int $plannedPeriodsPerWeek = null,
        ?int $capacity = null,
        ?User $actor = null,
    ): CourseOffering {
        $academicCycleSectionIds = array_values(array_unique(array_map('intval', $academicCycleSectionIds)));
        $studentRecordIds = array_values(array_unique(array_map('intval', $studentRecordIds)));
        $cycleSections = AcademicCycleSection::inSchool()
            ->whereKey($academicCycleSectionIds)
            ->where('academic_year_id', $academicYear->id)
            ->where('academic_level_id', $academicLevel->id)
            ->get();
        $studentRecords = StudentRecord::inSchool()
            ->attending()
            ->whereHas('academicCycleSection', function ($query) use ($academicYear, $academicLevel): void {
                $query->where('academic_year_id', $academicYear->id)
                    ->where('academic_level_id', $academicLevel->id);
            })
            ->whereKey($studentRecordIds)
            ->get();

        $this->failIfRecordsDoNotFit($subject, $academicYear, $academicPeriod, $academicLevel, $rosterMode, $cycleSections, $academicCycleSectionIds, $studentRecords, $studentRecordIds);

        return DB::transaction(function () use ($subject, $academicYear, $academicPeriod, $academicLevel, $rosterMode, $cycleSections, $studentRecords, $plannedPeriodsPerWeek, $capacity, $actor): CourseOffering {
            $courseOffering = CourseOffering::create([
                'school_id' => $subject->school_id,
                'academic_year_id' => $academicYear->id,
                'academic_period_id' => $academicPeriod->id,
                'subject_id' => $subject->id,
                'academic_level_id' => $academicLevel->id,
                'roster_mode' => $rosterMode,
                'planned_periods_per_week' => $plannedPeriodsPerWeek,
                'capacity' => $capacity,
            ]);

            $courseOffering->cycleSections()->sync($cycleSections->modelKeys());
            $courseOffering->studentRecords()->sync($studentRecords->modelKeys());

            $this->auditor->record(
                AuditAction::CourseOfferingCreated,
                $courseOffering,
                [
                    'subject_id' => $subject->id,
                    'academic_year_id' => $academicYear->id,
                    'academic_period_id' => $academicPeriod->id,
                    'academic_level_id' => $academicLevel->id,
                    'roster_mode' => $rosterMode->value,
                    'academic_cycle_section_ids' => $cycleSections->modelKeys(),
                    'student_record_ids' => $studentRecords->modelKeys(),
                ],
                $actor,
            );

            return $courseOffering->load(['cycleSections', 'studentRecords']);
        });
    }

    /**
     * Create the same subject offering for every top-level teaching period in a year.
     *
     * @param  array<int, int>  $academicCycleSectionIds
     * @param  array<int, int>  $studentRecordIds
     * @return Collection<int, CourseOffering>
     *
     * @throws InvalidValueException when the year has no teaching periods or one period cannot accept the offering
     */
    public function createForAcademicYear(
        Subject $subject,
        AcademicYear $academicYear,
        AcademicLevel $academicLevel,
        array $academicCycleSectionIds = [],
        RosterMode $rosterMode = RosterMode::HomeSection,
        array $studentRecordIds = [],
        ?int $plannedPeriodsPerWeek = null,
        ?int $capacity = null,
        ?User $actor = null,
    ): Collection {
        $periods = $academicYear->topLevelPeriods()
            ->ordered()
            ->get()
            ->filter(fn (AcademicPeriod $period): bool => $period->type->isPrimaryDivision() || $period->type === AcademicPeriodType::Other)
            ->values();

        if ($periods->isEmpty()) {
            throw new InvalidValueException('Add at least one term or semester to the academic year before creating this subject.');
        }

        return DB::transaction(function () use ($subject, $academicYear, $academicLevel, $periods, $academicCycleSectionIds, $rosterMode, $studentRecordIds, $plannedPeriodsPerWeek, $capacity, $actor): Collection {
            return $periods->map(fn (AcademicPeriod $period): CourseOffering => $this->create(
                $subject,
                $academicYear,
                $period,
                $academicLevel,
                $academicCycleSectionIds,
                $rosterMode,
                $studentRecordIds,
                $plannedPeriodsPerWeek,
                $capacity,
                $actor,
            ))->values();
        });
    }

    /**
     * @param  Collection<int, AcademicCycleSection>  $cycleSections
     * @param  array<int, int>  $academicCycleSectionIds
     * @param  Collection<int, StudentRecord>  $studentRecords
     * @param  array<int, int>  $studentRecordIds
     *
     * @throws InvalidValueException
     */
    private function failIfRecordsDoNotFit(
        Subject $subject,
        AcademicYear $academicYear,
        AcademicPeriod $academicPeriod,
        AcademicLevel $academicLevel,
        RosterMode $rosterMode,
        Collection $cycleSections,
        array $academicCycleSectionIds,
        Collection $studentRecords,
        array $studentRecordIds,
    ): void {
        if ($subject->school_id !== $academicYear->school_id || $academicPeriod->school_id !== $academicYear->school_id) {
            throw new InvalidValueException('The subject and academic period must belong to the academic year school.');
        }

        if ($academicPeriod->academic_year_id !== $academicYear->id) {
            throw new InvalidValueException('The academic period does not belong to the academic year.');
        }

        if ($academicLevel->school_id !== $academicYear->school_id) {
            throw new InvalidValueException('The academic level belongs to another school.');
        }

        $sectionLabel = strtolower(school_term('section', 'section'));
        $sectionsLabel = strtolower(school_terms('section', 'sections'));

        if ($cycleSections->count() !== count($academicCycleSectionIds)) {
            throw new InvalidValueException("Every selected {$sectionLabel} must belong to this level and academic cycle.");
        }

        if ($studentRecords->count() !== count($studentRecordIds)) {
            throw new InvalidValueException('Every named learner must actively attend this academic level in this school.');
        }

        if (
            !instructional_model($academicYear)->allowsRosterMode($rosterMode)
            && !$this->exceptions->allows($academicYear, $subject, $academicLevel, $rosterMode)
        ) {
            throw new InvalidValueException('The teaching setup does not allow this way of choosing learners for the academic cycle. Record an exception for this subject if it is taught differently on purpose.');
        }

        match ($rosterMode) {
            RosterMode::HomeSection => $this->requireRosterCount($academicCycleSectionIds, 1, "Select exactly one {$sectionLabel}."),
            RosterMode::CombinedHomeSections => $this->requireAtLeastRosterCount($academicCycleSectionIds, 2, "Select at least two {$sectionsLabel}."),
            RosterMode::AcademicLevel => $this->requireEmptyRoster($academicCycleSectionIds, $studentRecordIds, 'A whole-level offering does not select sections or individual learners.'),
            RosterMode::IndividualRoster => $this->requireIndividualRoster($academicCycleSectionIds, $studentRecordIds, $sectionsLabel),
        };

        if ($academicYear->isClosed() || $academicPeriod->isClosed()) {
            throw new InvalidValueException('Reopen the academic cycle before creating a course offering.');
        }
    }

    /**
     * @param  array<int, int>  $sectionIds
     *
     * @throws InvalidValueException
     */
    private function requireRosterCount(array $sectionIds, int $count, string $message): void
    {
        if (count($sectionIds) !== $count) {
            throw new InvalidValueException($message);
        }
    }

    /**
     * @param  array<int, int>  $sectionIds
     *
     * @throws InvalidValueException
     */
    private function requireAtLeastRosterCount(array $sectionIds, int $count, string $message): void
    {
        if (count($sectionIds) < $count) {
            throw new InvalidValueException($message);
        }
    }

    /**
     * @param  array<int, int>  $sectionIds
     * @param  array<int, int>  $studentRecordIds
     *
     * @throws InvalidValueException
     */
    private function requireEmptyRoster(array $sectionIds, array $studentRecordIds, string $message): void
    {
        if ($sectionIds !== [] || $studentRecordIds !== []) {
            throw new InvalidValueException($message);
        }
    }

    /**
     * @param  array<int, int>  $sectionIds
     * @param  array<int, int>  $studentRecordIds
     *
     * @throws InvalidValueException
     */
    private function requireIndividualRoster(array $sectionIds, array $studentRecordIds, string $sectionsLabel): void
    {
        if ($sectionIds !== [] || $studentRecordIds === []) {
            throw new InvalidValueException("Select one or more named learners and no {$sectionsLabel}.");
        }
    }
}
