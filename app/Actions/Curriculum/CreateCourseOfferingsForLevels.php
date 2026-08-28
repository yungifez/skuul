<?php

namespace App\Actions\Curriculum;

use App\Enums\RosterMode;
use App\Models\AcademicLevel;
use App\Models\AcademicPeriod;
use App\Models\AcademicYear;
use App\Models\CourseOffering;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class CreateCourseOfferingsForLevels
{
    public function __construct(private CreateCourseOffering $createCourseOffering) {}

    /**
     * Create one offering per selected level, using each level's own settings.
     *
     * @param  array<int, array{academic_level_id: int, roster_mode: string, academic_cycle_section_ids?: array<int, int>, planned_periods_per_week?: int|null, capacity?: int|null}>  $configurations
     * @return Collection<int, CourseOffering>
     */
    public function create(
        Subject $subject,
        AcademicYear $academicYear,
        string|int $academicPeriodId,
        array $configurations,
        ?User $actor = null,
    ): Collection {
        return DB::transaction(function () use ($subject, $academicYear, $academicPeriodId, $configurations, $actor): Collection {
            /** @var Collection<int, CourseOffering> $created */
            $created = new Collection;

            foreach ($configurations as $configuration) {
                $academicLevel = AcademicLevel::inSchool()->findOrFail($configuration['academic_level_id']);
                $rosterMode = RosterMode::from($configuration['roster_mode']);
                $sectionIds = $rosterMode->usesHomeSections()
                    ? ($configuration['academic_cycle_section_ids'] ?? [])
                    : [];
                $plannedPeriodsPerWeek = $configuration['planned_periods_per_week'] ?? null;
                $capacity = $configuration['capacity'] ?? null;

                if ($academicPeriodId === 'all') {
                    $created = $created->merge($this->createCourseOffering->createForAcademicYear(
                        $subject,
                        $academicYear,
                        $academicLevel,
                        $sectionIds,
                        $rosterMode,
                        [],
                        $plannedPeriodsPerWeek,
                        $capacity,
                        $actor,
                    ));

                    continue;
                }

                $created->push($this->createCourseOffering->create(
                    $subject,
                    $academicYear,
                    AcademicPeriod::inSchool()->findOrFail((int) $academicPeriodId),
                    $academicLevel,
                    $sectionIds,
                    $rosterMode,
                    [],
                    $plannedPeriodsPerWeek,
                    $capacity,
                    $actor,
                ));
            }

            return $created;
        });
    }
}
