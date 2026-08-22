<?php

namespace App\Actions\Curriculum;

use App\Actions\Audit\RecordAuditEvent;
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
    public function __construct(private RecordAuditEvent $auditor)
    {
    }

    /**
     * @param array<int, int> $academicCycleSectionIds
     * @param array<int, int> $studentRecordIds
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
                'school_id'                => $subject->school_id,
                'academic_year_id'         => $academicYear->id,
                'academic_period_id'       => $academicPeriod->id,
                'subject_id'               => $subject->id,
                'academic_level_id'        => $academicLevel->id,
                'roster_mode'              => $rosterMode,
                'planned_periods_per_week' => $plannedPeriodsPerWeek,
                'capacity'                 => $capacity,
            ]);

            $courseOffering->cycleSections()->sync($cycleSections->modelKeys());
            $courseOffering->studentRecords()->sync($studentRecords->modelKeys());

            $this->auditor->record(
                AuditAction::CourseOfferingCreated,
                $courseOffering,
                [
                    'subject_id'                 => $subject->id,
                    'academic_year_id'           => $academicYear->id,
                    'academic_period_id'         => $academicPeriod->id,
                    'academic_level_id'          => $academicLevel->id,
                    'roster_mode'                => $rosterMode->value,
                    'academic_cycle_section_ids' => $cycleSections->modelKeys(),
                    'student_record_ids'         => $studentRecords->modelKeys(),
                ],
                $actor,
            );

            return $courseOffering->load(['cycleSections', 'studentRecords']);
        });
    }

    /**
     * @param Collection<int, AcademicCycleSection> $cycleSections
     * @param array<int, int>                       $academicCycleSectionIds
     * @param Collection<int, StudentRecord>        $studentRecords
     * @param array<int, int>                       $studentRecordIds
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

        if ($cycleSections->count() !== count($academicCycleSectionIds)) {
            throw new InvalidValueException('Every selected home section must belong to this level and academic cycle.');
        }

        if ($studentRecords->count() !== count($studentRecordIds)) {
            throw new InvalidValueException('Every named learner must actively attend this academic level in this school.');
        }

        if (!instructional_model($academicYear)->allowsRosterMode($rosterMode)) {
            throw new InvalidValueException('The campus teaching setup does not allow this roster type for the academic cycle.');
        }

        match ($rosterMode) {
            RosterMode::HomeSection          => $this->requireRosterCount($academicCycleSectionIds, 1, 'Select exactly one home section.'),
            RosterMode::CombinedHomeSections => $this->requireAtLeastRosterCount($academicCycleSectionIds, 2, 'Select at least two home sections.'),
            RosterMode::AcademicLevel        => $this->requireEmptyRoster($academicCycleSectionIds, $studentRecordIds, 'A whole-level offering does not select sections or individual learners.'),
            RosterMode::IndividualRoster     => $this->requireIndividualRoster($academicCycleSectionIds, $studentRecordIds),
        };

        if ($academicYear->isClosed() || $academicPeriod->isClosed()) {
            throw new InvalidValueException('Reopen the academic cycle before creating a course offering.');
        }
    }

    /**
     * @param array<int, int> $sectionIds
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
     * @param array<int, int> $sectionIds
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
     * @param array<int, int> $sectionIds
     * @param array<int, int> $studentRecordIds
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
     * @param array<int, int> $sectionIds
     * @param array<int, int> $studentRecordIds
     *
     * @throws InvalidValueException
     */
    private function requireIndividualRoster(array $sectionIds, array $studentRecordIds): void
    {
        if ($sectionIds !== [] || $studentRecordIds === []) {
            throw new InvalidValueException('Select one or more named learners and no home sections.');
        }
    }
}
