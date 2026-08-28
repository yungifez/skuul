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

class CreateCourseOfferingsForSections
{
    public function __construct(private CreateCourseOffering $createCourseOffering) {}

    /**
     * Create one independent home-section offering for each selected section.
     *
     * @param  array<int, int>  $academicCycleSectionIds
     * @return Collection<int, CourseOffering>
     */
    public function create(
        Subject $subject,
        AcademicYear $academicYear,
        string|int $academicPeriodId,
        AcademicLevel $academicLevel,
        array $academicCycleSectionIds,
        ?int $plannedPeriodsPerWeek = null,
        ?int $capacity = null,
        ?User $actor = null,
    ): Collection {
        return DB::transaction(function () use ($subject, $academicYear, $academicPeriodId, $academicLevel, $academicCycleSectionIds, $plannedPeriodsPerWeek, $capacity, $actor): Collection {
            /** @var Collection<int, CourseOffering> $created */
            $created = new Collection;

            foreach ($academicCycleSectionIds as $academicCycleSectionId) {
                if ($academicPeriodId === 'all') {
                    $created = $created->merge($this->createCourseOffering->createForAcademicYear(
                        $subject,
                        $academicYear,
                        $academicLevel,
                        [$academicCycleSectionId],
                        RosterMode::HomeSection,
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
                    [$academicCycleSectionId],
                    RosterMode::HomeSection,
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
