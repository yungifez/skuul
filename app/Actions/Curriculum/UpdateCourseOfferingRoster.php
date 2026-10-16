<?php

namespace App\Actions\Curriculum;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AcademicStructureStatus;
use App\Enums\AuditAction;
use App\Enums\CourseOfferingStatus;
use App\Enums\RosterMode;
use App\Exceptions\InvalidValueException;
use App\Models\AcademicCycleSection;
use App\Models\AcademicLevel;
use App\Models\CourseOffering;
use App\Models\StudentRecord;
use App\Models\User;
use App\Services\Curriculum\OfferingExceptions;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class UpdateCourseOfferingRoster
{
    public function __construct(
        private RecordAuditEvent $auditor,
        private OfferingExceptions $exceptions,
    ) {}

    /**
     * @param  array<int, int>  $academicCycleSectionIds
     * @param  array<int, int>  $studentRecordIds
     */
    public function update(
        CourseOffering $courseOffering,
        RosterMode $rosterMode,
        array $academicCycleSectionIds = [],
        array $studentRecordIds = [],
        ?int $academicLevelId = null,
        ?User $actor = null,
    ): CourseOffering {
        $academicCycleSectionIds = array_values(array_unique(array_map('intval', $academicCycleSectionIds)));
        $studentRecordIds = array_values(array_unique(array_map('intval', $studentRecordIds)));

        return DB::transaction(function () use ($courseOffering, $rosterMode, $academicCycleSectionIds, $studentRecordIds, $academicLevelId, $actor): CourseOffering {
            $courseOffering = CourseOffering::query()
                ->with(['academicLevel', 'academicPeriod', 'academicYear', 'subject', 'cycleSections', 'studentRecords'])
                ->lockForUpdate()
                ->findOrFail($courseOffering->id);

            if ($courseOffering->status === CourseOfferingStatus::Archived) {
                throw new InvalidValueException('An archived course offering cannot be changed.');
            }

            if ($courseOffering->academicLevel->is_group && $rosterMode !== RosterMode::AcademicLevel) {
                throw new InvalidValueException('A level group can only use a whole-level roster.');
            }

            if ($courseOffering->academicYear->isClosed() || $courseOffering->academicPeriod->isClosed()) {
                throw new InvalidValueException('Reopen the academic cycle before changing this course offering.');
            }

            $cycleSections = AcademicCycleSection::inSchool()
                ->whereKey($academicCycleSectionIds)
                ->where('academic_year_id', $courseOffering->academic_year_id)
                ->where('academic_level_id', $courseOffering->academic_level_id)
                ->where('status', '!=', AcademicStructureStatus::Archived)
                ->get();
            $studentRecords = StudentRecord::inSchool()
                ->attending()
                ->with('academicCycleSection')
                ->whereKey($studentRecordIds)
                ->whereHas('academicCycleSection', function ($query) use ($courseOffering): void {
                    $query->where('academic_year_id', $courseOffering->academic_year_id)
                        ->where('academic_level_id', $courseOffering->academic_level_id);
                })
                ->get();
            $academicLevel = $this->resolveAcademicLevel($courseOffering, $rosterMode, $academicLevelId, $cycleSections, $studentRecords);

            if ($cycleSections->count() !== count($academicCycleSectionIds)) {
                throw new InvalidValueException('Every selected section must belong to this class and school year.');
            }

            if ($studentRecords->count() !== count($studentRecordIds)) {
                throw new InvalidValueException('Every named learner must actively attend this class in this school.');
            }

            if (
                !instructional_model($courseOffering->academicYear)->allowsRosterMode($rosterMode)
                && !$this->exceptions->allows($courseOffering->academicYear, $courseOffering->subject, $academicLevel, $rosterMode)
            ) {
                throw new InvalidValueException('The teaching setup does not allow this way of choosing learners for the academic cycle.');
            }

            $this->validateRoster($rosterMode, $academicCycleSectionIds, $studentRecordIds, $courseOffering->status);

            $activeKey = null;

            if ($courseOffering->status === CourseOfferingStatus::Active) {
                $activeKey = $courseOffering->activeKeyForRoster($academicCycleSectionIds, $studentRecordIds);

                if (CourseOffering::query()->where('active_key', $activeKey)->whereKeyNot($courseOffering->id)->exists()) {
                    throw new InvalidValueException('An active offering already exists for this subject, period, class, and roster.');
                }
            }

            $courseOffering->roster_mode = $rosterMode;
            $courseOffering->active_key = $activeKey;
            $courseOffering->save();
            $courseOffering->cycleSections()->sync($cycleSections->modelKeys());
            $courseOffering->studentRecords()->sync($studentRecords->modelKeys());

            $this->auditor->record(
                AuditAction::CourseOfferingRosterUpdated,
                $courseOffering,
                [
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
     * @param  Collection<int, AcademicCycleSection>  $cycleSections
     * @param  Collection<int, StudentRecord>  $studentRecords
     */
    private function resolveAcademicLevel(
        CourseOffering $courseOffering,
        RosterMode $rosterMode,
        ?int $academicLevelId,
        Collection $cycleSections,
        Collection $studentRecords,
    ): AcademicLevel {
        $academicLevel = AcademicLevel::inSchool()->find($academicLevelId ?: $courseOffering->academic_level_id);

        if (!$academicLevel instanceof AcademicLevel) {
            throw new InvalidValueException('Select a class level.');
        }

        if ($rosterMode === RosterMode::AcademicLevel && $academicLevel->id !== $courseOffering->academic_level_id) {
            throw new InvalidValueException('The class level of an existing offering cannot be changed.');
        }

        if ($rosterMode->usesHomeSections() && $cycleSections->contains(fn (AcademicCycleSection $section): bool => $section->academic_level_id !== $academicLevel->id)) {
            throw new InvalidValueException('Select sections from this class level only.');
        }

        if ($rosterMode === RosterMode::IndividualRoster && $studentRecords->contains(fn (StudentRecord $student): bool => $student->academicCycleSection?->academic_level_id !== $academicLevel->id)) {
            throw new InvalidValueException('Select learners from this class level only.');
        }

        return $academicLevel;
    }

    /**
     * @param  array<int, int>  $sectionIds
     * @param  array<int, int>  $studentRecordIds
     */
    private function validateRoster(RosterMode $rosterMode, array $sectionIds, array $studentRecordIds, CourseOfferingStatus $status): void
    {
        match ($rosterMode) {
            RosterMode::HomeSection => $this->requireCount($sectionIds, 1, 'Select exactly one section.'),
            RosterMode::CombinedHomeSections => $this->requireAtLeast($sectionIds, 2, 'Select at least two sections.'),
            RosterMode::AcademicLevel => $this->requireEmpty($sectionIds, $studentRecordIds, 'A whole-level offering does not select sections or individual learners.'),
            RosterMode::IndividualRoster => $this->requireIndividual($sectionIds, $studentRecordIds, $status),
        };
    }

    /**
     * @param  array<int, int>  $values
     */
    private function requireCount(array $values, int $count, string $message): void
    {
        if (count($values) !== $count) {
            throw new InvalidValueException($message);
        }
    }

    /**
     * @param  array<int, int>  $values
     */
    private function requireAtLeast(array $values, int $count, string $message): void
    {
        if (count($values) < $count) {
            throw new InvalidValueException($message);
        }
    }

    /**
     * @param  array<int, int>  $sectionIds
     * @param  array<int, int>  $studentRecordIds
     */
    private function requireEmpty(array $sectionIds, array $studentRecordIds, string $message): void
    {
        if ($sectionIds !== [] || $studentRecordIds !== []) {
            throw new InvalidValueException($message);
        }
    }

    /**
     * @param  array<int, int>  $sectionIds
     * @param  array<int, int>  $studentRecordIds
     */
    private function requireIndividual(array $sectionIds, array $studentRecordIds, CourseOfferingStatus $status): void
    {
        if ($sectionIds !== []) {
            throw new InvalidValueException('A named-learner roster does not select sections.');
        }

        if ($status === CourseOfferingStatus::Active && $studentRecordIds === []) {
            throw new InvalidValueException('Add at least one named learner before changing an active offering.');
        }
    }
}
